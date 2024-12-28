// $('#table-hasil-sto').DataTable({
//     ajax: '/sto/compare/datatable',
//     columns: [
//         { data: 'no_label', name: 'no_label' },
//         { data: 'customer', name: 'customer' },
//         { data: 'part_code', name: 'part_code' },
//         { data: 'part_name', name: 'part_name' },
//         { data: 'part_desc', name: 'part_desc' },
//         { data: 'wh_name', name: 'wh_name' },
//         { data: 'quantity', name: 'quantity' },
//         { data: 'input_by', name: 'input_by' },
//     ]
// });

$(function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    var columnsTable = [
        { data: 'no_label' },
        { data: 'customer' },
        { data: 'part_code' },
        { data: 'part_name' },
        { data: 'part_desc' },
        { data: 'model' },
        { data: 'wh_name'},
        { data: 'quantity' },
        { data: 'identitas_lot' },
    ];

    var eventTable =
    $("#table-hasil-sto").DataTable({
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
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
        ],
    });




});