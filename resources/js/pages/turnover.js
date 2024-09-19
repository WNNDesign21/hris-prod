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

    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "id_turnover" },
        { data: "karyawan_id" },
        { data: "nama" },
        { data: "status" },
        { data: "tanggal_keluar" },
        { data: "keterangan" },
    ];

    var turnoverTable = $("#turnover-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/turnover/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                console.log(dataFilter);
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
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        },

        columns: columnsTable,
        columnDefs: [
            // {
            //     orderable: false,
            //     targets: [-1,-2,-3],
            // },
            // {
            //     targets: [-2, -1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         $(td).addClass("text-center");
            //     },
            // },
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5', 
                text: 'Export to Excel',
                exportOptions: {
                    search: 'applied', 
                    order: 'applied' 
                }
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        turnoverTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //Karyawan Select2
    $('#karyawan_id').select2({
        ajax: {
            url: base_url + "/master-data/karyawan/get-data-karyawan",
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

    //Status Karyawan Select2
    $('#status_karyawan').select2();

    $('#form-input-turnover').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        Swal.fire({
            title: "Turn Over Karyawan",
            text: "Karyawan akan menjadi non-aktif dan tidak bisa mengakses akunnya lagi. Apakah anda yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Submit it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                let url = $('#form-input-turnover').attr('action');
                var formData = new FormData($('#form-input-turnover')[0]);
                $.ajax({
                    url: url,
                    data: formData,
                    method:"POST",
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (data) {
                        showToast({ title: data.message });
                        refreshTable();
                        loadingSwalClose();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                })
            } 
        });
    });
});