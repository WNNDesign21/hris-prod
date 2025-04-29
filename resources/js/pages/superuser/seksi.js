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
        { data: "no" },
        { data: "nama" },
        { data: "departemen" },
        { data: "aksi" },
    ];

    var seksiTable = $("#seksi-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/superuser/seksi/datatable",
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
        seksiTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH SEKSI
    $('.btnAdd').on("click", function (){
        openSeksi();
    })

    //CLOSE MODAL TAMBAH SEKSI
    $('.btnClose').on("click", function (){
        closeSeksi();
    })


    // MODAL TAMBAH SEKSI
    var modalInputSeksiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputSeksi = new bootstrap.Modal(
        document.getElementById("modal-input-seksi"),
        modalInputSeksiOptions
    );

    function openSeksi() {
        modalInputSeksi.show();
    }

    function closeSeksi() {
        $('#nama_seksi').val('');
        $('#id_departemen').val('');
        modalInputSeksi.hide();
    }

    //SUBMIT TAMBAH SEKSI
    $('#form-tambah-seksi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-seksi').attr('action');

        var formData = new FormData($('#form-tambah-seksi')[0]);
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

    // MODAL EDIT SEKSI
    var modalEditSeksiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditSeksi = new bootstrap.Modal(
        document.getElementById("modal-edit-seksi"),
        modalEditSeksiOptions
    );

    function openEditSeksi() {
        modalEditSeksi.show();
    }

    function closeEditSeksi() {
        $('#nama_seksi_edit').val('');
        $('#id_departemen_edit').val('');
        modalEditSeksi.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditSeksi();
    })

    //EDIT SEKSI
    $('#seksi-table').on('click', '.btnEdit', function (){
        var idSeksi = $(this).data('id');
        var idDepartemen = $(this).data('departemen-id');
        var nama = $(this).data('seksi-nama');
        $('#id_seksi_edit').val(idSeksi);
        $('#nama_seksi_edit').val(nama);
        $('#id_departemen_edit').val(idDepartemen);
        openEditSeksi();
    });

    //SUBMIT EDIT SEKSI
    $('#form-edit-seksi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idSeksi = $('#id_seksi_edit').val();
        let url = base_url + '/superuser/seksi/update/' + idSeksi;

        var formData = new FormData($('#form-edit-seksi')[0]);
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

    //DELETE SEKSI
    $('#seksi-table').on('click', '.btnDelete', function (){
        var idSeksi = $(this).data('id');
        Swal.fire({
            title: "Delete Seksi",
            text: "Apakah kamu yakin untuk menghapus departemen ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/superuser/seksi/delete/' + idSeksi;
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
