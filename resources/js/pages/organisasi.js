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

    //DATATABLE ORGANISASI
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "alamat" },
        { data: "aksi" },
    ];

    var orgTable = $("#organisasi-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/organisasi/datatable",
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
        orgTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH ORG
    $('.btnAdd').on("click", function (){
        openOrg();
    })

    //CLOSE MODAL TAMBAH ORG
    $('.btnClose').on("click", function (){
        closeOrg();
    })


    // MODAL TAMBAH ORG
    var modalInputOrgOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputOrg = new bootstrap.Modal(
        document.getElementById("modal-input-org"),
        modalInputOrgOptions
    );

    function openOrg() {
        modalInputOrg.show();
    }

    function closeOrg() {
        $('#nama_org').val('');
        $('#alamat_org').val('');
        modalInputOrg.hide();
    }

    //SUBMIT TAMBAH ORGANISASI
    $('#form-tambah-org').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-org').attr('action');

        var formData = new FormData($('#form-tambah-org')[0]);
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

    // MODAL EDIT ORG
    var modalEditOrgOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditOrg = new bootstrap.Modal(
        document.getElementById("modal-edit-org"),
        modalEditOrgOptions
    );

    function openEditOrg() {
        modalEditOrg.show();
    }

    function closeEditOrg() {
        $('#nama_org_edit').val('');
        $('#alamat_org_edit').val('');
        modalEditOrg.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditOrg();
    })

    //EDIT ORGANISASI
    $('#organisasi-table').on('click', '.btnEdit', function (){
        var idOrg = $(this).data('id');
        var nama = $(this).data('org-nama');
        var alamat = $(this).data('org-alamat');
        $('#id_org_edit').val(idOrg);
        $('#nama_org_edit').val(nama);
        $('#alamat_org_edit').val(alamat);
        openEditOrg();
    });

    //SUBMIT EDIT ORGANISASI
    $('#form-edit-org').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idOrg = $('#id_org_edit').val();
        let url = base_url + '/master-data/organisasi/update/' + idOrg;

        var formData = new FormData($('#form-edit-org')[0]);
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

    //DELETE ORGANISASI
    $('#organisasi-table').on('click', '.btnDelete', function (){
        var idOrg = $(this).data('id');
        Swal.fire({
            title: "Delete Organisasi",
            text: "Apakah kamu yakin untuk menghapus organisasi ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/organisasi/delete/' + idOrg;
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