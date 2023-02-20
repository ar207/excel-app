@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <h6 class="h6">ODBC</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price</th>
                                </thead>
                                <tbody id="page-data"></tbody>
                            </table>
                            <div class="paq-pager"></div>
                        </div>
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
                    console.log(response.data);
                    if (response.data) {

                    }
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
