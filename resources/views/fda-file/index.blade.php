@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <h6 class="h6">Fda Files</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="submit_form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Product</label>
                                <input type="file" class="form-control" id="product"
                                       name="product"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>Package</label>
                                <input type="file" class="form-control" name="package" id="package"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>FDA NDC</label>
                                <input type="file" class="form-control" name="fda_ndc" id="fda_ndc"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm float-right">Submit</button>
                            </div>
                        </form>
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

        function showLoader() {
            $('#preloader').show();
            $(document.body).css('pointer-events', 'none');
        }

        function hideLoader() {
            $('#preloader').hide();
            $(document.body).css('pointer-events', 'all');
        }

        $('#submit_form').submit(function (e) {
            e.preventDefault();
            showLoader();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ url('fda/files') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoader();
                    if (response.success == true) {
                        $('#submit_form')[0].reset();
                        swal('Success', response.message, 'success');
                    } else {
                        swal('Error', response.message, 'error');
                    }
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.statusText, 'error');
                }
            })
        });

    </script>
@endsection
