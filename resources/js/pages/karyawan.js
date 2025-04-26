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
        { data: "ni_karyawan" },
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "grup" },
        { data: "jenis_kontrak" },
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "status_karyawan" },
        { data: "nik" },
        { data: "no_kk" },
        { data: "tempat_lahir" },
        { data: "tanggal_lahir" },
        { data: "jenis_kelamin" },
        { data: "agama" },
        { data: "alamat" },
        { data: "domisili" },
        { data: "npwp" },
        { data: "no_bpjs_ks" },
        { data: "no_bpjs_kt" },
        { data: "no_telp" },
        { data: "email" },
        { data: "nama_bank" },
        { data: "no_rekening" },
        { data: "nama_rekening" },
        { data: "nama_ibu_kandung" },
        { data: "jenjang_pendidikan" },
        { data: "jurusan_pendidikan" },
        { data: "no_telp_darurat" },
        { data: "gol_darah" },
        { data: "sisa_cuti" },
        { data: "hutang_cuti" },
        { data: "aksi" },
    ];

    var karyawanTable = $("#karyawan-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        stateSave: !0,
        ajax: {
            url: base_url + "/master-data/karyawan/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var departemen = $('#filterDepartemen').val();
                var grup = $('#filterGrup').val();
                var jenisKontrak = $('#filterJeniskontrak').val();
                var statusKaryawan = $('#filterStatuskaryawan').val();
                var jenisKelamin = $('#filterJeniskelamin').val();
                var agama = $('#filterAgama').val();
                var golonganDarah = $('#filterGolongandarah').val();
                var statusKeluarga = $('#filterStatuskeluarga').val();
                var kategoriKeluarga = $('#filterKategorikeluarga').val();
                var namaBank = $('#filterNamabank').val();
                var nama = $('#filterNama').val();
                var nik = $('#filterNik').val();

                dataFilter.departemen = departemen;
                dataFilter.grup = grup;
                dataFilter.jenisKontrak = jenisKontrak;
                dataFilter.statusKaryawan = statusKaryawan;
                dataFilter.jenisKelamin = jenisKelamin;
                dataFilter.agama = agama;
                dataFilter.golonganDarah = golonganDarah;
                dataFilter.statusKeluarga = statusKeluarga;
                dataFilter.kategoriKeluarga = kategoriKeluarga;
                dataFilter.namaBank = namaBank;
                dataFilter.nama = nama;
                dataFilter.nik = nik;
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
                targets: [3,-1],
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
        // $('#filterDepartemen').val('').trigger('change');
        // $('#filterGrup').val('').trigger('change');
        // $('#filterJeniskontrak').val('').trigger('change');
        // $('#filterStatuskaryawan').val('').trigger('change');
        // $('#filterJeniskelamin').val('').trigger('change');
        // $('#filterAgama').val('').trigger('change');
        // $('#filterGolongandarah').val('').trigger('change');
        // $('#filterStatuskeluarga').val('').trigger('change');
        // $('#filterKategorikeluarga').val('').trigger('change');
        // $('#filterNamabank').val('').trigger('change');
        // $('#filterNama').val('');
        // $('#filterNik').val('');
        // karyawanTable.search("").draw();
        var searchValue = karyawanTable.search();
        if (searchValue) {
            karyawanTable.search(searchValue).draw();
        } else {
            karyawanTable.search("").draw();
        }
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
        $("#no_kk").val("");
        $("#sisa_cuti_pribadi").val("");
        $("#sisa_cuti_bersama").val("");
        $("#tempat_lahir").val("");
        $("#tanggal_lahir").val("");
        $("#jenis_kelamin").val("");
        $("#agama").val("");
        $("#gol_darah").val("");
        $("#status_keluarga").val("");
        $("#kategori_keluarga").val("");
        $("#alamat").val("");
        $("#domisili").val("");
        $("#no_telp").val("");
        $("#email").val("");
        $("#npwp").val("");
        $("#no_bpjs_ks").val("");
        $("#no_bpjs_kt").val("");
        $("#no_rekening").val("");
        $("#nama_rekening").val("");
        $("#nama_bank").val("");
        $("#nama_ibu_kandung").val("");
        $("#jurusan_pendidikan").val("");
        $("#jenjang_pendidikan").val("");
        $("#no_telp_darurat").val("");
        $('#status_karyawan').val("");
        $('#pin').val("");
        // $('#grup').val("");
        $('#posisi').val("");
    }

    function initializeSelect2() {
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

        $('#kategori_keluarga').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#nama_bank').select2({
            dropdownParent: $('#modal-input-karyawan'),
        });

        $('#jenjang_pendidikan').select2({
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

        // $('#grup').select2({
        //     dropdownParent: $('#modal-input-karyawan'),
        //     ajax: {
        //         url: base_url + "/master-data/grup/get-data-grup",
        //         type: "post",
        //         dataType: "json",
        //         delay: 250,
        //         data: function (params) {
        //             return {
        //                 search: params.term || "",
        //                 page: params.page || 1,
        //             };
        //         },
        //         cache: true,
        //     },
        // });

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

        $('#nama_bankEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#kategori_keluargaEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        $('#jenjang_pendidikanEdit').select2({
            dropdownParent: $('#modal-edit-karyawan'),
        });

        // selectGrupEdit(grupId);
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
                $('#ni_karyawanEdit').val(detailKaryawan.ni_karyawan);
                $('#link_fotoEdit').attr('href', detailKaryawan.foto);
                $('#image_reviewEdit').attr('src', detailKaryawan.foto);
                $('#nikEdit').val(detailKaryawan.nik);
                $('#namaEdit').val(detailKaryawan.nama);
                $('#no_kkEdit').val(detailKaryawan.no_kk);
                $('#sisa_cuti_pribadiEdit').val(detailKaryawan.sisa_cuti_pribadi);
                $('#sisa_cuti_bersamaEdit').val(detailKaryawan.sisa_cuti_bersama);
                $('#sisa_cuti_tahun_laluEdit').val(detailKaryawan.sisa_cuti_tahun_lalu);
                $('#expired_date_cuti_tahun_laluEdit').val(detailKaryawan.expired_date_cuti_tahun_lalu);
                $('#hutang_cutiEdit').val(detailKaryawan.hutang_cuti);
                $('#tempat_lahirEdit').val(detailKaryawan.tempat_lahir);
                $('#tanggal_lahirEdit').val(detailKaryawan.tanggal_lahir);
                $('#jenis_kelaminEdit').val(detailKaryawan.jenis_kelamin);
                $('#agamaEdit').val(detailKaryawan.agama);
                $('#gol_darahEdit').val(detailKaryawan.gol_darah);
                $('#status_keluargaEdit').val(detailKaryawan.status_keluarga);
                $('#kategori_keluargaEdit').val(detailKaryawan.kategori_keluarga);
                $('#alamatEdit').val(detailKaryawan.alamat);
                $('#domisiliEdit').val(detailKaryawan.domisili);
                $('#no_telpEdit').val(detailKaryawan.no_telp);
                $('#no_telp_daruratEdit').val(detailKaryawan.no_telp_darurat);
                $('#emailEdit').val(detailKaryawan.email);
                $('#npwpEdit').val(detailKaryawan.npwp);
                $('#no_bpjs_ksEdit').val(detailKaryawan.no_bpjs_ks);
                $('#no_bpjs_ktEdit').val(detailKaryawan.no_bpjs_kt);
                $('#no_rekeningEdit').val(detailKaryawan.no_rekening);
                $('#nama_rekeningEdit').val(detailKaryawan.nama_rekening);
                $('#nama_bankEdit').val(detailKaryawan.nama_bank);
                $('#nama_ibu_kandungEdit').val(detailKaryawan.nama_ibu_kandung);
                $('#jenjang_pendidikanEdit').val(detailKaryawan.jenjang_pendidikan);
                $('#jurusan_pendidikanEdit').val(detailKaryawan.jurusan_pendidikan);
                $('#status_karyawanEdit').val(detailKaryawan.status_karyawan);
                $('#tanggal_mulaiEdit').val(detailKaryawan.tanggal_mulai);
                $('#tanggal_selesaiEdit').val(detailKaryawan.tanggal_selesai);
                $('#pinEdit').val(detailKaryawan.pin);
                $('#isAdminEdit').prop('checked', detailKaryawan.is_admin);
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
    });

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
    }

    function getListKontrak(idKaryawan){
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
                                                <label for="" class="fw-light">Template Print</label>
                                                <h5><a href="`+url_download+`" target="_blank"><i class="fas fa-download"></i>
                                                        Unduh
                                                        Template Disini</a></h5>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="fw-light">Dokumen Asli</label>
                                                <h5>`+(val.attachment ? `<a href="`+val.attachment+`" target="_blank"><i class="fas fa-download"></i>
                                                        Unduh
                                                        Dokumen Disini</a>` : '-') +`</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    no++;
                });
                openKontrak();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    }

    // $('#form-kontrak').on('submit', function (e){
    //     loadingSwalShow();
    //     e.preventDefault();
    //     let url = $('#form-kontrak').attr('action');

    //     var formData = new FormData($('#form-kontrak')[0]);
    //     $.ajax({
    //         url: url,
    //         data: formData,
    //         method:"POST",
    //         contentType: false,
    //         processData: false,
    //         dataType: "JSON",
    //         success: function (data) {
    //             let dataKontrak = data.data;
    //             showToast({ title: data.message });
    //             getListKontrak(dataKontrak.karyawan_id);
    //             refreshTable();
    //             loadingSwalClose();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             loadingSwalClose();
    //             showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //         },
    //     })
    // });

    // $('#jenis_kontrakEdit').on('change', function (){
    //     let jenisKontrak = $(this).val();
    //     if(jenisKontrak == 'PKWTT'){
    //         $('#durasi_kontrakEdit').val('').prop('readonly', true);
    //         $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', true);
    //     } else {
    //         $('#durasi_kontrakEdit').val('').prop('readonly', false);
    //         $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', false);
    //     }
    // })

    // $('.btnUpload').on('click', function (){
    //     let input = $('#upload-karyawan');
    //     input.click();

    //     input.off('change').on('change', function () {
    //         Swal.fire({
    //             title: "Upload Record Karyawan",
    //             text: "Karyawan dengan ID yang sudah terdaftar akan terupdate sesuai dengan ID Karyawan",
    //             icon: "warning",
    //             showCancelButton: true,
    //             confirmButtonColor: "#3085d6",
    //             cancelButtonColor: "#d33",
    //             confirmButtonText: "Yes, Upload it!",
    //             allowOutsideClick: false,
    //         }).then((result) => {
    //             if (result.value) {
    //                 loadingSwalShow();
    //                 const url = base_url + "/master-data/karyawan/upload-karyawan";
    //                 let formData = new FormData();
    //                 formData.append('karyawan_file', input[0].files[0]);

    //                 $.ajax({
    //                     url: url,
    //                     method: "POST",
    //                     data: formData,
    //                     contentType: false,
    //                     processData: false,
    //                     success: function (data) {
    //                         showToast({ title: data.message });
    //                         input.val('');
    //                         loadingSwalClose();
    //                         refreshTable();
    //                     },
    //                     error: function (jqXHR, textStatus, errorThrown) {
    //                         loadingSwalClose();
    //                         showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //                     },
    //                 })
    //             }
    //         });
    //     });
    // });

    // $('.btnTemplate').on('click', function () {
    //     window.location.href = base_url + '/template/template_upload_karyawan.xlsx';
    // });

    //FILTER KARYAWAN
    $('.btnFilter').on("click", function (){
        openFilter();
    })

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    })

    $('.btnResetFilter').on("click", function (){
        $('#filterDepartemen').val("").trigger('change');
        $('#filterGrup').val("").trigger('change');
        $('#filterJeniskontrak').val("").trigger('change');
        $('#filterStatuskaryawan').val("").trigger('change');
        $('#filterJeniskelamin').val("").trigger('change');
        $('#filterAgama').val("").trigger('change');
        $('#filterGolongandarah').val("").trigger('change');
        $('#filterStatuskeluarga').val("").trigger('change');
        $('#filterKategorikeluarga').val("").trigger('change');
        $('#filterNamabank').val("").trigger('change');
        $('#filterNama').val("");
        $('#filterNik').val("");
    })


    // MODAL FILTER
    var modalFilterKaryawanOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilterKaryawan = new bootstrap.Modal(
        document.getElementById("modal-filter"),
        modalFilterKaryawanOptions
    );

    function openFilter() {
        modalFilterKaryawan.show();
        $('#filterDepartemen').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterGrup').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterJeniskontrak').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterStatuskaryawan').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterJeniskelamin').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterAgama').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterGolongandarah').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterStatuskeluarga').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterKategorikeluarga').select2({
            dropdownParent: $('#modal-filter'),
        });

        $('#filterNamabank').select2({
            dropdownParent: $('#modal-filter'),
        });
    }

    function closeFilter() {
        modalFilterKaryawan.hide();
    }

    $(".btnSubmitFilter").on("click", function () {
        karyawanTable.draw();
        closeFilter();
    });

    $("#foto").on("change", function () {
        var review = "image_review";
        var linkFoto = "link_foto";
        loadingSwalShow();
        readURL(this, review, linkFoto);
    });

    $("#fotoEdit").on("change", function () {
        var review = "image_reviewEdit";
        var linkFoto = "link_fotoEdit";
        loadingSwalShow();
        readURL(this, review, linkFoto);
    });

    function readURL(input, review, linkFoto) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#" + linkFoto).attr("href", e.target.result);
                $("#" + review).attr("src", e.target.result);
                $(input).next(".custom-file-label").html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]);
        }
        loadingSwalClose();
    }

    // MODAL UPLOAD
    $('#method').select2({
        dropdownParent: $('#modal-upload'),
    });

    var modalUploadOptions = {
        backdrop: "static",
        keyboard: false,
    };

    var modalUpload = new bootstrap.Modal(
        document.getElementById("modal-upload"),
        modalUploadOptions
    );

    function openUpload() {
        modalUpload.show();
    }

    function closeUpload() {
        modalUpload.hide();
        resetUpload();
        refreshTable();
        clearInterval(refreshUploadTable);
    }

    function resetUpload() {
        $('#method').val('I').trigger('change');
        $("#karyawan_file").val("");
    }

    $('.btnUpload').on("click", function (){
        openUpload();
        setInterval(refreshUploadTable, 30000);
    });

    $('.btnCloseUpload').on("click", function (){
        closeUpload();
    })

    $('#form-upload').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-upload').attr('action');

        var formData = new FormData($('#form-upload')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                resetUpload();
                loadingSwalClose();
                showToast({ title: data.message });
                refreshUploadTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                resetUpload();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    var uploadColumnsTable = [
        { data: "description" },
        { data: "causer" },
        { data: "created_at" },
    ];

    var uploadTable = $("#upload-table").DataTable({
        search: {
            return: true,
        },
        order: [[2, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/karyawan/upload-datatable",
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
                    });
                }
            },
        },
        responsive: true,
        columns: uploadColumnsTable,
    })

    function refreshUploadTable() {
        var searchValue = uploadTable.search();
        if (searchValue) {
            uploadTable.search(searchValue).draw();
        } else {
            uploadTable.search("").draw();
        }
    }
});
