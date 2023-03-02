@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="h6">FDA</h6>
                    </div>
                    <div class="table-responsive">
                        <div class="form-group m-2">
                            <input class="form-control" type="search" id="search" placeholder="Search....">
                        </div>
                        <table class="table table-striped">
                            <thead>
                            <th>#</th>
                            <th>Ndc</th>
                            <th>Name</th>
                            <th>Strength</th>
                            <th>Form</th>
                            <th>Count</th>
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
        let search = '', page = 1;
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

        $(document).on("click", '.paq-pager ul.pagination a', function (e) {
            e.preventDefault();
            page = $(this).attr('href').split('page=')[1];
            getData();
        });

        $('body').on('keyup', '#search', function (e) {
            search = $(this).val();
            getData();
        });

        function getData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                page: page,
                per_page: 1000,
                search: search
            };
            $.ajax({
                url: "{{ url('fda/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data.fda)) {
                        $.each(response.data.fda.data, function (i, v) {
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.name + '</td>' +
                                '   <td>' + v.strength + ' ' + v.unit + '</td>' +
                                '   <td>' + v.dosage_form + '</td>' +
                                '   <td>' + v.count + '</td>' +
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
