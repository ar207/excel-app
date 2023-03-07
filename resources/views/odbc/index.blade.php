@extends('layouts.app')

@section('content')
    <style>
        .cursor-pointer {
            cursor: pointer !important;
        }

        .decoration-none {
            text-decoration: none !important;
        }

        span {
            font-style: italic;
            position: relative
        }

        span:hover::after {
            background: #add8e6;
            border-radius: 4px;
            content: attr(data-title);
            display: block;
            right: 100%;
            padding: 1em;
            position: absolute;
            width: 400px;
            z-index: 1;
        }

    </style>
    <div class="card m-2">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h6 class="h6">ODBC</h6>
                </div>
                <div class="col-sm-6 col-12">
                    <button class="btn btn-sm btn-primary float-right" id="export-to-excel">Export to
                        excel
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div class="form-group m-2">
                <input class="form-control" type="search" id="search" placeholder="Search....">
            </div>
            <table class="table table-striped" id="dataTables-example">
                <thead>
                <th>#</th>
                <th>Product No</th>
                <th>NDC</th>
                <th>Name</th>
                <th>Strength</th>
                <th>Form</th>
                <th>Count</th>
                <th>GPW</th>
                <th>Cardinal</th>
                <th>Ezirx</th>
                <th>Trxade</th>
                <th>Auburn</th>
                </thead>
                <tbody>
                @php
                    $count = 1;
                @endphp
                @foreach($odbc as $key => $row)
                    @php
                        $gpwData = $cardinalData = $exportData = $trendingData = $auburnData = $cardinalFull = $exportFull = $trendingFull = $auburnFull = '';
                        if ($row['gpw_price'] != '-') {
                            $gpwData = 'NDC:'.$row['ndc'].' Name:' . $row['name'] . ' Strength:' . $row['strength'] . ' Form:' . $row['form'] . ' Count:' . $row['count'];
                        }
                        if ($row['cardinal_price'] != '-') {
                            foreach ($row['cardinal_full'] as $key => $cardinal) {
                                $cardinalFull .= '<tr>' .
                                                    '<td>' . $cardinal['cin_ndc_upc1'] . '</td>' .
                                                    '<td>' . $cardinal['trade_name_mfr'] . '</td>' .
                                                    '<td>' . $cardinal['strength'] . '</td>' .
                                                    '<td>' . $cardinal['from'] . '</td>' .
                                                    '<td>' . $cardinal['size'] . '</td>' .
                                                    '<td>$' . $cardinal['invoice_cost'] . '</td>' .
                                                '</tr>';
                            }
                            $cardinalData = 'NDC:' . $row['cardinal_data']['cin_ndc_upc1'] . ' Name:' . $row['cardinal_data']['trade_name_mfr'] . ' Strength:' . $row['cardinal_data']['strength'] . ' Form:' . $row['cardinal_data']['from'] . ' Count:' . $row['cardinal_data']['size'];
                        }
                        if ($row['export_price'] != '-') {
                            foreach ($row['export_full'] as $key => $export) {
                                $exportFull .= '<tr>' .
                                                    '<td>' . $export['ndc'] . '</td>' .
                                                    '<td>' . $export['fda_name'] . '</td>' .
                                                    '<td>' . $export['fda_strength'] . '</td>' .
                                                    '<td>' . $export['fda_form'] . '</td>' .
                                                    '<td>' . $export['fda_count'] . '</td>' .
                                                    '<td>$' . $export['price'] . '</td>' .
                                                '</tr>';
                            }
                            $exportData = 'NDC:' . $row['export_data']['ndc'] . ' Name:' . $row['export_data']['fda_name'] . ' Strength:' . $row['export_data']['fda_strength'] . ' Form:' . $row['export_data']['fda_form'] . ' Count:' . $row['export_data']['fda_count'];
                        }
                        if ($row['trending_price'] != '-') {
                            foreach ($row['trending_full'] as $key => $export) {
                                $trendingFull .= '<tr>' .
                                                    '<td>' . $export['ndc'] . '</td>' .
                                                    '<td>' . $export['fda_name'] . '</td>' .
                                                    '<td>' . $export['fda_strength'] . '</td>' .
                                                    '<td>' . $export['fda_form'] . '</td>' .
                                                    '<td>' . $export['fda_count'] . '</td>' .
                                                    '<td>$' . $export['best_price_today'] . '</td>' .
                                                '</tr>';
                            }
                            $trendingData = 'NDC:' . $row['trending_data']['ndc'] . ' Name:' . $row['trending_data']['fda_name'] . ' Strength:' . $row['trending_data']['fda_strength'] . ' Form:' . $row['trending_data']['fda_form'] . ' Count:' . $row['trending_data']['fda_count'];
                        }
                        if ($row['auburn_price'] != '-') {
                            foreach ($row['auburn_full'] as $key => $export) {
                                $auburnFull .= '<tr>' .
                                                    '<td>' . $export['ndc'] . '</td>' .
                                                    '<td>' . $export['fda_name'] . '</td>' .
                                                    '<td>' . $export['fda_strength'] . '</td>' .
                                                    '<td>' . $export['fda_form'] . '</td>' .
                                                    '<td>' . $export['fda_count'] . '</td>' .
                                                    '<td>$' . $export['price'] . '</td>' .
                                                '</tr>';
                            }
                            $auburnData = 'NDC:' . $row['auburn_data']['ndc'] . ' Name:' . $row['auburn_data']['fda_name'] . ' Strength:' . $row['auburn_data']['fda_strength'] . ' Form:' . $row['auburn_data']['fda_form'] . ' Count:' . $row['auburn_data']['fda_count'];
                        }
                    @endphp
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $row['product_no'] }}</td>
                        <td>{{ $row['ndc'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['strength'] }}</td>
                        <td>{{ $row['form'] }}</td>
                        <td>{{ $row['count'] }}</td>
                        <td>
                            <span class="cursor-pointer decoration-none"
                                  data-title="{{ $gpwData }}">
                                {{ $row['gpw_price'] }}
                            </span>
                        </td>
                        <td>
                            <span data-toggle="modal" data-header="Cardinal" data-html="{{ $cardinalFull }}"
                                  class="cursor-pointer decoration-none total-prices-data"
                                  data-title="{{ $cardinalData }}">
                                {{ $row['cardinal_price'] }}
                            </span>
                        </td>
                        <td>
                            <span data-toggle="modal" data-header="Ezirx" data-html="{{ $exportFull }}"
                                  class="cursor-pointer decoration-none total-prices-data"
                                  data-title="{{ $exportData }}">
                                {{ $row['export_price'] }}
                            </span>
                        </td>
                        <td>
                            <span data-toggle="modal" data-header="Txrade" data-html="{{ $trendingFull }}"
                                  class="cursor-pointer decoration-none total-prices-data"
                                  data-title="{{ $trendingData }}">
                                {{ $row['trending_price'] }}
                            </span>
                        </td>
                        <td>
                            <span data-toggle="modal" data-header="Txrade" data-html="{{ $auburnFull }}"
                                  class="cursor-pointer decoration-none total-prices-data"
                                  data-title="{{ $auburnData }}">
                                {{ $row['auburn_price'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="m-3 paq-pager"></div>
        </div>
    </div>
    <div class="modal fade" id="dataModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-header"></h6>
                    <button type="button" class="close font-15" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <th>NDC</th>
                            <th>Name</th>
                            <th>Strength</th>
                            <th>Form</th>
                            <th>Count</th>
                            <th>Price</th>
                            </thead>
                            <tbody id="price-data"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/datatable.min.css') }}">
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function () {
        });

        /**
         * This is used to check whether a value is empty
         *
         * @param val
         * @returns {boolean}
         */
        function empty(val) {
            return !(!!val ? typeof val === 'object' ? Array.isArray(val) ? !!val.length : !!Object.keys(val).length : true : false);
        }

        $('body').on('click', '#export-to-excel', function () {
            window.location.href = "{{ url('odbc/export') }}";
        });

        oTable = $('#dataTables-example').DataTable({
            "bPaginate": false,
            "ordering": false
        });//pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
        $('#dataTables-example_filter, #dataTables-example_length').hide();
        $('#search').keyup(function () {
            oTable.search($(this).val()).draw();
        });

        $('body').on('click', '.total-prices-data', function () {
            const data = $(this).attr('data-html');
            const header = $(this).attr('data-header');
            $('#modal-header').html('').html(header);
            $('#price-data').html('').html(data);
            $('#dataModal').modal('show');
        });

        function showLoader() {
            $('#preloader').show();
            $(document.body).css('pointer-events', 'none');
        }

        function hideLoader() {
            $('#preloader').hide();
            $(document.body).css('pointer-events', 'all');
        }
    </script>
@endsection
