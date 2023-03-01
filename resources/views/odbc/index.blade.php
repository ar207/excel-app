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
            width: 300px;
            z-index: 1;
        }

    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
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
                        <table class="table table-striped">
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
                            <tbody id="page-data"></tbody>
                        </table>
                        <div class="m-3 paq-pager"></div>
                    </div>
                </div>
            </div>
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
    <script>
        let page = 1, search = '';
        $(document).ready(function () {
            getData();
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

        $('body').on('keyup', '#search', function (e) {
            if (e.keyCode == 13) {
                search = $(this).val();
                getData();
            }
        });

        $(document).on("click", '.paq-pager ul.pagination a', function (e) {
            e.preventDefault();
            page = $(this).attr('href').split('page=')[1];
            getData();
        });

        $('body').on('click', '#export-to-excel', function () {
            window.location.href = "{{ url('odbc/export') }}";
        });

        function getData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                page: page,
                per_page: 10,
                search: search
            };
            $.ajax({
                url: "{{ url('odbc/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    console.log(response.data.odbc.data);
                    let html = '', count = 1;
                    if (!empty(response.data.odbc)) {
                        $.each(response.data.odbc.data, function (i, v) {
                            let cardinalData = '', exportData = '', trendingData = '', auburnData = '', gpwData = '',
                                cardinalFull = '', exportFull = '', trendingFull = '', auburnFull = '';
                            if (v.gpw_price != '-') {
                                gpwData = 'NDC:' + v.ndc + ' Name:' + v.name + ' Strength:' + v.strength + ' Form:' + v.form + ' Count:' + v.count;
                            }
                            if (v.cardinal_price != '-') {
                                if (!empty(v.cardinal_full)) {
                                    $.each(v.cardinal_full, function (ii, vv) {
                                        cardinalFull += '<tr>' +
                                            '   <td>' + vv.cin_ndc_upc1 + '</td>' +
                                            '   <td>' + vv.fda_name + '</td>' +
                                            '   <td>' + vv.fda_strength + '</td>' +
                                            '   <td>' + vv.fda_form + '</td>' +
                                            '   <td>' + vv.fda_count + '</td>' +
                                            '   <td>$' + vv.invoice_cost + '</td>' +
                                            '</tr>';
                                    });
                                }
                                cardinalData = 'NDC:' + v.cardinal_data.cin_ndc_upc1 + ' Name:' + v.cardinal_data.fda_name + ' Strength:' + v.cardinal_data.fda_strength + ' Count:' + v.cardinal_data.fda_count;
                            }
                            if (v.export_price != '-') {
                                if (!empty(v.export_full)) {
                                    $.each(v.export_full, function (ii, vv) {
                                        exportFull += '<tr>' +
                                            '   <td>' + vv.ndc + '</td>' +
                                            '   <td>' + vv.fda_name + '</td>' +
                                            '   <td>' + vv.fda_strength + '</td>' +
                                            '   <td>' + vv.fda_form + '</td>' +
                                            '   <td>' + vv.fda_count + '</td>' +
                                            '   <td>$' + vv.price + '</td>' +
                                            '</tr>';
                                    });
                                }
                                exportData = 'NDC:' + v.export_data.ndc + ' Name:' + v.export_data.fda_name + ' Strength:' + v.export_data.fda_strength + ' Count:' + v.export_data.fda_count;
                            }
                            if (v.trending_price != '-') {
                                if (!empty(v.trending_full)) {
                                    $.each(v.trending_full, function (ii, vv) {
                                        trendingFull += '<tr>' +
                                            '   <td>' + vv.ndc + '</td>' +
                                            '   <td>' + vv.fda_name + '</td>' +
                                            '   <td>' + vv.fda_strength + '</td>' +
                                            '   <td>' + vv.fda_form + '</td>' +
                                            '   <td>' + vv.fda_count + '</td>' +
                                            '   <td>$' + vv.best_price_today + '</td>' +
                                            '</tr>';
                                    });
                                }
                                trendingData = 'NDC:' + v.trending_data.ndc + ' Name:' + v.trending_data.fda_name + ' Strength:' + v.trending_data.fda_strength + ' Count:' + v.trending_data.fda_count;
                            }
                            if (v.auburn_price != '-') {
                                if (!empty(v.auburn_full)) {
                                    $.each(v.auburn_full, function (ii, vv) {
                                        auburnFull += '<tr>' +
                                            '   <td>' + vv.ndc + '</td>' +
                                            '   <td>' + vv.fda_name + '</td>' +
                                            '   <td>' + vv.fda_strength + '</td>' +
                                            '   <td>' + vv.fda_form + '</td>' +
                                            '   <td>' + vv.fda_count + '</td>' +
                                            '   <td>$' + vv.price + '</td>' +
                                            '</tr>';
                                    });
                                }
                                auburnData = 'NDC:' + v.auburn_data.ndc + ' Name:' + v.auburn_data.fda_name + ' Strength:' + v.auburn_data.fda_strength + ' Count:' + v.auburn_data.fda_count;
                            }
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.product_no + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.name + '</td>' +
                                '   <td>' + v.strength + '</td>' +
                                '   <td>' + v.form + '</td>' +
                                '   <td>' + v.count + '</td>' +
                                '   <td><span class="cursor-pointer decoration-none" data-title="' + gpwData + '">' + v.gpw_price + '</span></td>' +
                                '   <td><span data-toggle="modal" data-header="Cardinal" data-html="' + cardinalFull + '" class="cursor-pointer decoration-none total-prices-data" data-title="' + cardinalData + '">' + v.cardinal_price + '</span></td>' +
                                '   <td><span data-toggle="modal" data-header="Ezirx" data-html="' + exportFull + '" class="cursor-pointer decoration-none total-prices-data" data-title="' + exportData + '">' + v.export_price + '</span></td>' +
                                '   <td><span data-toggle="modal" data-header="Trxade" data-html="' + trendingFull + '" class="cursor-pointer decoration-none total-prices-data" data-title="' + trendingData + '">' + v.trending_price + '</span></td>' +
                                '   <td><span data-toggle="modal" data-header="Auburn" data-html="' + auburnFull + '" class="cursor-pointer decoration-none total-prices-data" data-title="' + auburnData + '">' + v.auburn_price + '</span></td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#page-data').html('').html(html);
                    if (response.data.pager !== 'undefined') {
                        $('.paq-pager').show().html(response.data.pager);
                    }
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.statusText, 'error');
                }
            })
        }

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
