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

        $('#submit_form').submit(function (e) {
            e.preventDefault();
            showLoader();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ url('upload/file') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success == true) {
                        $('#submit_form')[0].reset();
                        hideLoader();
                    } else {
                        hideLoader();
                        swal('Error', response.message, 'error');
                    }
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.statusText, 'error');
                }
            })
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
