$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    //DATATABLE
    var columnsTable = [
        { data: "karyawan" },
        { data: "pin" },
        { data: "verify" },
        { data: "scan_date" },
    ];

    var liveTable = $("#data-table").DataTable({
        search: {
            return: true,
        },
        order: [[2, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/live-attendance/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON.data) {
                    var error = jqXHR.responseJSON.data.error;
                    Swal.fire({
                        icon: "error",
                        title: " <br>Application error!",
                        html:
                            '<div class="alert alert-danger text-left" role="alert">' +
                            "<p>Error Message: <strong>" +
                            error +
                            "</strong></p>" +
                            "</div>",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    }).then(function () {
                        refreshTable();
                    });
                } else {
                    var message = jqXHR.responseJSON.message;
                    var errorLine = jqXHR.responseJSON.line;
                    var file = jqXHR.responseJSON.file;
                    Swal.fire({
                        icon: "error",
                        title: " <br>Application error!",
                        html:
                            '<div class="alert alert-danger text-left" role="alert">' +
                            "<p>Error Message: <strong>" +
                            message +
                            "</strong></p>" +
                            "<p>File: " +
                            file +
                            "</p>" +
                            "<p>Line: " +
                            errorLine +
                            "</p>" +
                            "</div>",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    }).then(function () {
                        refreshTable();
                    });
                }
            },
        },
        columns: columnsTable,
    })

    function refreshTable() {
        var searchValue = liveTable.search();
        if (searchValue) {
            liveTable.search(searchValue).draw();
        } else {
            liveTable.search("").draw();
        }
    }
    
    window.Echo.channel('live-attendance')
    .listen('LiveAttendanceEvent', (e) => {
        refreshTable();
    });
});