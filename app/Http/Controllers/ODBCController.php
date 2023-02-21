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
        $arrayData = $this->getOdbcData();
        $response = Excel::download(new ODBCExport($arrayData), 'ODBC.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();

        return $response;
    }

    /**
     * This is used to get odbc data
     *
     * @return array
     */
    private function getOdbcData()
    {
        $activeData = ActiveProductListing::where('name', '!=', '')->get();
        $arr = [];
        if (!empty($activeData)) {
            foreach ($activeData as $key => $row) {
                $search = '%' . $row->name . '%';
                $minCardinal = CardinalHealth::select('invoice_cost', 'trade_name_mfr')->where('trade_name_mfr', 'like', $search)->whereNotNull('invoice_cost')->min("invoice_cost");
                $minExport = ExportAllProduct::select('price', 'name')->where('name', 'like', $search)->whereNotNull('price')->min("price");
                $minTrending = TrendingProduct::select('best_price_today', 'product_name')->where('product_name', 'like', $search)->whereNotNull('best_price_today')->min("best_price_today");
                $minAuburn = AuburnPharmaceutical::select('price', 'description')->where('description', 'like', $search)->whereNotNull('price')->min("price");
                $arr[$key]['ndc'] = $row->fda_ndc;
                $arr[$key]['name'] = $row->name;
                $arr[$key]['gpw_price'] = !empty($row->list_price) ? '$' . str_replace('$', '', $row->list_price) : '-';
                $arr[$key]['cardinal_price'] = !empty($minCardinal) ? '$' . str_replace('$', '', $minCardinal) : '-';
                $arr[$key]['export_price'] = !empty($minExport) ? '$' . str_replace('$', '', $minExport) : '-';
                $arr[$key]['trending_price'] = !empty($minTrending) ? '$' . str_replace('$', '', $minTrending) : '-';
                $arr[$key]['auburn_price'] = !empty($minAuburn) ? '$' . str_replace('$', '', $minAuburn) : '-';
            }
        }

        return $arr;
    }
}
