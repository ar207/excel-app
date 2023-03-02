@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <h6 class="h6">Dashboard</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('message'))
                            <div class="alert alert-success" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form id="submit_form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Active Product Listing</label>
                                <input type="file" class="form-control" id="active_product_listing"
                                       name="active_product_listing"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>Cardinal health</label>
                                <input type="file" class="form-control" name="cardinal_health" id="cardinal_health"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>Export all products</label>
                                <input type="file" class="form-control" name="export_all" id="export_all"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>Top 500 Product Trending</label>
                                <input type="file" class="form-control" name="top_trending" id="top_trending"
                                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label>Auburn Pharmaceutical</label>
                                <input type="file" class="form-control" name="auburn_pharmaceutical" id="auburn_pharmaceutical"
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

    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog otp_modal_dailoge">
            <div class="modal-content otp_modal_content">
                <div class="modal-body text-center py-5">
                    <div class="d-flex align-items justify-content-between">
                        <div>
                            <p class="mb-0 text-capitalize text-muted font-12" id="percentage_count"></p>
                        </div>
                    </div>
                    <div class="progress border-radius-12" style="height: 25px;">
                        <div class="progress-bar border-radius-12" id="progressbar" role="progressbar"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm" id="progressModalBtn" data-dismiss="modal"
                            style="display: none;">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        let isProgressComplete = 0;
        let interval;
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert-success').fadeOut('slow');
            }, 2000);
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
                    swal('Error', error.responseJSON.message, 'error');
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

        function progressUpload() {
            let value = 10;
            $('#percentage_count').html('10%');
            $('#progressbar').css({'width': '10%', 'background': '#fffa61'});
            interval = window.setInterval(function () {
                value += 10;
                if (value == 30) {
                    $('#percentage_count').html('30%');
                    $('#progressbar').css({'width': '30%', 'background': '#ddd72f'});
                }
                if (value == 50) {
                    $('#percentage_count').html('50%');
                    $('#progressbar').css({'width': '50%', 'background': '#cadd2ffa'});
                }
                if (value == 60) {
                    $('#percentage_count').html('60%');
                    $('#progressbar').css({'width': '60%', 'background': '#b4ef25fa'});
                }
                if (value == 80) {
                    $('#percentage_count').html('80%');
                    $('#progressbar').css({'width': '80%', 'background': '#a8e72efa'});
                }
                if (value == 90) {
                    $('#percentage_count').html('90%');
                    $('#progressbar').css({'width': '90%', 'background': '#a1e730fa'});
                }
                if (value == 100) {
                    $('#percentage_count').html('100%');
                    $('#progressbar').css({'width': '100%', 'background': '#49db0cfa'});
                    if (isProgressComplete == 1) {
                        $(document.body).css('pointer-events', 'all');
                        $('#progressModalBtn').show();
                        $('#submit_form')[0].reset();
                        swal('Success', 'Files uploaded successfully', 'success');
                    }
                    clearInterval(interval);
                    value = 10;
                }
            }, 12000);
        }

        $('body').on('click', '#progressModalBtn', function () {
            $(this).hide();
        });
    </script>
@endsection
