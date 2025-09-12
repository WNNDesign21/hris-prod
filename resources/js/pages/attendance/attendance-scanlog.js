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

    function refreshTable() {
        var searchValue = scanlogTable.search();
        if (searchValue) {
            scanlogTable.search(searchValue).draw();
        } else {
            scanlogTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnDownload').on("click", function (){
        openDownload();
    })

    $('.btnClose').on("click", function (){
        closeDownload();
    })

    // MODAL
    var modalDownloadScanlogOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDownloadScanlog = new bootstrap.Modal(
        document.getElementById("modal-download-scanlog"),
        modalDownloadScanlogOptions
    );

    function openDownload() {
        modalDownloadScanlog.show();
    }

    function closeDownload() {
        modalDownloadScanlog.hide();
    }

    function resetDownload() {
        $('#start_date').val('');
        $('#end_date').val('');
    }

    $('#device_id').select2({
        dropdownParent: $('#modal-download-scanlog'),
        width: '100%',
    });

    $('#format').select2({
        dropdownParent: $('#modal-download-scanlog'),
        width: '100%',
    });

    $('.btnGetScanlog').on("click", function () {
        loadingSwalShow();
        let formData = new FormData($('#form-export-scanlog')[0]);
        let url = base_url + '/attendance/scanlog/download-scanlog';
        loadingSwalShow();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                loadingSwalClose();
                refreshTable();
                showToast({title: response.message, icon: 'success'});
            },
            error: function (xhr, status, error){
                loadingSwalClose();
                showToast({title: xhr.responseJSON.message, icon: 'error'});
            }
        });
    })

    $('.btnExport').on("click", function () {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        if (startDate == '' || endDate == '') {
            showToast({title: 'Please fill start date and end date', icon: 'error'});
            return;
        }

        if (startDate > endDate) {
            showToast({title: 'Start date must be less than end date', icon: 'error'});
            return;
        }

        if(startDate > new Date().toISOString().split('T')[0]) {
            showToast({title: 'Please choose start date less than today', icon: 'error'});
            return;
        }

        $('#form-export-scanlog').submit();
    });

    //DATATABLE
    var columnsTable = [
        { data: "karyawan" },
        { data: "pin" },
        { data: "verify" },
        { data: "scan_date" },
    ];

    var scanlogTable = $("#scanlog-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "ASC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/attendance/scanlog/datatable",
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
        columnDefs: [
            // {
            //     orderable: false,
            //     targets: [0, -1],
            // },
        ],
    })
});