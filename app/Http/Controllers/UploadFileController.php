<?php

namespace App\Http\Controllers;

use App\Models\ActiveProductListing;
use App\Models\AuburnPharmaceutical;
use App\Models\CardinalHealth;
use App\Models\ExportAllProduct;
use App\Models\FDA;
use App\Models\FileCategory;
use App\Models\ProductPackageCombination;
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
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '-1');
            if (!empty($data['active_product_listing'])) {
                $this->activeProductListing($request);
            }
            if (!empty($data['cardinal_health'])) {
                $this->cardinalHealth($request);
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
            $this->prepareDataForGoogleSheet($data);
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
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[6])) {
                $correctNdc = ndcCorrection($row[6]);
                $ndc = str_replace('-', '', $correctNdc);
                $fda = ProductPackageCombination::where('ndc_match', $ndc)->first();
                $fdaData = [];
                if (!empty($fda)) {
                    $fdaData = $fda->toArray();
                }
                $arrProductListing[$count]['product_no'] = isset($row[0]) ? $row[0] : '';
                $arrProductListing[$count]['desc_one'] = isset($row[1]) ? $row[1] : '';
                $arrProductListing[$count]['vendor'] = isset($row[5]) ? $row[5] : '';
                $arrProductListing[$count]['ndc'] = isset($correctNdc) ? $correctNdc : '';
                $arrProductListing[$count]['list_price'] = isset($row[4]) ? str_replace('$', '', $row[4]) : '';
                $arrProductListing[$count]['name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                $arrProductListing[$count]['strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] . ' ' . $fdaData['unit'] : '';
                $arrProductListing[$count]['form'] = !empty($fdaData['dosage_form']) ? $fdaData['dosage_form'] : '';
                $arrProductListing[$count]['count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                $arrProductListing[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                $arrProductListing[$count]['gpw'] = 'Gpw';
                $arrProductListing[$count]['created_at'] = now();
                $arrProductListing[$count]['updated_at'] = now();
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
        $arrCardinal = [];
        $count = 0;
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[1])) {
                $correctNdc = ndcCorrection($row[1]);
                $ndc = str_replace('-', '', $correctNdc);
                $fda = ProductPackageCombination::where('ndc_match', $ndc)->first();
                $fdaData = [];
                if (!empty($fda)) {
                    $fdaData = $fda->toArray();
                }
                $arrCardinal[$count]['cin_ndc_upc'] = isset($row[0]) ? $row[0] : '';
                $arrCardinal[$count]['cin_ndc_upc1'] = isset($correctNdc) ? $correctNdc : '';
                $arrCardinal[$count]['trade_name_mfr'] = isset($row[2]) ? $row[2] : '';
                $arrCardinal[$count]['trade_name_mfr2'] = isset($row[3]) ? $row[3] : '';
                $arrCardinal[$count]['strength'] = isset($row[4]) ? $row[4] : '';
                $arrCardinal[$count]['from'] = isset($row[5]) ? $row[5] : '';
                $arrCardinal[$count]['size'] = isset($row[6]) ? $row[6] : '';
                $arrCardinal[$count]['type'] = isset($row[7]) ? $row[7] : '';
                $arrCardinal[$count]['fda_name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                $arrCardinal[$count]['fda_strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] . ' ' . $fdaData['unit'] : '';
                $arrCardinal[$count]['fda_form'] = !empty($fdaData['dosage_form']) ? $fdaData['dosage_form'] : '';
                $arrCardinal[$count]['fda_count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                $arrCardinal[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                $arrCardinal[$count]['net_cost'] = isset($row[8]) ? $row[8] : '';
                $arrCardinal[$count]['invoice_cost'] = isset($row[9]) ? str_replace('$', '', $row[9]) : '';
                $arrCardinal[$count]['cardinal'] = 'Cardinal';
                $arrCardinal[$count]['created_at'] = now();
                $arrCardinal[$count]['updated_at'] = now();
            }
            $count++;
        }

        if (!empty($arrCardinal)) {
            unset($arrCardinal[0]);
        }
        CardinalHealth::truncate();
        foreach (array_chunk($arrCardinal, 1000) as $ind) {
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
        $arrExport = [];
        $count = 0;
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[1])) {
                $correctNdc = ndcCorrection($row[0]);
                $ndc = str_replace('-', '', $correctNdc);
                $fda = ProductPackageCombination::where('ndc_match', $ndc)->first();
                $fdaData = [];
                if (!empty($fda)) {
                    $fdaData = $fda->toArray();
                }
                $arrExport[$count]['name'] = isset($row[1]) ? $row[1] : '';
                $arrExport[$count]['vendor'] = '';
                $arrExport[$count]['ndc'] = isset($correctNdc) ? $correctNdc : '';
                $arrExport[$count]['price'] = isset($row[7]) ? str_replace('$', '', $row[7]) : '';
                $arrExport[$count]['fda_name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                $arrExport[$count]['fda_strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] . ' ' . $fdaData['unit'] : '';
                $arrExport[$count]['fda_form'] = !empty($fdaData['dosage_form']) ? $fdaData['dosage_form'] : '';
                $arrExport[$count]['fda_count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                $arrExport[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                $arrExport[$count]['wholesaler'] = 'EzriRx';
                $arrExport[$count]['created_at'] = now();
                $arrExport[$count]['updated_at'] = now();
            }
            $count++;
        }
        if (!empty($arrExport)) {
            unset($arrExport[0]);
        }
        ExportAllProduct::truncate();
        foreach (array_chunk($arrExport, 1000) as $ind) {
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
        $arrTrending = [];
        $count = 0;
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[1])) {
                $correctNdc = ndcCorrection($row[1]);
                $ndc = str_replace('-', '', $correctNdc);
                $fda = ProductPackageCombination::where('ndc_match', $ndc)->first();
                $fdaData = [];
                if (!empty($fda)) {
                    $fdaData = $fda->toArray();
                }
                $arrTrending[$count]['ndc'] = isset($correctNdc) ? $correctNdc : '';
                $arrTrending[$count]['product_name'] = isset($row[2]) ? $row[2] : '';
                $arrTrending[$count]['strength'] = isset($row[3]) ? $row[3] : '';
                $arrTrending[$count]['package_size'] = isset($row[4]) ? $row[4] : '';
                $arrTrending[$count]['from'] = isset($row[5]) ? $row[5] : '';
                $arrTrending[$count]['mfr'] = isset($row[6]) ? $row[6] : '';
                $arrTrending[$count]['type'] = isset($row[7]) ? $row[7] : '';
                $arrTrending[$count]['low_sold_price'] = isset($row[8]) ? $row[8] : '';
                $arrTrending[$count]['avg_sold_price'] = isset($row[9]) ? $row[9] : '';
                $arrTrending[$count]['high_sold_price'] = isset($row[10]) ? $row[10] : '';
                $arrTrending[$count]['best_price_today'] = isset($row[11]) ? str_replace('$', '', $row[11]) : '';
                $arrTrending[$count]['fda_name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                $arrTrending[$count]['fda_strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] . ' ' . $fdaData['unit'] : '';
                $arrTrending[$count]['fda_form'] = !empty($fdaData['dosage_form']) ? $fdaData['dosage_form'] : '';
                $arrTrending[$count]['fda_count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                $arrTrending[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                $arrTrending[$count]['trxade'] = 'Trxade';
                $arrTrending[$count]['created_at'] = now();
                $arrTrending[$count]['updated_at'] = now();
            }
            $count++;
        }
        if (!empty($arrTrending)) {
            unset($arrTrending[0]);
        }
        TrendingProduct::truncate();
        foreach (array_chunk($arrTrending, 1000) as $ind) {
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
        $arrAuburn = [];
        $count = 0;
        foreach ($rows[0] as $key => $row) {
            if (!empty($row[3])) {
                $correctNdc = ndcCorrection($row[3]);
                $ndc = str_replace('-', '', $correctNdc);
                $fda = ProductPackageCombination::where('ndc_match', $ndc)->first();
                $fdaData = [];
                if (!empty($fda)) {
                    $fdaData = $fda->toArray();
                }
                $arrAuburn[$count]['description'] = isset($row[9]) ? $row[9] : '';
                $arrAuburn[$count]['vendor'] = isset($row[11]) ? $row[11] : '';
                $arrAuburn[$count]['ndc'] = isset($correctNdc) ? $correctNdc : '';
                $arrAuburn[$count]['price'] = isset($row[26]) ? str_replace('$', '', $row[26]) : '';
                $arrAuburn[$count]['wholesaler'] = 'Auburn';
                $arrAuburn[$count]['fda_name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                $arrAuburn[$count]['fda_strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] . ' ' . $fdaData['unit'] : '';
                $arrAuburn[$count]['fda_form'] = !empty($fdaData['dosage_form']) ? $fdaData['dosage_form'] : '';
                $arrAuburn[$count]['fda_count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                $arrAuburn[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                $arrAuburn[$count]['created_at'] = now();
                $arrAuburn[$count]['updated_at'] = now();
            }
            $count++;
        }

        if (!empty($arrAuburn)) {
            unset($arrAuburn[0]);
        }
        AuburnPharmaceutical::truncate();
        foreach (array_chunk($arrAuburn, 1000) as $ind) {
            AuburnPharmaceutical::insert($ind);
        }
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws \Google\Exception
     */
    public function prepareDataForGoogleSheet($data)
    {
        $activeProducts = ActiveProductListing::get()->toArray();
        $cardinalHealth = CardinalHealth::get()->toArray();
        $exportAll = ExportAllProduct::get()->toArray();
        $topTrending = TrendingProduct::get()->toArray();
        $auburnPharmaceutical = AuburnPharmaceutical::get()->toArray();

        $arrActiveProducts = $arrCardinalHealth = $arrExportAll = $arrTopTrending = $arrAuburnPharmaceutical = [];
        $headers[] = ['Product Name', 'Vendor', 'NDC', 'Price', 'Wholesaler'];
        if (!empty($data['active_product_listing'])) {
            foreach ($activeProducts as $key => $row) {
                $arrActiveProducts[$key][] = $row['desc_one'];
                $arrActiveProducts[$key][] = $row['vendor'];
                $arrActiveProducts[$key][] = $row['ndc'];
                $arrActiveProducts[$key][] = '$' . str_replace('$', '', $row['list_price']);
                $arrActiveProducts[$key][] = $row['gpw'];
            }
        }
        if (!empty($data['cardinal_health'])) {
            foreach ($cardinalHealth as $key => $row) {
                $arrCardinalHealth[$key][] = $row['trade_name_mfr'] . ' ' . $row['strength'] . ' ' . $row['from'] . ' ' . $row['size'];
                $arrCardinalHealth[$key][] = $row['trade_name_mfr2'];
                $arrCardinalHealth[$key][] = $row['cin_ndc_upc1'];
                $arrCardinalHealth[$key][] = '$' . str_replace('$', '', $row['invoice_cost']);
                $arrCardinalHealth[$key][] = $row['cardinal'];
            }
        }
        if (!empty($data['export_all'])) {
            foreach ($exportAll as $key => $row) {
                $arrExportAll[$key][] = $row['name'];
                $arrExportAll[$key][] = $row['vendor'];
                $arrExportAll[$key][] = $row['ndc'];
                $arrExportAll[$key][] = '$' . str_replace('$', '', $row['price']);
                $arrExportAll[$key][] = $row['wholesaler'];
            }
        }
        if (!empty($data['top_trending'])) {
            foreach ($topTrending as $key => $row) {
                $arrTopTrending[$key][] = $row['product_name'] . ' ' . $row['strength'] . ' ' . $row['from'];
                $arrTopTrending[$key][] = $row['mfr'];
                $arrTopTrending[$key][] = $row['ndc'];
                $arrTopTrending[$key][] = '$' . str_replace('$', '', $row['best_price_today']);
                $arrTopTrending[$key][] = $row['trxade'];
            }
        }
        if (!empty($data['auburn_pharmaceutical'])) {
            foreach ($auburnPharmaceutical as $key => $row) {
                $arrAuburnPharmaceutical[$key][] = $row['description'];
                $arrAuburnPharmaceutical[$key][] = $row['vendor'];
                $arrAuburnPharmaceutical[$key][] = $row['ndc'];
                $arrAuburnPharmaceutical[$key][] = '$' . str_replace('$', '', $row['price']);
                $arrAuburnPharmaceutical[$key][] = $row['wholesaler'];
            }
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
//        $this->documentId = '1VZzH9zs_H8XsRWnGqyph-xU6JYxiAaOvcY5-q9VmDdw';
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
