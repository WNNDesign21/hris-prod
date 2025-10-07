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

    //DATATABLE JABATAN
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "aksi" },
    ];

    var jabatanTable = $("#jabatan-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/jabatan/datatable",
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
        jabatanTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH JABATAN
    $('.btnAdd').on("click", function (){
        openJabatan();
    })

    //CLOSE MODAL TAMBAH JABATAN
    $('.btnClose').on("click", function (){
        closeJabatan();
    })


    // MODAL TAMBAH JABATAN
    var modalInputJabatanOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputJabatan = new bootstrap.Modal(
        document.getElementById("modal-input-jabatan"),
        modalInputJabatanOptions
    );

    function openJabatan() {
        modalInputJabatan.show();
    }

    function closeJabatan() {
        $('#nama_jabatan').val('');
        modalInputJabatan.hide();
    }

    //SUBMIT TAMBAH JABATAN
    $('#form-tambah-jabatan').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-jabatan').attr('action');

        var formData = new FormData($('#form-tambah-jabatan')[0]);
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

    // MODAL EDIT JABATAN
    var modalEditJabatanOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditJabatan = new bootstrap.Modal(
        document.getElementById("modal-edit-jabatan"),
        modalEditJabatanOptions
    );

    function openEditJabatan() {
        modalEditJabatan.show();
    }

    function closeEditJabatan() {
        $('#nama_jabatan_edit').val('');
        modalEditJabatan.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditJabatan();
    })

    //EDIT JABATAN
    $('#jabatan-table').on('click', '.btnEdit', function (){
        var idJabatan = $(this).data('id');
        var nama = $(this).data('jabatan-nama');
        $('#id_jabatan_edit').val(idJabatan);
        $('#nama_jabatan_edit').val(nama);
        openEditJabatan();
    });

    //SUBMIT EDIT JABATAN
    $('#form-edit-jabatan').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idJabatan = $('#id_jabatan_edit').val();
        let url = base_url + '/master-data/jabatan/update/' + idJabatan;

        var formData = new FormData($('#form-edit-jabatan')[0]);
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

    //DELETE JABATAN
    $('#jabatan-table').on('click', '.btnDelete', function (){
        var idJabatan = $(this).data('id');
        Swal.fire({
            title: "Delete Jabatan",
            text: "Apakah kamu yakin untuk menghapus jabatan ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/jabatan/delete/' + idJabatan;
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