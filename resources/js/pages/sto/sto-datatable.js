
$(function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    var columnsTable = [
        { data: 'customer_name' },
        { data: 'wh_name'},
        { data: 'locator_name'},
        { data: 'product_code' },
        { data: 'product_name' },
        { data: 'product_desc' },
        { data: 'model' },
        { data: 'qty_book' },
        { data: 'qty_count' },
        { data: 'balance' },
        { data: 'processed' },
    ];

    var stoTable = $("#table-hasil-sto").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/sto/compare/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                dataFilter.wh_id = $('#wh_id').val();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON.data) {
                    var error = jqXHR.responseJSON.data.error;
                } else {
                    var message = jqXHR.responseJSON.message;
                    var errorLine = jqXHR.responseJSON.line;
                    var file = jqXHR.responseJSON.file;
                }
            },
        },
        responsive: true,
        columns: columnsTable,
        dom: 'Bfrtip',
        buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
        ],
    });

    $('#submit-filter').on('click', function() {
        stoTable.search('').draw();
    });


    $('#wh_id').select2({
        ajax: {
            url: '/sto/input_hasil/get_wh_label',
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                };
            },
            cache: true,
        },
    });




});