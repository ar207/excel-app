@extends('layouts.app')

@section('content')
    <div class="card m-2">
        <div class="card-header">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="btn btn-primary m-2 file-data active" id="pills-odbc-tab" data-toggle="pill"
                            data-target="#pills-odbc" type="button" role="tab" aria-controls="pills-odbc"
                            aria-selected="true">ODBC
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-primary m-2 file-data" id="pills-cardinal-tab" data-toggle="pill"
                            data-target="#pills-cardinal" type="button" role="tab" aria-controls="pills-cardinal"
                            aria-selected="false">Cardinal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-primary m-2 file-data" id="pills-ezirx-tab" data-toggle="pill"
                            data-target="#pills-ezirx" type="button" role="tab" aria-controls="pills-ezirx"
                            aria-selected="false">Ezirx
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-primary m-2 file-data" id="pills-txrade-tab" data-toggle="pill"
                            data-target="#pills-txrade" type="button" role="tab" aria-controls="pills-txrade"
                            aria-selected="false">Txrade
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-primary m-2 file-data" id="pills-auburn-tab" data-toggle="pill"
                            data-target="#pills-auburn" type="button" role="tab" aria-controls="pills-auburn"
                            aria-selected="false">Auburn
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content card-body" id="pills-tabContent">
            <div class="tab-pane included-files fade show active" id="pills-odbc" role="tabpanel"
                 aria-labelledby="pills-odbc-tab">
                <div class="form-group">
                    <label>
                        Matched Results
                        <input type="radio" checked name="odbc" class="fda-match-radio" value="1" id="odbc_match">
                    </label>
                    <label>
                        Non Matched Results
                        <input type="radio" name="odbc" class="fda-match-radio" value="0" id="odbc_non-match">
                    </label>
                </div>
                @include('all-files.partials._odbc')
            </div>
            <div class="tab-pane included-files fade" id="pills-cardinal" role="tabpanel"
                 aria-labelledby="pills-cardinal-tab">
                <div class="form-group">
                    <label>
                        Matched Results
                        <input type="radio" checked name="cardinal" class="fda-match-radio" value="1" id="cardinal_match">
                    </label>
                    <label>
                        Non Matched Results
                        <input type="radio" name="cardinal" class="fda-match-radio" value="0" id="cardinal_non-match">
                    </label>
                </div>
                @include('all-files.partials._cardinal')
            </div>
            <div class="tab-pane included-files fade" id="pills-ezirx" role="tabpanel"
                 aria-labelledby="pills-ezirx-tab">
                <div class="form-group">
                    <label>
                        Matched Results
                        <input type="radio" checked name="ezirx" class="fda-match-radio" value="1" id="ezirx_match">
                    </label>
                    <label>
                        Non Matched Results
                        <input type="radio" name="ezirx" class="fda-match-radio" value="0" id="ezirx_non-match">
                    </label>
                </div>
                @include('all-files.partials._ezrix')
            </div>
            <div class="tab-pane included-files fade" id="pills-txrade" role="tabpanel"
                 aria-labelledby="pills-txrade-tab">
                <div class="form-group">
                    <label>
                        Matched Results
                        <input type="radio" checked name="txrade" class="fda-match-radio" value="1" id="txrade_match">
                    </label>
                    <label>
                        Non Matched Results
                        <input type="radio" name="txrade" class="fda-match-radio" value="0" id="txrade_non-match">
                    </label>
                </div>
                @include('all-files.partials._txrade')
            </div>
            <div class="tab-pane included-files fade" id="pills-auburn" role="tabpanel"
                 aria-labelledby="pills-auburn-tab">
                <div class="form-group">
                    <label>
                        Matched Results
                        <input type="radio" checked name="auburn" class="fda-match-radio" value="1" id="auburn_match">
                    </label>
                    <label>
                        Non Matched Results
                        <input type="radio" name="auburn" class="fda-match-radio" value="0" id="auburn_non-match">
                    </label>
                </div>
                @include('all-files.partials._auburn')
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @yield('partial_scripts')
    <script>
        let search = '', isMatch = 1;
        $(document).ready(function () {
            odbcData();
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

        $('body').on('click', '.file-data', function () {
            const id = $(this).attr('id').split('-');
            search = '';
            isMatch = 1;
            $('#' + id + '-search').val('');
            if (id[1] == 'odbc') {
                odbcData();
            }
            if (id[1] == 'cardinal') {
                cardinalData();
            }
            if (id[1] == 'ezirx') {
                ezirxData();
            }
            if (id[1] == 'txrade') {
                txradeData();
            }
            if (id[1] == 'auburn') {
                auburnData();
            }
        });

        $('body').on('click', '.fda-match-radio', function () {
            const id = $(this).attr('name');
            search = '';
            $('#' + id + '-search').val('');
            isMatch = $(this).val();
            if (id == 'odbc') {
                odbcData();
            }
            if (id == 'cardinal') {
                cardinalData();
            }
            if (id == 'ezirx') {
                ezirxData();
            }
            if (id == 'txrade') {
                txradeData();
            }
            if (id == 'auburn') {
                auburnData();
            }
        });

        $('body').on('keyup', '#cardinal-search', function (e) {
            search = $(this).val();
            cardinalData();
        });

        function cardinalData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                search: search,
                is_match:isMatch
            };
            $.ajax({
                url: "{{ url('cardinal/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data)) {
                        $.each(response.data, function (i, v) {
                            const name = v.trade_name_mfr + ' ' + v.strength + ' ' + v.from + ' ' + v.size;
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.cin_ndc_upc1 + '</td>' +
                                '   <td>' + v.fda_name + '</td>' +
                                '   <td>' + v.fda_strength + '</td>' +
                                '   <td>' + v.fda_form + '</td>' +
                                '   <td>' + v.fda_count + '</td>' +
                                '   <td>' + v.trade_name_mfr2 + '</td>' +
                                '   <td>$' + v.invoice_cost + '</td>' +
                                '   <td>' + v.date_created + '</td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#cardinal-data').html('').html(html);
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.responseJSON.message, 'error');
                }
            })
        }

        $('body').on('keyup', '#ezirx-search', function (e) {
            search = $(this).val();
            ezirxData();
        });

        function ezirxData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                search: search,
                is_match:isMatch
            };
            $.ajax({
                url: "{{ url('ezirx/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data)) {
                        $.each(response.data, function (i, v) {
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.fda_name + '</td>' +
                                '   <td>' + v.fda_form + '</td>' +
                                '   <td>' + v.fda_strength + '</td>' +
                                '   <td>' + v.fda_count + '</td>' +
                                '   <td>' + v.vendor + '</td>' +
                                '   <td>$' + v.price + '</td>' +
                                '   <td>' + v.date_created + '</td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#ezirx-data').html('').html(html);
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.responseJSON.message, 'error');
                }
            })
        }

        $('body').on('keyup', '#txrade-search', function (e) {
            search = $(this).val();
            txradeData();
        });

        function txradeData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                search: search,
                is_match:isMatch
            };
            $.ajax({
                url: "{{ url('txrade/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data)) {
                        $.each(response.data, function (i, v) {
                            const name = v.product_name + ' ' + v.strength + ' ' + v.from;
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.fda_name + '</td>' +
                                '   <td>' + v.fda_form + '</td>' +
                                '   <td>' + v.fda_strength + '</td>' +
                                '   <td>' + v.fda_count + '</td>' +
                                '   <td>' + v.mfr + '</td>' +
                                '   <td>$' + v.best_price_today + '</td>' +
                                '   <td>' + v.date_created + '</td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#txrade-data').html('').html(html);
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.responseJSON.message, 'error');
                }
            })
        }

        $('body').on('keyup', '#auburn-search', function (e) {
            search = $(this).val();
            auburnData();
        });

        function auburnData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                search: search,
                is_match:isMatch
            };
            $.ajax({
                url: "{{ url('auburn/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data)) {
                        $.each(response.data, function (i, v) {
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.fda_name + '</td>' +
                                '   <td>' + v.fda_form + '</td>' +
                                '   <td>' + v.fda_strength + '</td>' +
                                '   <td>' + v.fda_count + '</td>' +
                                '   <td>' + v.vendor + '</td>' +
                                '   <td>$' + v.price + '</td>' +
                                '   <td>' + v.date_created + '</td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#auburn-data').html('').html(html);
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.responseJSON.message, 'error');
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
