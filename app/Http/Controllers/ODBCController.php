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
        $this->data['odbc'] = $this->getOdbcData();

        return view('odbc.index', $this->data);
    }

    /**
     * Used to return index of specific resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function allFiles()
    {
        return view('all-files.index');
    }

    /**
     * Used to get all uploaded files data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function odbcAllData(Request $request)
    {
        $data = $request->all();
        if (!empty($data['search'])) {
            $searchData = $data['search'];
            $active = ActiveProductListing::where(function ($query) use ($searchData) {
                $query->where('product_no', 'rlike', $searchData)
                    ->orWhere('ndc', 'rlike', $searchData)
                    ->orWhere('name', 'rlike', $searchData)
                    ->orWhere('form', 'rlike', $searchData)
                    ->orWhere('strength', 'rlike', $searchData)
                    ->orWhere('count', 'rlike', $searchData)
                    ->orWhere('vendor', 'rlike', $searchData)
                    ->orWhere('list_price', 'rlike', $searchData);
            });
            if (!empty($data['is_match'])) {
                $this->data = $active->where('count', '!=', '')->get();
            } else {
                $this->data = $active->where('count', '=', '')->get();
            }
        } else {
            if (!empty($data['is_match'])) {
                $this->data = ActiveProductListing::where('count', '!=', '')->get();
            } else {
                $this->data = ActiveProductListing::where('count', '=', '')->get();
            }
        }

        return response()->json(['data' => $this->data]);
    }

    /**
     * Used to get all uploaded files data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cardinalData(Request $request)
    {
        $data = $request->all();
        if (!empty($data['search'])) {
            $searchData = $data['search'];
            $cardinal = CardinalHealth::where(function ($query) use ($searchData) {
                $query->where('cin_ndc_upc1', 'rlike', $searchData)
                    ->orWhere('fda_name', 'rlike', $searchData)
                    ->orWhere('fda_form', 'rlike', $searchData)
                    ->orWhere('fda_strength', 'rlike', $searchData)
                    ->orWhere('fda_count', 'rlike', $searchData)
                    ->orWhere('trade_name_mfr2', 'rlike', $searchData)
                    ->orWhere('invoice_cost', 'rlike', $searchData);
            });
            if (!empty($data['is_match'])) {
                $this->data = $cardinal->where('fda_count', '!=', '')->get();
            } else {
                $this->data = $cardinal->where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        } else {
            if (!empty($data['is_match'])) {
                $this->data = CardinalHealth::where('fda_count', '!=', '')->get();
            } else {
                $this->data = CardinalHealth::where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        }

        return response()->json(['data' => $this->data]);
    }

    /**
     * Used to get all uploaded files data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ezirxData(Request $request)
    {
        $data = $request->all();
        if (!empty($data['search'])) {
            $searchData = $data['search'];
            $export = ExportAllProduct::where(function ($query) use ($searchData) {
                $query->where('fda_name', 'rlike', $searchData)
                    ->orWhere('fda_form', 'rlike', $searchData)
                    ->orWhere('fda_strength', 'rlike', $searchData)
                    ->orWhere('fda_count', 'rlike', $searchData)
                    ->orWhere('ndc', 'rlike', $searchData)
                    ->orWhere('vendor', 'rlike', $searchData)
                    ->orWhere('price', 'rlike', $searchData);
            });
            if (!empty($data['is_match'])) {
                $this->data = $export->where('fda_count', '!=', '')->get();
            } else {
                $this->data = $export->where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        } else {
            if (!empty($data['is_match'])) {
                $this->data = ExportAllProduct::where('fda_count', '!=', '')->get();
            } else {
                $this->data = ExportAllProduct::where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        }

        return response()->json(['data' => $this->data]);
    }

    /**
     * Used to get all uploaded files data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function txradeData(Request $request)
    {
        $data = $request->all();
        if (!empty($data['search'])) {
            $searchData = $data['search'];
            $trending = TrendingProduct::where(function ($query) use ($searchData) {
                $query->where('fda_name', 'rlike', $searchData)
                    ->orWhere('ndc', 'rlike', $searchData)
                    ->orWhere('fda_strength', 'rlike', $searchData)
                    ->orWhere('fda_form', 'rlike', $searchData)
                    ->orWhere('fda_count', 'rlike', $searchData)
                    ->orWhere('mfr', 'rlike', $searchData)
                    ->orWhere('best_price_today', 'rlike', $searchData);
            });
            if (!empty($data['is_match'])) {
                $this->data = $trending->where('fda_count', '!=', '')->get();
            } else {
                $this->data = $trending->where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        } else {
            if (!empty($data['is_match'])) {
                $this->data = TrendingProduct::where('fda_name', '!=', '')->get();
            } else {
                $this->data = TrendingProduct::where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        }

        return response()->json(['data' => $this->data]);
    }

    /**
     * Used to get all uploaded files data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function auburnData(Request $request)
    {
        $data = $request->all();
        if (!empty($data['search'])) {
            $searchData = $data['search'];
            $auburn = AuburnPharmaceutical::where(function ($query) use ($searchData) {
                $query->where('fda_name', 'rlike', $searchData)
                    ->orWhere('fda_strength', 'rlike', $searchData)
                    ->orWhere('fda_form', 'rlike', $searchData)
                    ->orWhere('fda_count', 'rlike', $searchData)
                    ->orWhere('ndc', 'rlike', $searchData)
                    ->orWhere('vendor', 'rlike', $searchData)
                    ->orWhere('price', 'rlike', $searchData);
            });
            if (!empty($data['is_match'])) {
                $this->data = $auburn->where('fda_name', '!=', '')->get();
            } else {
                $this->data = $auburn->where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        } else {
            if (!empty($data['is_match'])) {
                $this->data = AuburnPharmaceutical::where('fda_name', '!=', '')->get();
            } else {
                $this->data = AuburnPharmaceutical::where('fda_name', '=', '')->where('fda_count', '=', '')->get();
            }
        }

        return response()->json(['data' => $this->data]);
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
        $this->data['odbc'] = $this->getOdbcData($data['search']);

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
        $arrayData = $this->getOdbcData('', 1);
        $response = Excel::download(new ODBCExport($arrayData), 'ODBC.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();

        return $response;
    }

    /**
     * This is used to get odbc data
     *
     * @param string $search
     * @param int $isExport
     * @return array
     */
    private function getOdbcData($search = '', $isExport = 0)
    {
        $activeData = ActiveProductListing::where('desc_one', '!=', '');
        if (!empty($search)) {
            $searchData = $search;
            $activeData->where(function ($query) use ($searchData) {
                $query->where('product_no', 'rlike', $searchData)
                    ->orWhere('ndc', 'rlike', $searchData)
                    ->orWhere('name', 'rlike', $searchData)
                    ->orWhere('strength', 'rlike', $searchData)
                    ->orWhere('form', 'rlike', $searchData)
                    ->orWhere('count', 'rlike', $searchData);
            });
        }
        $data = $activeData->get();
        $arr = [];
        if (!empty($data)) {
            foreach ($data as $key => $row) {
                if (!empty($row->name)) {
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
        }

        return $arr;
    }
}
