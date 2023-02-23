<?php

namespace App\Http\Controllers;

use App\Imports\PackageImport;
use App\Imports\ProductImport;
use App\Models\FDA;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductPackageCombination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use function Ramsey\Collection\Map\toArray;

class FdaFileController extends Controller
{
    private $data = [];
    private $message = '';
    private $success = false;

    /**
     * Used to return index of specific resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('fda-file.index');
    }

    /**
     * Used to return index of specific resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fda()
    {
        return view('fda.index');
    }

    /**
     * Used to store the specified resource data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $this->message = 'Something went wrong';
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
//        $products = Product::get()->toArray();
//        $combination = [];
//        $count = 1;
//        if (!empty($products)) {
//            ProductPackageCombination::truncate();
//            foreach (array_chunk($products, 500) as $ind => $data) {
//                foreach ($data as $product) {
//                    $package = Package::where('product_id', $product['product_id'])->get()->toArray();
//                    foreach ($package as $key => $row) {
//                        $combination[$count]['product_table_id'] = $product['id'];
//                        $combination[$count]['package_table_id'] = $row['id'];
//                        $combination[$count]['name'] = $product['name'];
//                        $combination[$count]['strength'] = $product['strength'];
//                        $combination[$count]['unit'] = $product['unit'];
//                        $combination[$count]['labeler_name'] = $product['labeler_name'];
//                        $combination[$count]['brand_name'] = $product['brand_name'];
//                        $combination[$count]['dosage_form'] = $product['dosage_form'];
//                        $combination[$count]['ndc'] = ndcCorrection($row['ndc_code']);
//                        $combination[$count]['ndc_match'] = str_replace('-', '', ndcCorrection($row['ndc_code']));
//                        $combination[$count]['count'] = $row['description'];
//                        $count++;
//                    }
//                }
//            }
//            foreach (array_chunk($combination, 500) as $ind) {
//                ProductPackageCombination::insert($ind);
//            }
//        }
        if (!empty($data)) {
            if (!empty($data['product'])) {
                Product::truncate();
                Excel::import(new ProductImport(), $request->file('product'));
            }
            if (!empty($data['package'])) {
                Package::truncate();
                Excel::import(new PackageImport(), $request->file('package'));
            }
            if (!empty($data['fda_ndc'])) {
                $this->uploadFdaFile($request);
            }

            $this->success = true;
            $this->message = 'Files uploaded successfully';
        }

        return response()->json(['success' => $this->success, 'message' => $this->message]);
    }

    /**
     * This is used to upload fda file
     *
     * @param $request
     */
    private function uploadFdaFile($request)
    {
        $rows = Excel::toArray(new FDA(), $request->file('fda_ndc'));
        $arr = [];
        $count = 0;
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[0])) {
                $arr[$count]['ndc'] = isset($row[0]) ? $row[0] : '';
                $arr[$count]['ndc_match'] = isset($row[0]) ? str_replace('-', '', $row[0]) : '';
                $arr[$count]['name'] = isset($row[1]) ? $row[1] : '';
                $arr[$count]['strength'] = isset($row[2]) ? $row[2] : '';
                $arr[$count]['form'] = isset($row[3]) ? $row[3] : '';
                $arr[$count]['count'] = isset($row[5]) ? $row[5] : '';
                $arr[$count]['created_at'] = Carbon::now();
                $arr[$count]['updated_at'] = Carbon::now();
            }
            $count++;
        }
        if (!empty($arr)) {
            unset($arr[0]);
        }
        FDA::truncate();
        if (!empty($arr)) {
            foreach (array_chunk($arr, 500) as $t) {
                FDA::insert($t);
            }
        }
    }

    /**
     * This is used to get odbc data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $data = $request->all();
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        $fda = ProductPackageCombination::get();

        $this->data['fda'] = paginateArrayData($fda, $data['per_page'], $data['page']);
        $this->data['pager'] = make_complete_pagination_block($this->data['fda'], count($fda));

        return response()->json(['data' => $this->data]);
    }
}
