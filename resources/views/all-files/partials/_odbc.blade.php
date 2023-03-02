<div class="table-responsive">
    <div class="form-group">
        <input type="search" class="form-control" id="odbc-search" placeholder="Search...">
    </div>
    <table class="table table-striped">
        <thead>
        <th>#</th>
        <th>Product no</th>
        <th>Ndc</th>
        <th>Description</th>
        <th>Vendor</th>
        <th>Price</th>
        </thead>
        <tbody id="odbc-data"></tbody>
    </table>
</div>
@section('partial_scripts')
    <script>
        $('body').on('keyup', '#odbc-search', function (e) {
            search = $(this).val();
            odbcData();
        });

        function odbcData() {
            showLoader();
            const formData = {
                '_token': "{{ csrf_token() }}",
                search: search
            };
            $.ajax({
                url: "{{ url('odbc/all/data') }}",
                type: 'get',
                data: formData,
                success: function (response) {
                    let html = '', count = 1;
                    if (!empty(response.data)) {
                        $.each(response.data, function (i, v) {
                            html += '<tr>' +
                                '   <td>' + count + '</td>' +
                                '   <td>' + v.product_no + '</td>' +
                                '   <td>' + v.ndc + '</td>' +
                                '   <td>' + v.desc_one + '</td>' +
                                '   <td>' + v.vendor + '</td>' +
                                '   <td>$' + v.list_price + '</td>' +
                                '</tr>';
                            count++;
                        });
                    }
                    $('#odbc-data').html('').html(html);
                    hideLoader();
                },
                error: function (error) {
                    hideLoader();
                    swal('Error', error.responseJSON.message, 'error');
                }
            })
        }
    </script>
@endsection
