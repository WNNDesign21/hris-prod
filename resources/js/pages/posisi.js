$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    
    //ALL ABOUT SELECT2
    //SELECT2 GET SELECT OPTION JABATAN DARI POSISI YANG DIPILIH
    function getDataJabatanByPosisi(idPosisi) {
        loadingSwalShow();
        $.ajax({
            url: base_url + '/master-data/posisi/get-data-jabatan-by-posisi/' + idPosisi,
            method: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if(data !== null){
                    $('#input-jabatan').append('<label for="id_jabatan">Jabatan</label>');
                    let div = $('<div class="input-group mb-2" style="width:100%;"></div>');
                    let select = $('<select id="id_jabatan" name="id_jabatan" class="form-control select2"></select>');
                    select.append('<option value="">Pilih Jabatan</option>');
                    $.each(data, function (i, val){
                        select.append('<option value="'+val.id_jabatan+'">'+val.nama+'</option>');
                    });
                    div.append(select)
                    $('#input-jabatan').append(div);
                    $(select).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $('#modal-input-posisi')
                    });
                    jabatanSelect();
                }
                loadingSwalClose();
            }
        })
    }

    //SELECT2 GET DATA SELECT OPTION DARI JABATAN
    function getDataByJabatan(idJabatan) {
        loadingSwalShow();
        $.ajax({
            url: base_url + '/master-data/posisi/get-data-by-jabatan/' + idJabatan,
            method: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if(data.organisasi !== null){
                    $('#input-tambahan').append('<label for="id_organisasi">Organisasi</label>');
                    let div = $('<div class="input-group mb-2" style="width:100%;"></div>');
                    let select = $('<select id="id_organisasi" name="id_organisasi" class="form-control select2"></select>');
                    select.append('<option value="">CORPORATE / ALL PLANT</option>');
                    $.each(data.organisasi, function (i, val){
                        select.append('<option value="'+val.id_organisasi+'">'+val.nama+'</option>');
                    });
                    div.append(select)
                    $('#input-tambahan').append(div);
                    $(select).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $('#modal-input-posisi')
                    });
                }

                if(data.divisi !== null){
                    $('#input-tambahan').append('<label for="id_divisi">Divisi</label>');
                    let div = $('<div class="input-group mb-2" style="width:100%;"></div>');
                    let select = $('<select id="id_divisi" name="id_divisi" class="form-control select2"></select>');
                    select.append('<option value="">Pilih Divisi</option>');
                    $.each(data.divisi, function (i, val){
                        select.append('<option value="'+val.id_divisi+'">'+val.nama+'</option>');
                    });
                    div.append(select)
                    $('#input-tambahan').append(div);
                    $(select).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $('#modal-input-posisi')
                    });
                }

                if(data.departemen !== null){
                    $('#input-tambahan').append('<label for="id_departemen">Departemen</label>');
                    let div = $('<div class="input-group mb-2" style="width:100%;"></div>');
                    let select = $('<select id="id_departemen" name="id_departemen" class="form-control select2"></select>');
                    select.append('<option value="">Pilih Departemen</option>');
                    $.each(data.departemen, function (i, val){
                        select.append('<option value="'+val.id_departemen+'">'+val.nama+'</option>');
                    });
                    div.append(select)
                    $('#input-tambahan').append(div);
                    $(select).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $('#modal-input-posisi')
                    });
                }

                if(data.seksi !== null){
                    $('#input-tambahan').append('<label for="id_seksi">Seksi</label>');
                    let div = $('<div class="input-group mb-2" style="width:100%;"></div>');
                    let select = $('<select id="id_seksi" name="id_seksi" class="form-control select2"></select>');
                    select.append('<option value="">Pilih Seksi</option>');
                    $.each(data.seksi, function (i, val){
                        select.append('<option value="'+val.id_seksi+'">'+val.nama+'</option>');
                    });
                    div.append(select)
                    $('#input-tambahan').append(div);
                    $(select).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $('#modal-input-posisi')
                    });
                }
                loadingSwalClose();
            }
        })
    }

    //SELECT2 PARENT_ID
    $('#parent_id').select2({
        theme: "bootstrap-5",
        dropdownParent: $('#modal-input-posisi'),
        ajax: {
            url: base_url + "/master-data/posisi/get-data-parent",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                };
            },
            cache: true,
        },
    }).on("select2:select", function (selected) {
        var idPosisi = selected.params.data.id;
        $('#input-jabatan').empty();
        $('#input-tambahan').empty();
        if(idPosisi !== ''){
            getDataJabatanByPosisi(idPosisi);
        }
    });

    //SELECT2 JABATAN
    function jabatanSelect(){
        $('#id_jabatan').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#modal-input-posisi'),
        }).on("select2:select", function (selected) {
            var idJabatan = selected.params.data.id;
            $('#input-tambahan').empty();
            getDataByJabatan(idJabatan);
        });
    }

    //ALL ABOUT SWALL & NOTIFICATION
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

    //ALL ABOUT DATATABLE & MODAL
    //DATATABLE POSISI
    var columnsTable = [
        { data: "no" },
        { data: "nama_posisi" },
        { data: "nama_jabatan" },
        { data: "nama_organisasi" },
        { data: "nama_divisi" },
        { data: "nama_departemen" },
        { data: "nama_seksi" },
        { data: "aksi" },
    ];

    var posisiTable = $("#posisi-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/posisi/datatable",
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
        posisiTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH POSISI
    $('.btnAdd').on("click", function (){
        openPosisi();
    })

    //CLOSE MODAL TAMBAH POSISI
    $('.btnClose').on("click", function (){
        closePosisi();
    })

    // MODAL TAMBAH DEPARTEMEN
    var modalInputPosisiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputPosisi = new bootstrap.Modal(
        document.getElementById("modal-input-posisi"),
        modalInputPosisiOptions
    );

    function openPosisi() {
        modalInputPosisi.show();
    }

    function closePosisi() {
        $('#nama_posisi').val('');
        modalInputPosisi.hide();
    }

    //SUBMIT TAMBAH DEPARTEMEN
    $('#form-tambah-posisi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-posisi').attr('action');

        var formData = new FormData($('#form-tambah-posisi')[0]);
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
    var modalEditPosisiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditPosisi = new bootstrap.Modal(
        document.getElementById("modal-edit-posisi"),
        modalEditPosisiOptions
    );

    function openEditPosisi() {
        modalEditPosisi.show();
    }

    function closeEditPosisi() {
        $('#nama_posisi_edit').val('');
        modalEditPosisi.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditPosisi();
    })

    //EDIT DEPARTEMEN
    $('#posisi-table').on('click', '.btnEdit', function (){
        var idPosisi = $(this).data('id');
        var nama = $(this).data('posisi-nama');
        $('#id_posisi_edit').val(idPosisi);
        $('#nama_posisi_edit').val(nama);
        openEditPosisi();
    });

    //SUBMIT EDIT DEPARTEMEN
    $('#form-edit-posisi').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idPosisi = $('#id_posisi_edit').val();
        let url = base_url + '/master-data/posisi/update/' + idPosisi;

        var formData = new FormData($('#form-edit-posisi')[0]);
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
    $('#posisi-table').on('click', '.btnDelete', function (){
        var idPosisi = $(this).data('id');
        Swal.fire({
            title: "Delete Posisi",
            text: "Apakah kamu yakin untuk menghapus posisi ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/posisi/delete/' + idPosisi;
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