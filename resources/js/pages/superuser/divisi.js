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

    //DATATABLE DIVISI
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "aksi" },
    ];

    var divisiTable = $("#divisi-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/superuser/divisi/datatable",
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
        divisiTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH DIVISI
    $('.btnAdd').on("click", function (){
        openDivisi();
    })

    //CLOSE MODAL TAMBAH DIVISI
    $('.btnClose').on("click", function (){
        closeDivisi();
    })


    // MODAL TAMBAH DIVISI
    var modalInputDivisiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputDivisi = new bootstrap.Modal(
        document.getElementById("modal-input-divisi"),
        modalInputDivisiOptions
    );

    function openDivisi() {
        modalInputDivisi.show();
    }

    function closeDivisi() {
        $('#nama_divisi').val('');
        modalInputDivisi.hide();
    }

    //SUBMIT TAMBAH DIVISI
    $('#form-tambah-divisi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-divisi').attr('action');

        var formData = new FormData($('#form-tambah-divisi')[0]);
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

    // MODAL EDIT DIVISI
    var modalEditDivisiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditDivisi = new bootstrap.Modal(
        document.getElementById("modal-edit-divisi"),
        modalEditDivisiOptions
    );

    function openEditDivisi() {
        modalEditDivisi.show();
    }

    function closeEditDivisi() {
        $('#nama_divisi_edit').val('');
        modalEditDivisi.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditDivisi();
    })

    //EDIT DIVISI
    $('#divisi-table').on('click', '.btnEdit', function (){
        var idDivisi = $(this).data('id');
        var nama = $(this).data('divisi-nama');
        $('#id_divisi_edit').val(idDivisi);
        $('#nama_divisi_edit').val(nama);
        openEditDivisi();
    });

    //SUBMIT EDIT DIVISI
    $('#form-edit-divisi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idDivisi = $('#id_divisi_edit').val();
        let url = base_url + '/superuser/divisi/update/' + idDivisi;

        var formData = new FormData($('#form-edit-divisi')[0]);
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

    //DELETE DIVISI
    $('#divisi-table').on('click', '.btnDelete', function (){
        var idDivisi = $(this).data('id');
        Swal.fire({
            title: "Delete Divisi",
            text: "Apakah kamu yakin untuk menghapus divisi ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/superuser/divisi/delete/' + idDivisi;
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
