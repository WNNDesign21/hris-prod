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

    //DATATABLE GRUP
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "jam_masuk" },
        { data: "jam_keluar" },
        { data: "toleransi_waktu" },
        { data: "aksi" },
    ];

    var grupTable = $("#grup-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/grup/datatable",
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
        grupTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH GRUP
    $('.btnAdd').on("click", function (){
        openGrup();
    })

    //CLOSE MODAL TAMBAH GRUP
    $('.btnClose').on("click", function (){
        closeGrup();
    })


    // MODAL TAMBAH GRUP
    var modalInputGrupOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputGrup = new bootstrap.Modal(
        document.getElementById("modal-input-grup"),
        modalInputGrupOptions
    );

    function openGrup() {
        modalInputGrup.show();
    }

    function closeGrup() {
        $('#nama_grup').val('');
        $('#jam_masuk').val('');
        $('#jam_keluar').val('');
        $('#toleransi_waktu').val('');
        modalInputGrup.hide();
    }

    //SUBMIT TAMBAH GRUP
    $('#form-tambah-grup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-grup').attr('action');

        var formData = new FormData($('#form-tambah-grup')[0]);
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
                closeGrup();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    // MODAL EDIT GRUP
    var modalEditGrupOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditGrup = new bootstrap.Modal(
        document.getElementById("modal-edit-grup"),
        modalEditGrupOptions
    );

    function openEditGrup() {
        modalEditGrup.show();
    }

    function closeEditGrup() {
        $('#id_grup_edit').val('');
        $('#nama_grup_edit').val('');
        $('#jam_masuk_edit').val('');
        $('#jam_keluar_edit').val('');
        $('#toleransi_waktu_edit').val('');
        modalEditGrup.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditGrup();
    })

    //EDIT GRUP
    $('#grup-table').on('click', '.btnEdit', function (){
        var idGrup = $(this).data('id');
        var nama = $(this).data('grup-nama');
        var jamMasuk = $(this).data('jam-masuk');
        var jamKeluar = $(this).data('jam-keluar');
        var toleransiWaktu = $(this).data('toleransi-waktu');

        $('#id_grup_edit').val(idGrup);
        $('#nama_grup_edit').val(nama);
        $('#jam_masuk_edit').val(jamMasuk);
        $('#jam_keluar_edit').val(jamKeluar);
        $('#toleransi_waktu_edit').val(toleransiWaktu);
        openEditGrup();
    });

    //SUBMIT EDIT GRUP
    $('#form-edit-grup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idGrup = $('#id_grup_edit').val();
        let url = base_url + '/master-data/grup/update/' + idGrup;

        var formData = new FormData($('#form-edit-grup')[0]);
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
                closeEditGrup();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //DELETE GRUP
    $('#grup-table').on('click', '.btnDelete', function (){
        var idGrup = $(this).data('id');
        Swal.fire({
            title: "Delete Grup",
            text: "Apakah kamu yakin untuk menghapus grup ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/grup/delete/' + idGrup;
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