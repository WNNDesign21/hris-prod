import { Html5Qrcode } from "html5-qrcode";

$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let cameraId = null;
    const html5QrCode = new Html5Qrcode("qr-scanner");
    const config = { fps: 10, qrbox: { width: 350, height: 350 } };
    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    getCameraPermission();

    //LOADING SWALL
    let loadingSwal;

    //FUNCTION
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
    //END FUNCTION

    //DATATABLE
    var izinColumnsTable = [
        { data: "id_izin" },
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "rencana" },
        { data: "aktual" },
        { data: "jenis_izin" },
        { data: "keterangan" }
    ];

    var izinTable = $("#izin-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/security/datatable/izin",
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
                        refreshTableIzin();
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
                        refreshTableIzin();
                    });
                }
            },
        },
        // responsive: true,
        scrollX: true,
        columns: izinColumnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    var tlColumnsTable = [
        { data: "id_tugasluar" },
        { data: "karyawan" },
        { data: "tanggal" },
        { data: "kendaraan" },
        { data: "pergi" },
        { data: "kembali" },
        { data: "km_awal" },
        { data: "km_akhir" },
        { data: "km_selisih" },
        { data: "rute" },
        { data: "keterangan" },
        { data: "status" },
        { data: "checked" },
        { data: "legalized" },
        { data: "aksi" },
    ];

    var tlTable = $("#tl-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/tugasluare/approval/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let nopolFilter = $('#nopolFilter').val();
                let statusFilter = $('#statusFilter').val();
                let fromFilter = $('#fromFilter').val();
                let toFilter = $('#toFilter').val();
                // let departemenFilter = '';

                dataFilter.nopol = nopolFilter;
                dataFilter.status = statusFilter;
                dataFilter.from = fromFilter;
                dataFilter.to = toFilter;
                // dataFilter.departemen = departemenFilter;
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

        columns: tlColumnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
        ],
    })
    //END DATATABLE

    //REFRESH TABLE
    function refreshTableIzin() {
        var searchValue = izinTable.search();
        if (searchValue) {
            izinTable.search(searchValue).draw();
        } else {
            izinTable.search("").draw();
        }
    }

    function refreshTableTl() {
        var searchValue = tlTable.search();
        if (searchValue) {
            tlTable.search(searchValue).draw();
        } else {
            tlTable.search("").draw();
        }
    }
    //RELOAD TABLE
    
    //SCANNER USING CAMERA
    function getCameraPermission(){
        if(!cameraId){
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const rearCamera = devices[1] || devices[0];
                    cameraId = rearCamera.id;
                    startScanning();
                }
            }).catch(err => {
                Swal.fire({
                    title: "Camera Not Found",
                    text: "Beri akses kamera untuk memulai scan qrcode,",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#0052cc",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "OK",
                    allowOutsideClick: false,
                }).then((result) => {
                    closeQrScanner();
                });
            });
        } else {
            startScanning();    
        }
    }

    function startScanning(){
        if(!cameraId) {
            Swal.fire({
                title: "Camera Not Found",
                text: "Beri akses kamera untuk memulai scan qrcode,",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#0052cc",
                cancelButtonColor: "#d33",
                confirmButtonText: "OK",
                allowOutsideClick: false,
            }).then((result) => {
                closeQrScanner();
                return;
            });
        }
        
        html5QrCode.start(
            cameraId, 
            config,
            (decodedText, decodedResult) => {
                loadingSwalShow();
                var url = base_url + '/security/get-qr-detail/' + decodedText;
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (response) {
                        let html = response.html;
                        setTimeout(() => {
                            loadingSwalClose();
                            Swal.fire({
                                title: "Verifikasi Data",
                                text: "Data yang sudah di konfirmasi tidak dapat diubah.",
                                icon: "info",
                                html: html,
                                showCancelButton: true,
                                confirmButtonColor: "#0052cc",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, Konfirmasi Data Ini!",
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.value) {
                                    loadingSwalShow();
                                    let url = base_url + '/izine/log-book-izin/confirmed/' + decodedText;
                                    var formData = new FormData();
                                    formData.append('_method', 'PATCH');
                                    $.ajax({
                                        url: url,
                                        data : formData,
                                        method:"POST",
                                        contentType: false,
                                        processData: false,
                                        dataType: "JSON",
                                        success: function (data) {
                                            loadingSwalClose();
                                            showToast({ title: data.message });
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            loadingSwalClose();
                                            showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                            html5QrCode.clear();
                                            startScanning();
                                        },
                                    });
                                } else {
                                    html5QrCode.clear();
                                    startScanning();
                                };
                            });
                        }, 1000);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose(); 
                        Swal.fire({
                            icon: "error",
                            title: jqXHR.responseJSON.message,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                        }).then((result) => {
                            if (result.value) {
                                html5QrCode.clear();
                                startScanning();
                            }
                        });
                    },
                });
                html5QrCode.stop();
            },
            (errorMessage) => {
            })
          .catch((err) => {
            showToast({ icon: "error", title: err });
          });
    }

    // EVENT
    $('.btnReloadIzin').on('click', function () {
        refreshTableIzin();
    });

    $('.btnReloadTl').on('click', function () {
        refreshTableTl();
    });
    // END EVENT
});