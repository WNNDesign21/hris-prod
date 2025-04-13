$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    //LOADING SWALL
    let loadingSwal;
    function loadingSwalShow() {
        loadingSwal = Swal.fire({
            imageHeight: 300,
            showConfirmButton: false,
            title: '<i class="fas fa-sync-alt fa-spin fs-80"></i>',
            allowOutsideClick: false,
            background: 'rgba(0, 0, 0, 0)'
          });
    }

    function loadingSwalClose() {
        loadingSwal.close();
    }

    // var modalApprovalOptions = {
    //     backdrop: true,
    //     keyboard: false,
    // };

    // var modalApproval = new bootstrap.Modal(
    //     document.getElementById("modal-approval"),
    //     modalApprovalOptions
    // );

    // function openApproval() {
    //     modalApproval.show();
    // }

    // function closeApproval() {
    //     modalApproval.hide();
    // }

    // var modalDetailOptions = {
    //     backdrop: true,
    //     keyboard: false,
    // };

    // var modalDetail = new bootstrap.Modal(
    //     document.getElementById("modal-detail"),
    //     modalDetailOptions
    // );

    // function openDetail() {
    //     modalDetail.show();
    // }

    // function closeDetail() {
    //     modalDetail.hide();
    // }

    //SHOW TOAST
    function showToast(options) {
        const toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
            }
        });

        toast.fire({
            icon: options.icon || "success",
            title: options.title
        });
    }

    function updateKskNotification(){
        $.ajax({
            url: base_url + '/ajax/ksk/get-ksk-notification',
            method: 'GET',
            success: function(response){
                $('.notification-release').html(response.html_release);
                $('.notification-approval').html(response.html_approval);
                $('.notification-cleareance').html(response.html_cleareance);
            }, error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    var columnsMustApprovedTable = [
        { data: "cleareance_id" },
        { data: "nama_karyawan" },
        { data: "nama_departemen"},
        { data: "nama_jabatan" },
        { data: "nama_posisi" },
        { data: "tanggal_akhir_bekerja" },
        { data: "status" },
    ];

    var mustApprovedTable = $("#must-approved-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/cleareance/approval/datatable-must-approved",
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
                    })
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
                    })
                }
            },
        },
        // responsive: true,
        scrollX: true,
        columns: columnsMustApprovedTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    var columnsHistoryTable = [
        { data: "cleareance_id" },
        { data: "nama_karyawan" },
        { data: "nama_departemen"},
        { data: "nama_jabatan" },
        { data: "nama_posisi" },
        { data: "tanggal_akhir_bekerja" },
        { data: "status" },
    ];

    var historyTable = $("#history-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/cleareance/approval/datatable-history",
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
                    })
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
                    })
                }
            },
        },
        // responsive: true,
        scrollX: true,
        columns: columnsHistoryTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    // //REFRESH TABLE
    function refreshTable() {
        var searchValueMustApproved = mustApprovedTable.search();
        if (searchValueMustApproved) {
            mustApprovedTable.search(searchValueMustApproved).draw();
        } else {
            mustApprovedTable.search("").draw();
        }

        var searchValueHistory = historyTable.search();
        if (searchValueHistory) {
            historyTable.search(searchValueHistory).draw();
        } else {
            historyTable.search("").draw();
        }
    }

    $('.btnReload').on('click', function() {
        refreshTable();
    });

    $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        var target = $(e.target).attr("href");
        if ($(target).find("table").hasClass("dataTable")) {
            if (!$.fn.DataTable.isDataTable($(target).find("table"))) {
                $(target).find("table").DataTable();
            } else {
                $(target).find("table").DataTable().columns.adjust().draw();
            }
        }
    });
});
