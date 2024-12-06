import { load } from "@eonasdan/tempus-dominus/dist/plugins/fa-five";

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

    function updateNotification(){
        $.ajax({
            url: base_url + '/get-notification',
            method: 'GET',
            success: function(response){
                $('.notifications-menu').html(response.data);
            }
        })
    }

    // function updatePengajuanCutiNotification(){
    //     $.ajax({
    //         url: base_url + '/get-lapor-skd-notification',
    //         method: 'GET',
    //         success: function(response){
    //             $('.notification-lapor-skd').html(response.data);
    //         }
    //     })
    // }
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "lampiran" },
        { data: "approved_by" },
        { data: "legalized_by" },
        { data: "aksi" },
    ];

    var laporSkdTable = $("#lapor-skd-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/lapor-skd-datatable",
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
        initComplete: function () {
            $('.image-popup-vertical-fit').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                image: {
                    verticalFit: true
                }
            });
        },
        drawCallback: function () { 
            $('.image-popup-vertical-fit').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                image: {
                    verticalFit: true
                }
            });
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
        laporSkdTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openInputForm();
    })

    $('.btnClose').on("click", function (){
        closeInputForm();
    })

    $('.btnEdit').on("click", function (){
        openEditForm();
    })

    $('.btnCloseEdit').on("click", function (){
        resetFormEdit();
        closeEditForm();
    })

    function resetForm() {
        $('#tanggal_mulai').val('');
        $('#tanggal_selesai').val('');
        $('#lampiran_skd').val('');
        $('#keterangan').val('');
        $("#linkFoto").attr(
            "href",
            base_url + "/img/no-image.png"
        );
        $("#imageReview").attr(
            "src",
            base_url + "/img/no-image.png"
        );
    }

    function resetFormEdit() {
        $('#tanggal_mulaiEdit').val('');
        $('#tanggal_selesaiEdit').val('');
        $('#lampiran_skdEdit').val('');
        $('#keteranganEdit').val('');
        $("#linkFotoEdit").attr(
            "href",
            base_url + "/img/no-image.png"
        );
        $("#imageReviewEdit").attr(
            "src",
            base_url + "/img/no-image.png"
        );
    }

    // MODAL TAMBAH KARYAWAN
    var modalInputLaporSkdOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputLaporSkd = new bootstrap.Modal(
        document.getElementById("modal-lapor-skd"),
        modalInputLaporSkdOptions
    );

    function openInputForm() {
        modalInputLaporSkd.show();
    }

    function closeInputForm() {
        modalInputLaporSkd.hide();
    }

    var modalEditLaporSkdOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditLaporSkd = new bootstrap.Modal(
        document.getElementById("modal-lapor-skd-edit"),
        modalEditLaporSkdOptions
    );

    function openEditForm() {
        modalEditLaporSkd.show();
    }

    function closeEditForm() {
        modalEditLaporSkd.hide();
    }

    //INPUT
    $('#tanggal_mulai').on('change', function(){
        let tanggal_mulai = $(this).val();
        if(tanggal_mulai > $('#tanggal_selesai').val()){
            $('#tanggal_selesai').val('');
            $('#tanggal_selesai').attr('min', tanggal_mulai);
        }
    });

    $('#tanggal_selesai').on('change', function(){
        let tanggal_selesai = $(this).val();
        if(tanggal_selesai < $('#tanggal_mulai').val()){
            $(this).val('');
        }
    })

    //EDIT
    $('#tanggal_mulaiEdit').on('change', function(){
        let tanggal_mulai = $(this).val();
        if(tanggal_mulai > $('#tanggal_selesaiEdit').val()){
            $('#tanggal_selesaiEdit').val('');
            $('#tanggal_selesaiEdit').attr('min', tanggal_mulai);
        }
    });

    $('#tanggal_selesaiEdit').on('change', function(){
        let tanggal_selesai = $(this).val();
        if(tanggal_selesai < $('#tanggal_mulaiEdit').val()){
            $(this).val('');
        }
    })

    $("#btnResetLampiranSkd").on("click", function () {
        const input = document.getElementById("lampiran_skd");
        input.value = "";
        $("#linkFoto").attr(
            "href",
            base_url + "/img/no-image.png"
        );
        $("#imageReview").attr(
            "src",
            base_url + "/img/no-image.png"
        );
    });

    $("#btnResetLampiranSkdEdit").on("click", function () {
        const input = document.getElementById("lampiran_skdEdit");
        input.value = "";
        $("#linkFotoEdit").attr(
            "href",
            base_url + "/img/no-image.png"
        );
        $("#imageReviewEdit").attr(
            "src",
            base_url + "/img/no-image.png"
        );
    });


    //UPLOAD IMAGE WHILE FILE
    $("#btnUploadLampiranSkd").on("click", function () {
        let input = document.getElementById("lampiran_skd");
        input.click();

        $("#lampiran_skd").on("change", function () {
            var linkFoto = "linkFoto";
            var review = "imageReview";

            compressAndDisplayImageSave(this, review, linkFoto);
        });
    });

    $("#btnUploadLampiranSkdEdit").on("click", function () {
        let input = document.getElementById("lampiran_skdEdit");
        input.click();

        $("#lampiran_skdEdit").on("change", function () {
            var linkFoto = "linkFotoEdit";
            var review = "imageReviewEdit";

            compressAndDisplayImageSave(this, review, linkFoto);
        });
    });

    function readURL(input, review, linkFoto) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#" + linkFoto).attr("href", e.target.result);
                $("#" + review).attr("src", e.target.result);
                $(input).next(".custom-file-label").html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]); 
        }
    }

    function compressAndDisplayImageSave(input, review, linkFoto ) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
      
          reader.onload = function (e) {
            var image = new Image();
            image.onload = function () {
              var canvas = document.createElement("canvas");
              var ctx = canvas.getContext("2d");
      
              const maxWidth = 1280;
              const maxHeight = 720; 
              const ratio = Math.min(maxWidth / image.width, maxHeight / image.height);
              canvas.width = Math.round(image.width * ratio);
              canvas.height = Math.round(image.height * ratio);
              ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
      
                const quality = 0.8; 
                const compressedImageData = canvas.toDataURL("image/jpeg", quality);
                const blob = dataURItoBlob(compressedImageData);

                const fileName = input.files[0].name.split('.')[0] + '-compressed.jpg';
                const fileType = input.files[0].type; 
                const compressedFile = new File([blob], fileName, {type: fileType});

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedFile);
                input.files = dataTransfer.files;
        
                $("#" + linkFoto).attr("href", compressedImageData);
                $("#" + review).attr("src", compressedImageData);

                let formData = new FormData();
                formData.append('lampiran_skd', compressedFile);
            };
      
            image.src = e.target.result;
          };
      
          reader.readAsDataURL(input.files[0]);
        }
    }

    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeType = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const intArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
          intArray[i] = byteString.charCodeAt(i);
        }
        const blob = new Blob([arrayBuffer], { type: mimeType });
        return blob;
    }

    $('#form-lapor-skd').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-lapor-skd').attr('action');
        var formData = new FormData($('#form-lapor-skd')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                updateNotification();
                showToast({ title: data.message });
                refreshTable();
                closeInputForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#form-lapor-skd-edit').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let idSakit = $('#id_sakitEdit').val();
        let url = base_url + '/izine/lapor-skd/update/' + idSakit;
        var formData = new FormData($('#form-lapor-skd-edit')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                updateNotification();
                showToast({ title: data.message });
                refreshTable();
                closeEditForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#lapor-skd-table').on('click', '.btnEdit', function (){
        loadingSwalShow();
        let idSakit = $(this).data('id-sakit');
        $.ajax({
            url: base_url + '/izine/lapor-skd/get-data-sakit/' + idSakit,
            method: 'GET',
            success: function(response){
                openEditForm();
                $('#tanggal_mulaiEdit').val(response.data.tanggal_mulai);
                $('#tanggal_selesaiEdit').val(response.data.tanggal_selesai);
                $('#keteranganEdit').val(response.data.keterangan);
                $('#id_sakitEdit').val(response.data.id_sakit);
                $("#linkFotoEdit").attr(
                    "href",
                    response.data.attachment
                );
                $("#imageReviewEdit").attr(
                    "src",
                    response.data.attachment
                );
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    })

    $('#lapor-skd-table').on('click', '.btnDelete', function (){
        var idSakit = $(this).data('id-sakit');
        Swal.fire({
            title: "Delete Laporan SKD",
            text: "Apakah kamu yakin untuk menghapus Laporan SKD ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/izine/lapor-skd/delete/' + idSakit;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        loadingSwalClose();
                        refreshTable();
                        showToast({ title: data.message });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })
});