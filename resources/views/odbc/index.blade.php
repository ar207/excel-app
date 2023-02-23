@extends('layouts.app')

@section('content')
    <style>
        .cursor-pointer {
            cursor: pointer;
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
@endsection
@section('scripts')
    <script>
        let page = 1;
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
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
            };
            $.ajax({
                url: "{{ url('odbc/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data.odbc)) {
                        $.each(response.data.odbc.data, function (i, v) {
                            let cardinalData = '', exportData = '', trendingData = '', auburnData = '', gpwData;
                            if (v.gpw_price != '-') {
                                gpwData = 'NDC:' + v.ndc + ' Name:' + v.name + ' Strength:' + v.strength + ' Form:' + v.form + ' Count:' + v.count;
                            }
                            if (v.cardinal_price != '-') {
                                cardinalData = 'NDC:' + v.cardinal_data.fda_ndc + ' Name:' + v.cardinal_data.fda_name + ' Strength:' + v.cardinal_data.fda_strength + ' Count:' + v.cardinal_data.fda_count;
                            }
                            if (v.export_price != '-') {
                                exportData = 'NDC:' + v.export_data.fda_ndc + ' Name:' + v.export_data.fda_name + ' Strength:' + v.export_data.fda_strength + ' Count:' + v.export_data.fda_count;
                            }
                            if (v.trending_price != '-') {
                                trendingData = 'NDC:' + v.trending_data.fda_ndc + ' Name:' + v.trending_data.fda_name + ' Strength:' + v.trending_data.fda_strength + ' Count:' + v.trending_data.fda_count;
                            }
                            if (v.auburn_price != '-') {
                                auburnData = 'NDC:' + v.auburn_data.fda_ndc + ' Name:' + v.auburn_data.fda_name + ' Strength:' + v.auburn_data.fda_strength + ' Count:' + v.auburn_data.fda_count;
                            }
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.product_no + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.name + '</td>' +
                                '   <td>' + v.strength + '</td>' +
                                '   <td>' + v.form + '</td>' +
                                '   <td>' + v.count + '</td>' +
                                '   <td><a class="cursor-pointer" data-toggle="tooltip" data-html="true" title="' + gpwData + '">' + v.gpw_price + '</a></td>' +
                                '   <td><a class="cursor-pointer" data-toggle="tooltip" data-html="true" title="' + cardinalData + '">' + v.cardinal_price + '</a></td>' +
                                '   <td><a class="cursor-pointer" data-toggle="tooltip" data-html="true" title="' + exportData + '">' + v.export_price + '</a></td>' +
                                '   <td><a class="cursor-pointer" data-toggle="tooltip" data-html="true" title="' + trendingData + '">' + v.trending_price + '</a></td>' +
                                '   <td><a class="cursor-pointer" data-toggle="tooltip" data-html="true" title="' + auburnData + '">' + v.auburn_price + '</a></td>' +
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
