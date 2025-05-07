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

    //DATATABLE
    var columnsTable = [
        { data: "departemen" },
        { data: "karyawan" },
        { data: "pin" },
        { data: "current_shift" },
        { data: "pola_shift" },
        { data: "aksi" },
    ];

    var shiftgroupTable = $("#shiftgroup-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/attendance/shift-group/datatable",
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
            {
                orderable: false,
                targets: [0, -1],
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = shiftgroupTable.search();
        if (searchValue) {
            shiftgroupTable.search(searchValue).draw();
        } else {
            shiftgroupTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnUpload').on("click", function (){
        openUpload();
    })

    $('.btnClose').on("click", function (){
        closeUpload();
    })

    $('.btnTemplate').on('click', function () {
        window.location.href = base_url + '/template/template_upload_shiftgroup.xlsx';
    });

    // MODAL
    var modalUploadOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalUpload = new bootstrap.Modal(
        document.getElementById("modal-upload-shiftgroup"),
        modalUploadOptions
    );

    function openUpload() {
        modalUpload.show();
    }

    function closeUpload() {
        modalUpload.hide();
    }

    var modalEditShiftgroupOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditShiftgroup = new bootstrap.Modal(
        document.getElementById("modal-edit-shiftgroup"),
        modalEditShiftgroupOptions
    );

    function openShiftgroupEdit() {
        modalEditShiftgroup.show();
    }

    function closeShiftgroupEdit() {
        modalEditShiftgroup.hide();
        resetShiftgroupEdit();
    }

    $('#grup_pattern_edit').select2({
        dropdownParent: $('#modal-edit-shiftgroup'),
    });

    $('#shiftgroup-table').on("click", '.btnEdit', function (){
        loadingSwalShow();
        let idKaryawan = $(this).data('id-karyawan');
        let idGrup = $(this).data('id-grup');
        let idGrupPattern = $(this).data('id-grup-pattern');
        // let pin = $(this).data('pin');

        let url = base_url + '/attendance/shift-group/update/' + idKaryawan;
        $('#form-edit-shiftgroup').attr('action', url);

        $('#grup_pattern_edit').off('change').on('change', function (){
            let idGrupPattern = $(this).val();
            $('#current_shift').removeClass('d-none');
            if (idGrupPattern == '') {
                $('#current_shift').addClass('d-none');
                $('#grup_edit').empty();
                return;
            }
            let url = base_url + '/attendance/shift-group/get-data-grup-pattern/' + idGrupPattern;
            $.ajax({
                url: url,
                method: "GET",
                dataType: "JSON",
                success: function (response) {
                    let data = response.data;
                    let select = $('#grup_edit').empty();
                    select.append('<option value="">Pilih Grup</option>');
                    $.each(data, function (key, value){
                        select.append('<option value="' + value.id_grup + '">' + value.nama + ' (' +value.jam_masuk+ ' - ' + value.jam_keluar + ')' + '</option>');
                    });

                    select.select2({
                        dropdownParent: $('#modal-edit-shiftgroup'),
                    });

                    $('#grup_edit').val(idGrup).trigger('change');
                    loadingSwalClose();
                    openShiftgroupEdit();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    loadingSwalClose();
                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                }
            })
        })

        $('#grup_pattern_edit').val(idGrupPattern).trigger('change');
    })

    $('.btnCloseEdit').on("click", function (){
        closeShiftgroupEdit();
    })

    function resetShiftgroupEdit() {
        $('#grup_edit').empty()
        $('#grup_pattern_edit').val('').trigger('change');
        $('#form-edit-shiftgroup').attr('action', '#');
    }

    $('#form-upload-shiftgroup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-upload-shiftgroup')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
                closeUpload();
                $('#file').val('');
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#form-edit-shiftgroup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-edit-shiftgroup')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
                closeShiftgroupEdit();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })
});
