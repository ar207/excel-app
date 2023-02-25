<?php

namespace App\Http\Controllers;

use App\Exports\ODBCExport;
use App\Models\ActiveProductListing;
use App\Models\AuburnPharmaceutical;
use App\Models\CardinalHealth;
use App\Models\ExportAllProduct;
use App\Models\TrendingProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ODBCController extends Controller
{
    private $data = [];

    /**
     * Used to return index of specific resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('odbc.index');
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
        $arr = $this->getOdbcData();

        $this->data['odbc'] = paginateArrayData($arr, $data['per_page'], $data['page']);
        $this->data['pager'] = make_complete_pagination_block($this->data['odbc'], count($arr));

        return response()->json(['data' => $this->data]);
    }

    /**
     * This is used to export data to excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel()
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        $arrayData = $this->getOdbcData(1);
        $response = Excel::download(new ODBCExport($arrayData), 'ODBC.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();

        return $response;
    }

    /**
     * This is used to get odbc data
     *
     * @param int $isExport
     * @return array
     */
    private function getOdbcData($isExport = 0)
    {
        $activeData = ActiveProductListing::where('name', '!=', '')->get();
        $arr = [];
        if (!empty($activeData)) {
            foreach ($activeData as $key => $row) {
                $search = '%' . $row->name . '%';
                $minCardinal = CardinalHealth::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->min('invoice_cost');
                $minExport = ExportAllProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->min("price");
                $minTrending = TrendingProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->min("best_price_today");
                $minAuburn = AuburnPharmaceutical::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->min("price");
                $cardinal = $export = $trending = $auburn = [];
                if (!empty($minCardinal)) {
                    $cardinal = CardinalHealth::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->first();
                    $cardinalArray = CardinalHealth::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->toArray();
                }
                if (!empty($minExport)) {
                    $export = ExportAllProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->first();
                    $exportArray = ExportAllProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->toArray();
                }
                if (!empty($minTrending)) {
                    $trending = TrendingProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->first();
                    $trendingArray = TrendingProduct::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->toArray();
                }
                if (!empty($minAuburn)) {
                    $auburn = AuburnPharmaceutical::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->first();
                    $auburnArray = AuburnPharmaceutical::where('fda_name', $row->name)->where('fda_strength', $row->strength)->where('fda_form', $row->form)->where('fda_count', $row->count)->get()->toArray();
                }
                $arr[$key]['product_no'] = $row->product_no;
                $arr[$key]['ndc'] = $row->fda_ndc;
                $arr[$key]['name'] = $row->name;
                $arr[$key]['strength'] = $row->strength;
                $arr[$key]['form'] = $row->form;
                $arr[$key]['count'] = $row->count;
                $arr[$key]['gpw_price'] = !empty($row->list_price) ? '$' . str_replace('$', '', $row->list_price) : '-';
                $arr[$key]['cardinal_price'] = !empty($minCardinal) ? '$' . str_replace('$', '', $minCardinal) : '-';
                $arr[$key]['export_price'] = !empty($minExport) ? '$' . str_replace('$', '', $minExport) : '-';
                $arr[$key]['trending_price'] = !empty($minTrending) ? '$' . str_replace('$', '', $minTrending) : '-';
                $arr[$key]['auburn_price'] = !empty($minAuburn) ? '$' . str_replace('$', '', $minAuburn) : '-';
                if (empty($isExport)) {
                    $arr[$key]['cardinal_data'] = !empty($cardinal) ? $cardinal->toArray() : [];
                    $arr[$key]['cardinal_full'] = !empty($cardinalArray) ? $cardinalArray : [];
                    $arr[$key]['export_data'] = !empty($export) ? $export->toArray() : [];
                    $arr[$key]['export_full'] = !empty($exportArray) ? $exportArray : [];
                    $arr[$key]['trending_data'] = !empty($trending) ? $trending->toArray() : [];
                    $arr[$key]['trending_full'] = !empty($trendingArray) ? $trendingArray : [];
                    $arr[$key]['auburn_data'] = !empty($auburn) ? $auburn->toArray() : [];
                    $arr[$key]['auburn_full'] = !empty($auburnArray) ? $auburnArray : [];
                }
            }
        }

        return $arr;
    }
}
