<?php

namespace App\Http\Controllers;

use App\Models\ActiveProductListing;
use App\Models\AuburnPharmaceutical;
use App\Models\CardinalHealth;
use App\Models\ExportAllProduct;
use App\Models\TrendingProduct;
use Illuminate\Http\Request;

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
        $activeData = ActiveProductListing::where('name', '!=', '')->get()->toArray();
        $arr = [];
        if (!empty($activeData)) {
            foreach ($activeData as $key => $row) {
                $search = '%' . $row['name'] . '%';
                $cardinalData = CardinalHealth::select('invoice_cost', 'trade_name_mfr')->where('trade_name_mfr', 'like', $search)->first();
                $exportData = ExportAllProduct::select('price', 'name')->where('name', 'like', $search)->first();
                $topTrending = TrendingProduct::select('best_price_today', 'product_name')->where('product_name', 'like', $search)->first();
                $auburn = AuburnPharmaceutical::select('price', 'description')->where('description', 'like', $search)->first();
                $arr[$key]['name'] = $row['name'];
                $arr[$key]['list_price'] = $row['list_price'];
                $arr[$key]['cardinal_price'] = !empty($cardinalData) ? str_replace('$', '', $cardinalData->invoice_cost) : '';
                $arr[$key]['export_price'] = !empty($exportData) ? str_replace('$', '', $exportData->price) : '';
                $arr[$key]['trending_price'] = !empty($topTrending) ? str_replace('$', '', $topTrending->best_price_today) : '';
                $arr[$key]['auburn_price'] = !empty($auburn) ? str_replace('$', '', $auburn->price) : '';
            }
        }

        $this->data['odbc'] = paginateArrayData($arr, $data['per_page'], $data['page']);
        $this->data['pager'] = make_complete_pagination_block($this->data['odbc'], count($arr));

        return response()->json(['data' => $this->data]);
    }
}
