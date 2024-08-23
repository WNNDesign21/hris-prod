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
        { data: "id_karyawan" },
        { data: "nama" },
        { data: "posisi" },
        { data: "grup" },
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
                console.log(dataFilter);
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
                targets: [2,-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center");
                },
            },
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
        openKaryawan();
    })

    //CLOSE MODAL TAMBAH KARYAWAN
    $('.btnClose').on("click", function (){
        closeKaryawan();
    })


    // MODAL TAMBAH KARYAWAN
    var modalInputKaryawanOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputKaryawan = new bootstrap.Modal(
        document.getElementById("modal-input-karyawan"),
        modalInputKaryawanOptions
    );

    function openKaryawan() {
        modalInputKaryawan.show();
        initializeSelect2();
    }

    function closeKaryawan() {
        modalInputKaryawan.hide();
        resetKaryawan();
    }

    function resetKaryawan() {
        $("#nama").val("");
        $("#no_ktp").val("");
        $("#sisa_cuti").val("");
        $("#tempat_lahir").val("");
        $("#tanggal_lahir").val("");
        $("#jenis_kelamin").val("");
        $("#agama").val("");
        $("#gol_darah").val("");
        $("#status_keluarga").val("");
        $("#alamat").val("");
        $("#no_telp").val("");
        $("#email").val("");
        $("#npwp").val("");
        $("#no_bpjs_ks").val("");
        $("#no_bpjs_kt").val("");
        $('#status_karyawan').val("AKTIF");
        $('#tahun_masuk').val("");
        $('#grup').val("");
        $('#posisi').val("");
    }

    function initializeSelect2() {
        $('#status_karyawan').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#jenis_kelamin').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#agama').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#gol_darah').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#status_keluarga').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#tahun_masuk').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#user_id').select2({
            dropdownParent: $('#modal-input-karyawan'),
            ajax: {
                url: base_url + "/master-data/karyawan/get-data-user",
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
        });

        $('#grup').select2({
            dropdownParent: $('#modal-input-karyawan'),
            ajax: {
                url: base_url + "/master-data/grup/get-data-grup",
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
        });
    
        $('#posisi').select2({
            dropdownParent: $('#modal-input-karyawan'),
            ajax: {
                url: base_url + "/master-data/posisi/get-data-posisi",
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
        });
    };

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
                closeKaryawan();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    function initializeSelect2Edit(grupId, posisi) {
        $('#status_karyawanEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#jenis_kelaminEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#agamaEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#gol_darahEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#status_keluargaEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#tahun_masukEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        selectGrupEdit(grupId);
        selectPosisiEdit(posisi);
    };

    // MODAL EDIT KARYAWAN
    var modalEditKaryawanOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditKaryawan = new bootstrap.Modal(
        document.getElementById("modal-edit-karyawan"),
        modalEditKaryawanOptions
    );

    function openEditKaryawan() {
        modalEditKaryawan.show();
    }

    function closeEditKaryawan() {
        modalEditKaryawan.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditKaryawan();
    })

    //EDIT JABATAN
    $('#karyawan-table').on('click', '.btnEdit', function (){
        let idKaryawan = $(this).data('id');
        getDetailKaryawan(idKaryawan);
    });

    function getDetailKaryawan(idKaryawan){
        loadingSwalShow();
        let url = base_url + '/master-data/karyawan/get-data-detail-karyawan/' + idKaryawan;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let detailKaryawan = response.data;
                $('#id_karyawanEdit').val(detailKaryawan.id_karyawan);
                $('#namaEdit').val(detailKaryawan.nama);
                $('#no_ktpEdit').val(detailKaryawan.no_ktp);
                $('#sisa_cutiEdit').val(detailKaryawan.sisa_cuti);
                $('#tempat_lahirEdit').val(detailKaryawan.tempat_lahir);
                $('#tanggal_lahirEdit').val(detailKaryawan.tanggal_lahir);
                $('#jenis_kelaminEdit').val(detailKaryawan.jenis_kelamin);
                $('#agamaEdit').val(detailKaryawan.agama);
                $('#gol_darahEdit').val(detailKaryawan.gol_darah);
                $('#status_keluargaEdit').val(detailKaryawan.status_keluarga);
                $('#alamatEdit').val(detailKaryawan.alamat);
                $('#no_telpEdit').val(detailKaryawan.no_telp);
                $('#emailEdit').val(detailKaryawan.email);
                $('#npwpEdit').val(detailKaryawan.npwp);
                $('#no_bpjs_ksEdit').val(detailKaryawan.no_bpjs_ks);
                $('#no_bpjs_ktEdit').val(detailKaryawan.no_bpjs_kt);
                $('#status_karyawanEdit').val(detailKaryawan.status_karyawan);
                $('#tahun_masukEdit').val(detailKaryawan.tahun_masuk);
                initializeSelect2Edit(detailKaryawan.grup_id, detailKaryawan.posisi);
                openEditKaryawan();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    }

    //SUBMIT EDIT KARYAWAN
    $('#form-edit-karyawan').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idKaryawan = $('#id_karyawanEdit').val();
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
                closeEditKaryawan();
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
            title: "Delete Karyawan",
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


    //SELECT GRUP EDIT
    function selectGrupEdit(grupId =  "") {
        $.ajax({
            url: base_url + '/master-data/grup/get-data-all-grup',
            type: "get",
            success: function (data) {
                var selectGrup = $("#grupEdit");
                selectGrup.empty();
                $.each(data, function (i, val){
                    selectGrup.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $('#grupEdit').val(grupId).trigger('change');
                $('#grupEdit').select2({
                    dropdownParent: $('#modal-edit-karyawan')
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    };

    //SELECT POSISI EDIT
    function selectPosisiEdit(posisiId =  []) {
        $.ajax({
            url: base_url + '/master-data/posisi/get-data-all-posisi',
            type: "get",
            success: function (data) {
                var selectGrup = $("#posisiEdit");
                selectGrup.empty();
                $.each(data, function (i, val){
                    selectGrup.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $('#posisiEdit').val(posisiId).trigger('change');
                $('#posisiEdit').select2({
                    dropdownParent: $('#modal-edit-karyawan')
                });
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    };

    //AKUN KARYAWAN
    $('.btnCloseAkun').on("click", function (){
        closeAkun();
    })

    $('#karyawan-table').on('click', '.btnAkun', function (){
        let idAkun = $(this).data('id');
        let idKaryawan = $(this).data('id-karyawan');
        let nama = $(this).data('nama');

        if(idAkun !== null){
            getDetailAkun(idAkun, idKaryawan, nama);
        } else {
            $('#akun-title').text('Buat Akun - ' + nama);
            $('#id_karyawanAkunEdit').val(idKaryawan);
            openAkun();
        }
    });

    var modalAkunOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalAkun = new bootstrap.Modal(
        document.getElementById("modal-akun"),
        modalAkunOptions
    );

    function openAkun() {
        modalAkun.show();
    }

    function closeAkun() {
        modalAkun.hide();
        resetAkun();
    }

    function resetAkun(){
        $('#id_karyawanAkunEdit').val("");
        $('#akun-title').text("");
        $('#username_akunEdit').val("");
        $('#email_akunEdit').val("");
        $('#password_akunEdit').val("");
    };

    function getDetailAkun(userId, idKaryawan, nama){
        loadingSwalShow();
        let url = base_url + '/master-data/akun/get-data-detail-akun/' + userId;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let detailAkun = response.data;
                
                $('#id_akunEdit').val(userId);
                $('#id_karyawanAkunEdit').val(idKaryawan);
                $('#akun-title').text('Edit Akun - ' + nama);
                $('#username_akunEdit').val(detailAkun.username);
                $('#email_akunEdit').val(detailAkun.email);
                openAkun();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    }

    $('#form-akun').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-akun').attr('action');

        var formData = new FormData($('#form-akun')[0]);
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
                closeAkun();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //KONTRAK
    $('.btnCloseKontrak').on("click", function (){
        closeKontrak();
    })

    $('#karyawan-table').on('click', '.btnKontrak', function (){
        loadingSwalShow();
        let idKaryawan = $(this).data('id');
        let namaKaryawan = $(this).data('nama');
        $('#title-kontrak').text('Kontrak - '+namaKaryawan)
        

        $('#jenis_kontrakEdit').select2({
            dropdownParent: $('#modal-kontrak'),
        });

        $('#tempat_administrasi_kontrakEdit').select2({
            dropdownParent: $('#modal-kontrak'),
        });

        $('#karyawan_id_kontrakEdit').val(idKaryawan)

        getListKontrak(idKaryawan);
        selectPosisiKontrak();
        openKontrak();
    });

    function selectPosisiKontrak(posisiId = ''){
        $.ajax({
            url: base_url + '/master-data/posisi/get-data-all-posisi',
            type: "get",
            success: function (data) {
                var selectPosisiKontrak = $("#posisi_kontrakEdit");
                selectPosisiKontrak.empty();
                $.each(data, function (i, val){
                    selectPosisiKontrak.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $('#posisi_kontrakEdit').val(posisiId).trigger('change');
                $('#posisi_kontrakEdit').select2({
                    dropdownParent: $('#modal-kontrak')
                });
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    }

    var modalKontrakOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalKontrak = new bootstrap.Modal(
        document.getElementById("modal-kontrak"),
        modalKontrakOptions
    );

    function openKontrak() {
        modalKontrak.show();
    }

    function closeKontrak() {
        modalKontrak.hide();
        resetKontrak();
    }

    function resetKontrak(){
        $('#id_kontrakEdit').val("");
        $('#jenis_kontrakEdit').val("");
        $('#no_surat_kontrakEdit').val("");
        $('#tempat_administrasi_kontrakEdit').val("");
        $('#posisi_kontrakEdit').val("");
        $('#issued_date_kontrakEdit').val("");
        $('#durasi_kontrakEdit').val("");
        $('#tanggal_mulai_kontrakEdit').val("");
        $('#tanggal_selesai_kontrakEdit').val("");
        $('#salary_kontrakEdit').val("");
        $('#deskripsi_kontrakEdit').val("");
    };

    // function getDetailAkun(userId, idKaryawan, nama){
    //     loadingSwalShow();
    //     let url = base_url + '/master-data/akun/get-data-detail-akun/' + userId;
    //     $.ajax({
    //         url: url,
    //         method: "GET",
    //         dataType: "JSON",
    //         success: function (response) {
    //             let detailAkun = response.data;
                
    //             $('#id_akunEdit').val(userId);
    //             $('#id_karyawanAkunEdit').val(idKaryawan);
    //             $('#akun-title').text('Edit Akun - ' + nama);
    //             $('#username_akunEdit').val(detailAkun.username);
    //             $('#email_akunEdit').val(detailAkun.email);
    //             openAkun();
    //             loadingSwalClose();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //         },
    //     }); 
    // }

    function getListKontrak(idKaryawan){
        // loadingSwalShow();
        let url = base_url + '/master-data/kontrak/get-data-list-kontrak/' + idKaryawan;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let detailKontrak = response.data;
                $('#list-kontrak').empty();
                let no = 1;
                $.each(detailKontrak, function (i, val){
                    let url_download = base_url + '/master-data/kontrak/download-kontrak-kerja/' + val.id_kontrak;
                    $('#list-kontrak').append(`
                        <div class="panel p-4 mb-3">
                            <div class="panel-heading" id="kontrak-`+no+`" role="tab">
                                <a class="panel-title" aria-controls="kontrak-content-`+no+`"
                                    aria-expanded="true" data-bs-toggle="collapse" href="#kontrak-content-`+no+`"
                                    data-parent="#list-kontrak">
                                    <div class="row d-flex justify-content-between">
                                        <div class="col flex-col">
                                            <small>`+val.no_surat+`</small>
                                            <h5>`+val.id_kontrak+`</h5>
                                            <small class="mt-0">`+val.tempat_administrasi+`, `+val.issued_date_text+`</small>
                                        </div>
                                        <div class="col text-end">
                                            <h5>`+val.status_badge+`</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="panel-collapse collapse mt-2" id="kontrak-content-`+no+`"
                                aria-labelledby="kontrak-`+no+`" role="tabpanel" data-bs-parent="#kontrak-`+no+`">
                                <div class="panel-body">
                                    <div class="row mt-5">
                                        <div class="col-lg-6 col-12">
                                            <div class="form-group">
                                                <label for="" class="fw-light">Jenis
                                                    Kontrak</label>
                                                <h5>`+val.jenis+`</h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Posisi</label>
                                                <h5>`+val.nama_posisi+`</h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Status</label>
                                                <h5>`+val.status+`</h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <div class="form-group">
                                                <label for="" class="fw-light">Periode
                                                    Kontrak</label>
                                                <h5>`+val.tanggal_mulai+` - `+val.tanggal_selesai+`</h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Salary</label>
                                                <h5>`+val.salary+`</h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Deskripsi</label>
                                                <h5>`+val.deskripsi+`</h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Soft Copy</label>
                                                <h5><a href="`+url_download+`" target="_blank"><i class="fas fa-download"></i>
                                                        Unduh
                                                        Dokumen Disini</a></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    no++;
                });
                // loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    }

    $('#form-kontrak').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-kontrak').attr('action');

        var formData = new FormData($('#form-kontrak')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                let dataKontrak = data.data;
                showToast({ title: data.message });
                getListKontrak(dataKontrak.karyawan_id);
                refreshTable();
                resetKontrak();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#jenis_kontrakEdit').on('change', function (){
        let jenisKontrak = $(this).val();
        if(jenisKontrak == 'PKWTT'){
            $('#durasi_kontrakEdit').val('').prop('readonly', true);
            $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', true);
        } else {
            $('#durasi_kontrakEdit').val('').prop('readonly', false);
            $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', false);
        }
    })
});