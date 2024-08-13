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

    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "no" },
        { data: "id_karyawan" },
        { data: "nama_karyawan" },
        { data: "email_karyawan" },
        { data: "no_telp_karyawan" },
        { data: "posisi_karyawan" },
        { data: "nama_grup" },
        { data: "jenis_kontrak" },
        { data: "status_karyawan" },
        { data: "aksi" },
    ];

    var karyawanTable = $("#karyawan-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/karyawan/datatable",
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
        karyawanTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH KARYAWAN
    $('.btnAdd').on("click", function (){
        openSeksi();
    })

    //CLOSE MODAL TAMBAH KARYAWAN
    $('.btnClose').on("click", function (){
        closeSeksi();
    })


    // MODAL TAMBAH KARYAWAN
    var modalInputSeksiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputSeksi = new bootstrap.Modal(
        document.getElementById("modal-input-karyawan"),
        modalInputSeksiOptions
    );

    function openSeksi() {
        modalInputSeksi.show();
    }

    function closeSeksi() {
        $('#nama_karyawan').val('');
        $('#id_departemen').val('');
        modalInputSeksi.hide();
    }

    //SUBMIT TAMBAH KARYAWAN
    $('#form-tambah-karyawan').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-karyawan').attr('action');

        var formData = new FormData($('#form-tambah-karyawan')[0]);
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

    // MODAL EDIT KARYAWAN
    var modalEditSeksiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditSeksi = new bootstrap.Modal(
        document.getElementById("modal-edit-karyawan"),
        modalEditSeksiOptions
    );

    function openEditSeksi() {
        modalEditSeksi.show();
    }

    function closeEditSeksi() {
        $('#nama_karyawan_edit').val('');
        $('#id_departemen_edit').val('');
        modalEditSeksi.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditSeksi();
    })

    //EDIT KARYAWAN
    $('#karyawan-table').on('click', '.btnEdit', function (){
        var idKaryawan = $(this).data('id');
        var idDepartemen = $(this).data('departemen-id');
        var nama = $(this).data('karyawan-nama');
        $('#id_karyawan_edit').val(idKaryawan);
        $('#nama_karyawan_edit').val(nama);
        $('#id_departemen_edit').val(idDepartemen);
        openEditSeksi();
    });

    //SUBMIT EDIT KARYAWAN
    $('#form-edit-karyawan').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idKaryawan = $('#id_karyawan_edit').val();
        let url = base_url + '/master-data/karyawan/update/' + idKaryawan;

        var formData = new FormData($('#form-edit-karyawan')[0]);
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

    //DELETE KARYAWAN
    $('#karyawan-table').on('click', '.btnDelete', function (){
        var idKaryawan = $(this).data('id');
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
                var url = base_url + '/master-data/karyawan/delete/' + idKaryawan;
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