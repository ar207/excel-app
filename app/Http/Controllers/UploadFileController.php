<?php

namespace App\Http\Controllers;

use App\Imports\CardinalImport;
use App\Models\ActiveProductListing;
use App\Models\AuburnPharmaceutical;
use App\Models\CardinalHealth;
use App\Models\ExportAllProduct;
use App\Models\FileCategory;
use App\Models\TrendingProduct;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_Request;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UploadFileController extends Controller
{
    private $data = [];
    private $message = '';
    private $success = false;
    private $client, $service, $documentId, $range;
    private $activeProductCount, $cardinalCount, $exportCount, $topTrendingCount, $auburnCount;

    /**
     * This is used to upload file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Google\Exception
     */
    public function uploadFile(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $this->message = 'Something went wrong';
        if (!empty($data)) {
            ActiveProductListing::truncate();
            CardinalHealth::truncate();
            ExportAllProduct::truncate();
            TrendingProduct::truncate();
            AuburnPharmaceutical::truncate();
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '-1');
            if (!empty($data['active_product_listing'])) {
                $this->activeProductListing($request);
            }
            if (!empty($data['cardinal_health'])) {
                Excel::import(new CardinalImport(), $request->file('cardinal_health'));
            }
            if (!empty($data['export_all'])) {
                $this->exportAll($request);
            }
            if (!empty($data['top_trending'])) {
                $this->topTrending($request);
            }
            if (!empty($data['auburn_pharmaceutical'])) {
                $this->auburnPharmaceutical($request);
            }
            $this->prepareDataForGoogleSheet();
            $this->success = true;
            $this->message = 'Files uploaded successfully';
        }

        return response()->json(['success' => $this->success, 'message' => $this->message]);
    }

    /**
     * This is used to upload active product listing
     *
     * @param $request
     */
    public function activeProductListing($request)
    {
        $rows = Excel::toArray(new FileCategory(), $request->file('active_product_listing'));
        $arrProductListing = [];
        $count = 0;
        foreach (array_chunk($rows[0], 1) as $t) {
            foreach ($t as $key => $row) {
                if (!empty($row[1])) {
                    $arrProductListing[$count]['desc_one'] = isset($row[1]) ? $row[1] : '';
                    $arrProductListing[$count]['vendor'] = isset($row[5]) ? $row[5] : '';
                    $arrProductListing[$count]['ndc'] = isset($row[6]) ? $row[6] : '';
                    $arrProductListing[$count]['list_price'] = isset($row[4]) ? $row[4] : '';
                    $arrProductListing[$count]['gpw'] = 'Gpw';
                }
            }
            $count++;
        }
        if (!empty($arrProductListing)) {
            unset($arrProductListing[0], $arrProductListing[1]);
        }
        ActiveProductListing::truncate();
        if (!empty($arrProductListing)) {
            ActiveProductListing::insert($arrProductListing);
        }
    }

    /**
     * This is used to upload cardinal health file
     *
     * @param $request
     */
    public function cardinalHealth($request)
    {
        $rows = Excel::toArray(new FileCategory(), $request->file('cardinal_health'));
        $arrProductListing = [];
        $count = 0;
        foreach (array_chunk($rows[0], 1) as $t) {
            foreach ($t as $key => $row) {
                if (!empty($row[1])) {
                    $arrProductListing[$count]['cin_ndc_upc'] = isset($row[0]) ? $row[0] : '';
                    $arrProductListing[$count]['cin_ndc_upc1'] = isset($row[1]) ? $row[1] : '';
                    $arrProductListing[$count]['trade_name_mfr'] = isset($row[2]) ? $row[2] : '';
                    $arrProductListing[$count]['trade_name_mfr2'] = isset($row[3]) ? $row[3] : '';
                    $arrProductListing[$count]['strength'] = isset($row[4]) ? $row[4] : '';
                    $arrProductListing[$count]['from'] = isset($row[5]) ? $row[5] : '';
                    $arrProductListing[$count]['size'] = isset($row[6]) ? $row[6] : '';
                    $arrProductListing[$count]['type'] = isset($row[7]) ? $row[7] : '';
                    $arrProductListing[$count]['net_cost'] = isset($row[8]) ? $row[8] : '';
                    $arrProductListing[$count]['invoice_cost'] = isset($row[9]) ? $row[9] : '';
                    $arrProductListing[$count]['cardinal'] = 'Cardinal';
                }
            }
            $count++;
        }

        if (!empty($arrProductListing)) {
            unset($arrProductListing[0]);
        }
        CardinalHealth::truncate();
        foreach (array_chunk($arrProductListing, 1) as $ind) {
            CardinalHealth::insert($ind);
        }
    }

    /**
     * This is used to upload export all file
     *
     * @param $request
     */
    public function exportAll($request)
    {
        $rows = Excel::toArray(new FileCategory(), $request->file('export_all'));
        $arrProductListing = [];
        $count = 0;
        foreach (array_chunk($rows[0], 1) as $t) {
            foreach ($t as $key => $row) {
                if (!empty($row[1])) {
                    $arrProductListing[$count]['name'] = isset($row[1]) ? $row[1] : '';
                    $arrProductListing[$count]['vendor'] = '';
                    $arrProductListing[$count]['ndc'] = isset($row[0]) ? $row[0] : '';
                    $arrProductListing[$count]['price'] = isset($row[7]) ? $row[7] : '';
                    $arrProductListing[$count]['wholesaler'] = 'EzriRx';
                }
            }
            $count++;
        }
        if (!empty($arrProductListing)) {
            unset($arrProductListing[0]);
        }
        ExportAllProduct::truncate();
        foreach (array_chunk($arrProductListing, 1) as $ind) {
            ExportAllProduct::insert($ind);
        }
    }

    /**
     * This is used to upload top trending file data
     *
     * @param $request
     */
    public function topTrending($request)
    {
        $rows = Excel::toArray(new FileCategory(), $request->file('top_trending'));
        $arrProductListing = [];
        $count = 0;
        foreach (array_chunk($rows[0], 1) as $t) {
            foreach ($t as $key => $row) {
                if (!empty($row[1])) {
                    $arrProductListing[$count]['ndc'] = isset($row[1]) ? $row[1] : '';
                    $arrProductListing[$count]['product_name'] = isset($row[2]) ? $row[2] : '';
                    $arrProductListing[$count]['strength'] = isset($row[3]) ? $row[3] : '';
                    $arrProductListing[$count]['package_size'] = isset($row[4]) ? $row[4] : '';
                    $arrProductListing[$count]['from'] = isset($row[5]) ? $row[5] : '';
                    $arrProductListing[$count]['mfr'] = isset($row[6]) ? $row[6] : '';
                    $arrProductListing[$count]['type'] = isset($row[7]) ? $row[7] : '';
                    $arrProductListing[$count]['low_sold_price'] = isset($row[8]) ? $row[8] : '';
                    $arrProductListing[$count]['avg_sold_price'] = isset($row[9]) ? $row[9] : '';
                    $arrProductListing[$count]['high_sold_price'] = isset($row[10]) ? $row[10] : '';
                    $arrProductListing[$count]['best_price_today'] = isset($row[11]) ? $row[11] : '';
                    $arrProductListing[$count]['trxade'] = 'Trxade';
                }
            }
            $count++;
        }
        if (!empty($arrProductListing)) {
            unset($arrProductListing[0]);
        }
        TrendingProduct::truncate();
        foreach (array_chunk($arrProductListing, 1) as $ind) {
            TrendingProduct::insert($ind);
        }
    }

    /**
     * This is used to upload auburn pharmaceutical file data
     *
     * @param $request
     */
    public function auburnPharmaceutical($request)
    {
        $rows = Excel::toArray(new FileCategory(), $request->file('auburn_pharmaceutical'));
        $arrProductListing = [];
        $count = 0;
        foreach (array_chunk($rows[0], 1) as $t) {
            foreach ($t as $key => $row) {
                if (!empty($row[9])) {
                    $arrProductListing[$count]['description'] = isset($row[9]) ? $row[9] : '';
                    $arrProductListing[$count]['vendor'] = isset($row[11]) ? $row[11] : '';
                    $arrProductListing[$count]['ndc'] = isset($row[3]) ? $row[3] : '';
                    $arrProductListing[$count]['price'] = isset($row[26]) ? $row[26] : '';
                    $arrProductListing[$count]['wholesaler'] = 'Auburn';
                }
            }
            $count++;
        }

        if (!empty($arrProductListing)) {
            unset($arrProductListing[0]);
        }
        AuburnPharmaceutical::truncate();
        foreach (array_chunk($arrProductListing, 1) as $ind) {
            AuburnPharmaceutical::insert($ind);
        }
    }

    /**
     * @throws \Google\Exception
     */
    public function prepareDataForGoogleSheet()
    {
        $activeProducts = ActiveProductListing::get()->toArray();
        $cardinalHealth = CardinalHealth::get()->toArray();
        $exportAll = ExportAllProduct::get()->toArray();
        $topTrending = TrendingProduct::get()->toArray();
        $auburnPharmaceutical = AuburnPharmaceutical::get()->toArray();

        $arrActiveProducts = $arrCardinalHealth = $arrExportAll = $arrTopTrending = $arrAuburnPharmaceutical = [];
        $headers[] = ['Product Name', 'Vendor', 'NDC', 'Price', 'Wholesaler'];
        foreach ($activeProducts as $key => $row) {
            $arrActiveProducts[$key][] = $row['desc_one'];
            $arrActiveProducts[$key][] = $row['vendor'];
            $arrActiveProducts[$key][] = $row['ndc'];
            $arrActiveProducts[$key][] = '$' . str_replace('$', '', $row['list_price']);
            $arrActiveProducts[$key][] = $row['gpw'];
        }
        foreach ($cardinalHealth as $key => $row) {
            $arrCardinalHealth[$key][] = $row['trade_name_mfr'] . ' ' . $row['strength'] . ' ' . $row['from'] . ' ' . $row['size'];
            $arrCardinalHealth[$key][] = $row['trade_name_mfr2'];
            $arrCardinalHealth[$key][] = $row['cin_ndc_upc1'];
            $arrCardinalHealth[$key][] = '$' . str_replace('$', '', $row['invoice_cost']);
            $arrCardinalHealth[$key][] = $row['cardinal'];
        }
        foreach ($exportAll as $key => $row) {
            $arrExportAll[$key][] = $row['name'];
            $arrExportAll[$key][] = $row['vendor'];
            $arrExportAll[$key][] = $row['ndc'];
            $arrExportAll[$key][] = '$' . str_replace('$', '', $row['price']);
            $arrExportAll[$key][] = $row['wholesaler'];
        }
        foreach ($topTrending as $key => $row) {
            $arrTopTrending[$key][] = $row['product_name'] . ' ' . $row['strength'] . ' ' . $row['from'];
            $arrTopTrending[$key][] = $row['mfr'];
            $arrTopTrending[$key][] = $row['ndc'];
            $arrTopTrending[$key][] = '$' . str_replace('$', '', $row['best_price_today']);
            $arrTopTrending[$key][] = $row['trxade'];
        }
        foreach ($auburnPharmaceutical as $key => $row) {
            $arrAuburnPharmaceutical[$key][] = $row['description'];
            $arrAuburnPharmaceutical[$key][] = $row['vendor'];
            $arrAuburnPharmaceutical[$key][] = $row['ndc'];
            $arrAuburnPharmaceutical[$key][] = '$' . str_replace('$', '', $row['price']);
            $arrAuburnPharmaceutical[$key][] = $row['wholesaler'];
        }
        asort($arrActiveProducts);
        asort($arrCardinalHealth);
        asort($arrExportAll);
        asort($arrTopTrending);
        asort($arrAuburnPharmaceutical);
        $dataValuesArr = array_merge($headers, $arrActiveProducts, $arrCardinalHealth, $arrExportAll, $arrTopTrending, $arrAuburnPharmaceutical);
        $params = [];
        $params['values'] = $dataValuesArr;
        $this->writeSheet($params);
        if (!empty($arrActiveProducts)) {
            $this->activeProductCount = count($arrActiveProducts);
            $params['red'] = 100;
            $params['green'] = 50;
            $params['blue'] = 20;
            $params['alpha'] = 1;
            $params['startRowIndex'] = 1;
            $params['endRowIndex'] = $this->activeProductCount + 1;
            $params['endIndex'] = 5;
            $this->changeColors($params);
        }

        if (!empty($arrCardinalHealth)) {
            $this->cardinalCount = count($arrCardinalHealth);
            $params['red'] = 0;
            $params['green'] = 1;
            $params['blue'] = 0;
            $params['alpha'] = 0;
            $params['startRowIndex'] = $this->activeProductCount + 1;
            $params['endRowIndex'] = $this->activeProductCount + $this->cardinalCount + 1;
            $params['endIndex'] = 5;
            $this->changeColors($params);
        }
        if (!empty($arrExportAll)) {
            $isActiveProducts = !empty($this->activeProductCount) ? $this->activeProductCount + 1 : 0;
            if (empty($isActiveProducts)) {
                $isCardinalHealth = !empty($this->cardinalCount) ? $this->cardinalCount + 1 : 0;
            } else {
                $isCardinalHealth = !empty($this->cardinalCount) ? $this->cardinalCount : 0;
            }
            $startIndex = $isActiveProducts + $isCardinalHealth;
            $this->exportCount = count($arrExportAll);
            $params['red'] = 1;
            $params['green'] = 1;
            $params['blue'] = 0;
            $params['alpha'] = 0;
            $params['startRowIndex'] = !empty($startIndex) ? $startIndex : 1;
            $params['endRowIndex'] = $this->activeProductCount + $this->cardinalCount + $this->exportCount + 1;
            $params['endIndex'] = 5;
            $this->changeColors($params);
        }

        if (!empty($arrTopTrending)) {
            $isActiveProducts = !empty($this->activeProductCount) ? $this->activeProductCount + 1 : 0;
            if (empty($isActiveProducts)) {
                $isCardinalHealth = !empty($this->cardinalCount) ? $this->cardinalCount + 1 : 0;
            } else {
                $isCardinalHealth = !empty($this->cardinalCount) ? $this->cardinalCount : 0;
            }
            if (empty($isCardinalHealth) && empty($isActiveProducts)) {
                $isExportAll = !empty($this->exportCount) ? $this->exportCount + 1 : 0;
            } else {
                $isExportAll = !empty($this->exportCount) ? $this->exportCount : 0;
            }
            $startIndex = $isActiveProducts + $isCardinalHealth + $isExportAll;
            $this->topTrendingCount = count($arrTopTrending);
            $params['red'] = 1;
            $params['green'] = 0;
            $params['blue'] = 0;
            $params['alpha'] = 0;
            $params['startRowIndex'] = !empty($startIndex) ? $startIndex : 1;
            $params['endRowIndex'] = $this->activeProductCount + $this->cardinalCount + $this->exportCount + $this->topTrendingCount + 1;
            $params['endIndex'] = 5;
            $this->changeColors($params);
        }

        if (!empty($arrAuburnPharmaceutical)) {
            $isActiveProducts = !empty($this->activeProductCount) ? $this->activeProductCount + 1 : 0;
            $isCardinalHealth = !empty($this->cardinalCount) ? $this->cardinalCount : 0;
            $isExportAll = !empty($this->exportCount) ? $this->exportCount : 0;
            $isTopTrending = !empty($this->topTrendingCount) ? $this->topTrendingCount : 0;
            $startIndex = $isActiveProducts + $isCardinalHealth + $isExportAll + $isTopTrending;
            $this->auburnCount = count($arrAuburnPharmaceutical);
            $params['red'] = 0;
            $params['green'] = 1;
            $params['blue'] = 1;
            $params['alpha'] = 0;
            $params['startRowIndex'] = !empty($startIndex) ? $startIndex : 1;
            $params['endRowIndex'] = $this->activeProductCount + $this->cardinalCount + $this->exportCount + $this->topTrendingCount + $this->auburnCount + 1;
            $params['endIndex'] = 5;
            $this->changeColors($params);
        }

        return response()->json(['status' => true]);
    }

    /**
     * This is used to write data for google sheet
     *
     * @param $params
     * @throws \Google\Exception
     */
    public function writeSheet($params)
    {
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->documentId = '1ROWFwumLf-nCeA1-dF30AZKp12J103pLAjIIxQ_FbKE';
        $this->range = 'A:Z';

        $body = new Sheets\ValueRange([
            'values' => $params['values']
        ]);
        $valueOption = [
            'valueInputOption' => 'RAW'
        ];
        $params['red'] = 1;
        $params['green'] = 1;
        $params['blue'] = 1;
        $params['alpha'] = 0;
        $params['startRowIndex'] = 1;
        $params['endRowIndex'] = 100000000;
        $params['endIndex'] = 5;
        $this->changeColors($params);
        $this->service->spreadsheets_values->clear($this->documentId, $this->range, new Google_Service_Sheets_ClearValuesRequest());
        $this->service->spreadsheets_values->update($this->documentId, $this->range, $body, $valueOption);
    }

    /**
     * This is used to update the colors
     *
     * @param $params
     */
    public function changeColors($params)
    {
        $sheetId = $this->service->spreadsheets->get($this->documentId);
        $sheetId = $sheetId->sheets[0]->properties->sheetId;

        // define range
        $myRange = [
            'sheetId' => $sheetId, // IMPORTANT: sheetId IS NOT the sheets index but its actual ID
            'startRowIndex' => $params['startRowIndex'],
            'endRowIndex' => $params['endRowIndex'],
            //'startColumnIndex' => 0, // can be omitted because default is 0
            'endColumnIndex' => $params['endIndex'],
        ];

        // define the formatting, change background colour and bold text
        $format = [
            'backgroundColor' => [
                'red' => $params['red'],
                'green' => $params['green'],
                'blue' => $params['blue'],
                'alpha' => $params['alpha'],
            ],
        ];

        // build request
        $requests = [
            new Google_Service_Sheets_Request([
                'updateSpreadsheetProperties' => [
                    'properties' => [
                        'title' => 'Product Sheet  ' . Carbon::now()->format('d-M-Y')
                    ],
                    'fields' => 'title'
                ],
            ]),
            new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'fields' => 'userEnteredFormat.backgroundColor, userEnteredFormat.textFormat.bold',
                    'range' => $myRange,
                    'cell' => [
                        'userEnteredFormat' => $format,
                    ],
                ],
            ])
        ];

        // add request to batchUpdate
        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);

        // run batchUpdate
        $this->service->spreadsheets->batchUpdate($this->documentId, $batchUpdateRequest);
    }

    /**
     * This is used to get clientp
     *
     * @return Client
     * @throws \Google\Exception
     */
    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('Google sheet demo');
        $client->setRedirectUri('http://127.0.0.1:8000/home');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig('credentials2.json');
        $client->setAccessType('offline');

        return $client;
    }
}
