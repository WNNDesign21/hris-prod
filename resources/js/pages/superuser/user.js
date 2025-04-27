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

    //DATATABLE USERANISASI
    var columnsTable = [
        { data: "username" },
        { data: "email" },
        { data: "organisasi" },
        { data: "role" },
        { data: "aksi" },
    ];

    var userTable = $("#user-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/superuser/user/datatable",
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
        userTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH USER
    $('.btnAdd').on("click", function (){
        openUser();
    })

    //CLOSE MODAL TAMBAH USER
    $('.btnClose').on("click", function (){
        closeUser();
    })


    // MODAL TAMBAH USER
    var modalInputUserOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputUser = new bootstrap.Modal(
        document.getElementById("modal-tambah"),
        modalInputUserOptions
    );

    function openUser() {
        modalInputUser.show();
    }

    function closeUser() {
        $('#username').val('');
        $('#email').val('');
        $('#password').val('');
        $('#confirm_password').val('');
        $('#organisasi').val('');
        $('#role').val('');
        modalInputUser.hide();
    }

    //SUBMIT TAMBAH USERANISASI
    $('#form-tambah').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah').attr('action');

        var formData = new FormData($('#form-tambah')[0]);
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

    // MODAL EDIT USER
    var modalEditUserOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditUser = new bootstrap.Modal(
        document.getElementById("modal-edit"),
        modalEditUserOptions
    );

    function openEditUser() {
        modalEditUser.show();
    }

    function closeEditUser() {
        $('#usernameEdit').val('');
        $('#emailEdit').val('');
        $('#passwordEdit').val('');
        $('#confirm_passwordEdit').val('');
        $('#organisasiEdit').val('');
        $('#roleEdit').val('');
        modalEditUser.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditUser();
    })

    //EDIT USERANISASI
    $('#user-table').on('click', '.btnEdit', function (){
        openEditUser();
    });

    //SUBMIT EDIT USERANISASI
    $('#form-edit').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idUser = $('#idEdit').val();
        let url = base_url + '/superuser/user/update/' + idUser;

        var formData = new FormData($('#form-edit')[0]);
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

    //DELETE USERANISASI
    $('#user-table').on('click', '.btnDelete', function (){
        var idUser = $(this).data('id');
        Swal.fire({
            title: "Delete User",
            text: "Apakah kamu yakin untuk menghapus user ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/superuser/user/delete/' + idUser;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        loadingSwalClose();
                        refreshTable();
                        showToast({ title: data.message });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

    $('#roles').select2({
        dropdownParent: $('#modal-tambah'),
    });

    $('#organisasi').select2({
        dropdownParent: $('#modal-tambah'),
    });

    $('#rolesEdit').select2({
        dropdownParent: $('#modal-edit'),
    });

    $('#organisasiEdit').select2({
        dropdownParent: $('#modal-edit'),
    });

});
