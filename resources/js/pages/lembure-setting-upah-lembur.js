$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    // ALERT & LOADING
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

    function showToast(options) {
        const toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000, 
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

    // DATATABLE
    var columnsTable = [
        { data: "ni_karyawan" },
        { data: "divisi" },
        { data: "departemen" },
        { data: "nama" },
        { data: "gaji" },
    ];

    var settingUpahTable = $("#setting-upah-lembur-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/setting-upah-lembur-datatable",
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
        responsive: true,
        columns: columnsTable,
        columnDefs: [
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center");
                },
            },
        ],
    })

    function refreshTable() {
        settingUpahTable.search("").draw();
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('#setting-upah-lembur-table').on('input', '.inputUpdahLembur', function () {
        let inputValue = $(this).val();

        if (!/^\d+$/.test(inputValue)) {
            $(this).val('');
        } else if (inputValue.length > 1 && inputValue.startsWith('0')) {
            $(this).val(inputValue.replace(/^0+/, ''));
        }
    })

    $('#setting-upah-lembur-table').on('click', '.updateUpahLembur', function (){
        loadingSwalShow();
        let idSettingLemburKaryawan = $(this).data('id-setting-lembur-karyawan');
        let karyawanId = $(this).data('karyawan-id');
        let formData = new FormData();
        formData.append('id_setting_lembur_karyawan', idSettingLemburKaryawan);
        formData.append('gaji', $(this).closest('tr').find('.inputUpdahLembur').val());
        formData.append('karyawan_id', karyawanId);
        formData.append('_method', 'PATCH');

        let url = base_url + '/lembure/setting-upah-lembur/update';
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    $('.btnTemplate').on('click', function () {
        window.location.href = base_url + '/template/template_upload_gaji_lembur_karyawan.xlsx';
    });

    $('.btnUpload').on('click', function (){
        let input = $('#upload-upah-lembur');
        input.click();

        input.on("change", function () {
            Swal.fire({
                title: "Upload Gaji Lembur Karyawan",
                text: "Gaji yang sudah ada akan terupdate, dan yang belum ada akan ditambahkan, yakin ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Upload it!",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    loadingSwalShow();
                    const url = base_url + "/lembure/upload-upah-lembur-karyawan";
                    let formData = new FormData();
                    formData.append('upah_lembur_karyawan_file', input[0].files[0]);

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            input.val('');
                            showToast({ title: data.message });
                            loadingSwalClose();
                            refreshTable();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            input.val('');
                            loadingSwalClose();
                            showToast({ icon: "error", title: jqXHR.responseJSON.message });
                        },
                    })
                } else {
                    input.val('');
                } 
            });
        });
    });

});