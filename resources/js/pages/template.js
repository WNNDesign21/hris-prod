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

    //DATATABLE SEKSI
    var columnsTable = [
        { data: "nama" },
        { data: "type" },
        { data: "isActive" },
        { data: "aksi" },
    ];

    var templateTable = $("#template-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/template/datatable",
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
            // {
            //     targets: [],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         $(td).addClass("text-center");
            //     },
            // },
            // {
            //     targets: [0],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        templateTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH SEKSI
    $('.btnAdd').on("click", function (){
        openTemplate();
    })

    //CLOSE MODAL TAMBAH SEKSI
    $('.btnClose').on("click", function (){
        closeTemplate();
    })


    // MODAL TAMBAH SEKSI
    var modalInputTemplateOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputTemplate = new bootstrap.Modal(
        document.getElementById("modal-input-template"),
        modalInputTemplateOptions
    );

    function openTemplate() {
        modalInputTemplate.show();
    }

    function closeTemplate() {
        $('#nama_template').val('');
        $('#type_template').val('KONTRAK');
        $('#file_template').val('');
        modalInputTemplate.hide();
    }

    //SUBMIT TAMBAH SEKSI
    $('#form-tambah-template').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-template').attr('action');

        var formData = new FormData($('#form-tambah-template')[0]);
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
                refreshTable();
                closeTemplate();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    // MODAL EDIT SEKSI
    var modalEditTemplateOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditTemplate = new bootstrap.Modal(
        document.getElementById("modal-edit-template"),
        modalEditTemplateOptions
    );

    function openEditTemplate() {
        modalEditTemplate.show();
    }

    function closeEditTemplate() {
        $('#nama_template_edit').val('');
        $('#type_template_edit').val('KONTRAK');
        $('#isactive_template_edit').val('');
        $('#file_template_edit').val('');
        modalEditTemplate.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditTemplate();
    })

    //EDIT SEKSI
    $('#template-table').on('click', '.btnEdit', function (){
        var idTemplate = $(this).data('id');
        var type = $(this).data('type');
        var nama = $(this).data('template-nama');
        var isActive = $(this).data('isactive');
        $('#id_template_edit').val(idTemplate);
        $('#nama_template_edit').val(nama);
        $('#type_template_edit').val(type);
        $('#isactive_template_edit').val(isActive);
        openEditTemplate();
    });

    //SUBMIT EDIT SEKSI
    $('#form-edit-template').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idTemplate = $('#id_template_edit').val();
        let url = base_url + '/master-data/template/update/' + idTemplate;

        var formData = new FormData($('#form-edit-template')[0]);
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
                refreshTable();
                closeEditTemplate();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //DELETE SEKSI
    $('#template-table').on('click', '.btnDelete', function (){
        var idTemplate = $(this).data('id');
        Swal.fire({
            title: "Delete Template",
            text: "Apakah kamu yakin untuk menghapus template ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/template/delete/' + idTemplate;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
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