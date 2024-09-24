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

    //DATATABLE DEPARTEMEN
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "divisi" },
        { data: "aksi" },
    ];

    var departemenTable = $("#departemen-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/departemen/datatable",
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
        departemenTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH DEPARTEMEN
    $('.btnAdd').on("click", function (){
        openDepartemen();
    })

    //CLOSE MODAL TAMBAH DEPARTEMEN
    $('.btnClose').on("click", function (){
        closeDepartemen();
    })


    // MODAL TAMBAH DEPARTEMEN
    var modalInputDepartemenOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputDepartemen = new bootstrap.Modal(
        document.getElementById("modal-input-departemen"),
        modalInputDepartemenOptions
    );

    function openDepartemen() {
        modalInputDepartemen.show();
    }

    function closeDepartemen() {
        $('#nama_departemen').val('');
        $('#id_divisi').val('');
        modalInputDepartemen.hide();
    }

    //SUBMIT TAMBAH DEPARTEMEN
    $('#form-tambah-departemen').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-departemen').attr('action');

        var formData = new FormData($('#form-tambah-departemen')[0]);
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
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    // MODAL EDIT DEPARTEMEN
    var modalEditDepartemenOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditDepartemen = new bootstrap.Modal(
        document.getElementById("modal-edit-departemen"),
        modalEditDepartemenOptions
    );

    function openEditDepartemen() {
        modalEditDepartemen.show();
    }

    function closeEditDepartemen() {
        $('#nama_departemen_edit').val('');
        $('#id_divisi_edit').val('');
        modalEditDepartemen.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditDepartemen();
    })

    //EDIT DEPARTEMEN
    $('#departemen-table').on('click', '.btnEdit', function (){
        var idDepartemen = $(this).data('id');
        var idDivisi = $(this).data('divisi-id');
        var nama = $(this).data('departemen-nama');
        $('#id_departemen_edit').val(idDepartemen);
        $('#nama_departemen_edit').val(nama);
        $('#id_divisi_edit').val(idDivisi);
        openEditDepartemen();
    });

    //SUBMIT EDIT DEPARTEMEN
    $('#form-edit-departemen').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idDepartemen = $('#id_departemen_edit').val();
        let url = base_url + '/master-data/departemen/update/' + idDepartemen;

        var formData = new FormData($('#form-edit-departemen')[0]);
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
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //DELETE DEPARTEMEN
    $('#departemen-table').on('click', '.btnDelete', function (){
        var idDepartemen = $(this).data('id');
        Swal.fire({
            title: "Delete Departemen",
            text: "Apakah kamu yakin untuk menghapus departemen ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/departemen/delete/' + idDepartemen;
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