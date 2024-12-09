import { Html5Qrcode } from "html5-qrcode";
import { Html5QrcodeScanner } from "html5-qrcode";

$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    console.log(Html5Qrcode);

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

    var columnsTable = [
        { data: "id_izin" },
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "rencana" },
        { data: "aktual" },
        { data: "jenis_izin" },
        { data: "keterangan" }
    ];

    var logBookIzinTable = $("#log-book-izin-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/log-book-izin-datatable",
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
        // responsive: true,
        scrollX: true,
        columns: columnsTable,
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

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = logBookIzinTable.search();
        if (searchValue) {
            logBookIzinTable.search(searchValue).draw();
        } else {
            logBookIzinTable.search("").draw();
        }
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //QR SCANNER MODAL
     //QR CODE SYSTEM
     var modalQrScannerOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalQrScanner = new bootstrap.Modal(
        document.getElementById("modal-qr-scanner"),
        modalQrScannerOptions
    );

    function openQrScanner() {
        modalQrScanner.show();
    }

    function closeQrScanner() {
        modalQrScanner.hide();
        refreshTable();
    }

    function getCameraPermission(){
        if(!cameraId){
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraId = devices[0].id;
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
                    getCameraPermission();
                });
            });
        }
    }

    let cameraId = null;
    const html5QrCode = new Html5Qrcode("qr-scanner");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    getCameraPermission();

    // const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    //     loadingSwalShow();
    //     var url = base_url + '/izine/log-book-izin/get-qrcode-detail-izin/' + decodedText;
    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         success: function (response) {
    //             let data = response.data;
    //             setTimeout(() => {
    //                 loadingSwalClose();
    //                 Swal.fire({
    //                     title: "Verifikasi Data",
    //                     text: "Data yang sudah di konfirmasi tidak dapat diubah.",
    //                     icon: "info",
    //                     html: '<div class="alert alert-primary text-white" style="text-align:start;" role="alert">'
    //                     +'<p style="text-align:start;"><strong>ID Izin</strong> : '+data.id_izin+'</p>'
    //                     +'<p style="text-align:start;"><strong>Nama</strong> : '+data.nama+'</p>'
    //                     +'<p style="text-align:start;"><strong>Departemen</strong> : '+data.departemen+'</p>'
    //                     +'<p style="text-align:start;"><strong>Jenis Izin</strong> : '+data.jenis_izin+'</p>'
    //                     +'<p style="text-align:start;"><strong>Rencana</strong> : '+data.rencana+'</p>'
    //                     +'<p style="text-align:start;"><strong>Keterangan</strong> : '+data.keterangan+'</p>'
    //                     +'</div>',
    //                     showCancelButton: true,
    //                     confirmButtonColor: "#0052cc",
    //                     cancelButtonColor: "#d33",
    //                     confirmButtonText: "Yes, Konfirmasi Data Ini!",
    //                     allowOutsideClick: false,
    //                 }).then((result) => {
    //                     if (result.value) {
    //                         loadingSwalShow();
    //                         let url = base_url + '/izine/log-book-izin/confirmed/' + decodedText;
    //                         var formData = new FormData();
    //                         formData.append('_method', 'PATCH');
    //                         $.ajax({
    //                             url: url,
    //                             data : formData,
    //                             method:"POST",
    //                             contentType: false,
    //                             processData: false,
    //                             dataType: "JSON",
    //                             success: function (data) {
    //                                 loadingSwalClose();
    //                                 showToast({ title: data.message });
    //                                 closeQrScanner();
    //                             },
    //                             error: function (jqXHR, textStatus, errorThrown) {
    //                                 loadingSwalClose();
    //                                 showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //                                 html5QrCode.clear();
    //                                 startScanning();
    //                             },
    //                         });
    //                     } else {
    //                         html5QrCode.clear();
    //                         startScanning();
    //                     };
    //                 });
    //             }, 1000);
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             loadingSwalClose(); 
    //             Swal.fire({
    //                 icon: "error",
    //                 title: jqXHR.responseJSON.message,
    //                 allowOutsideClick: false,
    //                 showConfirmButton: true,
    //             }).then((result) => {
    //                 if (result.value) {
    //                     html5QrCode.clear();
    //                     startScanning();
    //                 }
    //             });
    //         },
    //     });
    //     html5QrCode.stop(); 
    // };
    
    $('.btnQrScanner').on("click", function (){
        startScanning();
        openQrScanner();
    });
    
    $('.btnCloseQrScanner').on("click", function (){
        closeQrScanner();
        html5QrCode.stop();
    });

    function startScanning(){
        // html5QrCode.start(cameraId, { facingMode: "environment" }, config, qrCodeSuccessCallback);
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
                var url = base_url + '/izine/log-book-izin/get-qrcode-detail-izin/' + decodedText;
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (response) {
                        let data = response.data;
                        setTimeout(() => {
                            loadingSwalClose();
                            Swal.fire({
                                title: "Verifikasi Data",
                                text: "Data yang sudah di konfirmasi tidak dapat diubah.",
                                icon: "info",
                                html: '<div class="alert alert-primary text-white" style="text-align:start;" role="alert">'
                                +'<p style="text-align:start;"><strong>ID Izin</strong> : '+data.id_izin+'</p>'
                                +'<p style="text-align:start;"><strong>Nama</strong> : '+data.nama+'</p>'
                                +'<p style="text-align:start;"><strong>Departemen</strong> : '+data.departemen+'</p>'
                                +'<p style="text-align:start;"><strong>Jenis Izin</strong> : '+data.jenis_izin+'</p>'
                                +'<p style="text-align:start;"><strong>Rencana</strong> : '+data.rencana+'</p>'
                                +'<p style="text-align:start;"><strong>Keterangan</strong> : '+data.keterangan+'</p>'
                                +'</div>',
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
                                            closeQrScanner();
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
                showToast({ icon: "error", title: errorMessage });
                closeQrScanner();
            })
          .catch((err) => {
            showToast({ icon: "error", title: err });
            closeQrScanner();
          });
    }
});