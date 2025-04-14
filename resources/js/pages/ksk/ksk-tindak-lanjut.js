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

    var modalKontrakOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalKontrak = new bootstrap.Modal(
        document.getElementById("modal-kontrak"),
        modalKontrakOptions
    );

    function openKontrak() {
        modalKontrak.show();
    }

    function closeApproval() {
        modalKontrak.hide();
    }

    var modalTurnoverOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalTurnover = new bootstrap.Modal(
        document.getElementById("modal-turnover"),
        modalTurnoverOptions
    );

    function openTurnover() {
        modalTurnover.show();
    }

    function closeTurnover() {
        modalTurnover.hide();
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

    function updateKskNotification(){
        $.ajax({
            url: base_url + '/ajax/ksk/get-ksk-notification',
            method: 'GET',
            success: function(response){
                $('.notification-tindak-lanjut').html(response.html_tindak_lanjut);
            }, error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    var columnsNeedActionTable = [
        { data: "id_detail_ksk" },
        { data: "nama_karyawan" },
        { data: "nama_departemen" },
        { data: "nama_jabatan" },
        { data: "nama_posisi" },
        { data: "tanggal_akhir_bekerja" },
        { data: "status" },
        { data: "aksi" },
    ];

    var needActionTable = $("#need-action-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/tindak-lanjut/datatable-need-action",
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
        columns: columnsNeedActionTable,
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
        { data: "id_detail_ksk" },
        { data: "nama_karyawan" },
        { data: "nama_departemen" },
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
            url: base_url + "/ksk/tindak-lanjut/datatable-history",
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


    function refreshTable() {
        var searchValueNeedAction = needActionTable.search();
        if (searchValueNeedAction) {
            needActionTable.search(searchValueNeedAction).draw();
        } else {
            needActionTable.search("").draw();
        }

        var searchValueHistory = historyTable.search();
        if (searchValueHistory) {
            historyTable.search(searchValueHistory).draw();
        } else {
            historyTable.search("").draw();
        }
    }

    $('.btnReload').on('click', function(){
        refreshTable();
    });

    $('.btnCloseTurnover').on('click', function(){
        closeTurnover();
    });

    $('#need-action-table').on('click', '.btnTurnover', function () {
        let idKSKDetail = $(this).data('id-ksk-detail');
        console.log(idKSKDetail)
        let namaKaryawan = $(this).data('nama-karyawan');
        let idKaryawan = $(this).data('karyawan-id');
        let statusKSK = $(this).data('status-ksk');
        let tanggalAkhirBekerja = $(this).data('tgl-akhir-bekerja');

        let option = new Option(namaKaryawan, idKaryawan, true, true);
        $('#karyawan_idTurnover').empty();
        $('#karyawan_idTurnover').append(option);
        $('#karyawan_idTurnover').select2({
            dropdownParent: $('#modal-turnover'),
        });
        $('#status_karyawanTurnover').val('');
        $('#status_karyawanTurnover').select2({
            dropdownParent: $('#modal-turnover'),
        });
        $('#tanggal_keluarTurnover').val('');
        $('#tanggal_keluarTurnover').val(tanggalAkhirBekerja).attr('readonly', true);

        $('#id_ksk_detailTurnover').val('');
        $('#id_ksk_detailTurnover').val(idKSKDetail);
        openTurnover();
    })

    $('#form-turnover').on('submit', function (e) {
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                // updateKskNotification();
                showToast({ title: data.message });
                refreshTable();
                closeTurnover();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
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
