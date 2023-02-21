<?php

namespace App\Http\Controllers;

use App\Imports\PackageImport;
use App\Imports\ProductImport;
use App\Models\FDA;
use App\Models\Package;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $fda = FDA::get();

        $this->data['fda'] = paginateArrayData($fda, $data['per_page'], $data['page']);
        $this->data['pager'] = make_complete_pagination_block($this->data['fda'], count($fda));

        return response()->json(['data' => $this->data]);
    }
}
