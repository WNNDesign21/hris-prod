$(function () {
    // GLOBAL VARIABLES
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    let loadingSwal;
    let count = 0;
    let pengikutCount = 0;
    let qrCodeTemp;
    // END GLOBAL VARIABLES

    // LOADING & ALERT
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

    function updatePengajuanNotification(){
        $.ajax({
            url: base_url + '/ajax/tugasluare/pengajuan/notification',
            method: 'GET',
            success: function(response){
                $('.notification-pengajuan').html(response.data);
            }
        })
    }
    // END LOADING & ALERT

    // DATATABLE
    var columnsTable = [
        { data: "id_tugasluar" },
        { data: "tanggal" },
        { data: "kendaraan" },
        { data: "pergi" },
        { data: "kembali" },
        { data: "km_awal" },
        { data: "km_akhir" },
        { data: "km_selisih" },
        { data: "rute" },
        { data: "pengikut" },
        { data: "keterangan" },
        { data: "checked" },
        { data: "legalized" },
        { data: "status" },
        { data: "aksi" },
    ];

    var pengajuanTable = $("#pengajuan-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/tugasluare/pengajuan/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let filterNopol = '';
                let filterStatus = '';
                let filterFrom = '';
                let filterTo = '';

                dataFilter.nopol = filterNopol;
                dataFilter.status = filterStatus;
                dataFilter.from = filterFrom;
                dataFilter.to = filterTo;
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
                targets: [-1],
            },
        ],
    })
    // END DATATABLE
    
    // MODAL
    var modalInputOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInput = new bootstrap.Modal(
        document.getElementById("modal-input"),
        modalInputOptions
    );

    var modalEditOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEdit = new bootstrap.Modal(
        document.getElementById("modal-edit"),
        modalEditOptions
    );

    var modalQrCodeOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalQrCode = new bootstrap.Modal(
        document.getElementById("modal-show-qrcode"),
        modalQrCodeOptions
    );

    var modalVerificationOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalVerification = new bootstrap.Modal(
        document.getElementById("modal-verification"),
        modalVerificationOptions
    );
    // END MODAL


    // FUNCTION
    function refreshTable() {
        var searchValue = pengajuanTable.search();
        if (searchValue) {
            pengajuanTable.search(searchValue).draw();
        } else {
            pengajuanTable.search("").draw();
        }
    }

    function open() {
        modalInput.show();
    }

    function close() {
        reset();
        modalInput.hide();
    }

    function openEdit() {
        modalEdit.show();
    }

    function closeEdit() {
        modalEdit.hide();
        resetEdit();
    }

    function openQrCode() {
        modalQrCode.show();
    }

    function closeQrCode() {
        modalQrCode.hide();
        refreshTable();
    }

    function openVerification() {
        modalVerification.show();
    }

    function closeVerification() {
        modalVerification.hide();
        refreshTable();
    }

    function resetEdit() {
        $('#list-pengikutEdit').empty();
        $('#jam_pergiEdit').val('');
        $('#jam_kembaliEdit').val('');
        $('#jenis_kendaraanEdit').val('');
        $('#jenis_kepemilikanEdit').val('');
        $('#kode_wilayahEdit').val('');
        $('#nomor_polisiEdit').val('');
        $('#seri_akhirEdit').val('');
        $('#tempat_asalEdit').val('');
        $('#tempat_tujuanEdit').val('');
        $('#keteranganEdit').val('');
        $('#pengemudiEdit').val('');
        $('#id_tugasluarEdit').val('');
        pengikutCount = 0;
    }

    function reset(){
        $('#list-pengikut').empty();
        $('#jam_pergi').val('');
        $('#jam_kembali').val('');
        $('#jenis_kendaraan').val('');
        $('#jenis_kepemilikan').val('');
        $('#kode_wilayah').val('');
        $('#nomor_polisi').val('');
        $('#seri_akhir').val('');
        $('#tempat_asal').val('');
        $('#tempat_tujuan').val('');
        $('#keterangan').val('');
        $('#pengemudi').val('');
        count = 0;
    }

    function selectedPengikut(index, pengikutId){
        $.ajax({
            url: base_url + "/ajax/tugasluare/pengajuan/select-get-data-all-karyawan",
            method: 'GET',
            dataType: 'JSON',
            success: function (response) {
                let pengikutIds = response;
                let select = $('#id_pengikutEdit_'+index);
                $.each(pengikutIds, function (i, val){
                    select.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                console.log(index, pengikutId)
                $("#id_pengikutEdit_" + index).val(pengikutId).trigger('change');
                $("#id_pengikutEdit_" + index).select2({
                    dropdownParent: $('#modal-edit'),
                    ajax: {
                    url: base_url + "/ajax/tugasluare/pengajuan/select-get-data-karyawan",
                    type: "post",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        let selectedIds = [];
                        $("select[name='id_pengikutEdit[]']").each(function () {
                            let val = $(this).val();
                            if (val) {
                                selectedIds.push(val);
                            }
                        });
                        return {
                            search: params.term || "",
                            page: params.page || 1,
                            selectedIds: selectedIds
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true,
                    },
                });
            }
        })
    }

    function submitForm(url, formData, type){
        let jenisKeberangkatan;
        if (type == 'pergi') {
            jenisKeberangkatan = 'Pergi'
        } else {
            jenisKeberangkatan = 'Kembali'
        }

        Swal.fire({
            title: "Konfirmasi Jam "+jenisKeberangkatan+" Aktual",
            text: "Data yang sudah di konfirmasi tidak bisa diubah!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Tandai sebagai "+jenisKeberangkatan+" Kembali!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                $.ajax({
                    url: url,
                    data : formData,
                    method:"POST",
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (data) {
                        showToast({ title: data.message });
                        refreshTable();
                        loadingSwalClose();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    }
    // END FUNCTION


    // EVENT
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        open();
    })

    $('.btnClose').on("click", function (){
        close();
    })

    $('#pengajuan-table').on("click", '.btnEdit', function (){
        loadingSwalShow();
        let tugasluarId = $(this).data('id-tugasluar');
        let jamPergi = $(this).data('jam-pergi');
        let jamKembali = $(this).data('jam-kembali');
        let jenisKendaraan = $(this).data('jenis-kendaraan');
        let jenisKepemilikan = $(this).data('jenis-kepemilikan');
        let jenisKeberangkatan = $(this).data('jenis-keberangkatan');
        let kodeWilayah = $(this).data('kode-wilayah');
        let nomorPolisi = $(this).data('nomor-polisi');
        let seriAkhir = $(this).data('seri-akhir');
        let tempatAsal = $(this).data('tempat-asal');
        let tempatTujuan = $(this).data('tempat-tujuan');
        let pengemudi = $(this).data('pengemudi');
        let kmAwal = $(this).data('km-awal');
        let keterangan = $(this).data('keterangan');
        let url = base_url + '/ajax/tugasluare/pengajuan/get-data-pengikut/' + tugasluarId;

        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let pengikut = response.data;
                pengikutCount += pengikut.length;
                $('#jam_pergiEdit').val(jamPergi);
                $('#jam_kembaliEdit').val(jamKembali);
                $('#jenis_kendaraanEdit').val(jenisKendaraan).trigger('change');
                $('#jenis_kepemilikanEdit').val(jenisKepemilikan).trigger('change');
                $('#jenis_keberangkatanEdit').val(jenisKeberangkatan).trigger('change');
                $('#kode_wilayahEdit').val(kodeWilayah);
                $('#nomor_polisiEdit').val(nomorPolisi);
                $('#seri_akhirEdit').val(seriAkhir);
                $('#tempat_asalEdit').val(tempatAsal);
                $('#tempat_tujuanEdit').val(tempatTujuan);
                $('#keteranganEdit').val(keterangan);
                $('#km_awalEdit').val(kmAwal);
                $('#id_tugasluarEdit').val(tugasluarId);
                $('#pengemudiEdit').val(pengemudi).trigger('change');
                let list = $('#list-pengikutEdit').empty();
                
                $.each(pengikut, function (i, val){
                    list.append(`
                        <div class="form-group d-flex align-items-center">
                            <select class="form-control mr-2" name="id_pengikutEdit[]" id="id_pengikutEdit_${i + 1}" required>
                                <option value="">Pilih Karyawan</option>
                            </select>
                            <button type="button" class="btn btn-danger m-2 btnRemovePengikutEdit"><i class="fas fa-times"></i></button>
                        </div>
                    `)

                    $('.btnRemovePengikutEdit').last().on('click', function () {
                        $(this).closest('.form-group').remove();
                    });

                    selectedPengikut(i + 1, val.karyawan_id);
                });
                loadingSwalClose();
                openEdit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    $('.btnCloseEdit').on("click", function (){
        closeEdit();
    })
    
    $('#form-input').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-input')[0]);
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
                close();
                refreshTable();
                updatePengajuanNotification();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#form-edit').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idTugasluar = $('#id_tugasluarEdit').val();
        let url = base_url + '/tugasluare/pengajuan/update/' + idTugasluar;
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
                closeEdit();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#pengajuan-table').on('click', '.btnDelete', function (){
        var idTugsLuar = $(this).data('id-tugasluar');
        Swal.fire({
            title: "Delete ",
            text: "Apakah kamu yakin untuk menghapus pengajuan ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                let url = base_url + '/tugasluare/pengajuan/delete/' + idTugsLuar;
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
                        updatePengajuanNotification();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

    $('.btnAddPengikut').on("click", function (){
        count++;
        let tbody = $('#list-pengikut');
        tbody.append(`
            <div class="form-group d-flex align-items-center">
            <select class="form-control mr-2" name="id_pengikut[]" id="id_pengikut_${count}" required>
                <option value="">Pilih Karyawan</option>
            </select>
            <button type="button" class="btn btn-danger m-2 btnRemovePengikut"><i class="fas fa-times"></i></button>
            </div>
        `);

        // Add event listener to remove button
        $('.btnRemovePengikut').last().on('click', function () {
            $(this).closest('.form-group').remove();
            count--;
        });

        $("#id_pengikut_" + count).select2({
            dropdownParent: $('#modal-input'),
            ajax: {
            url: base_url + "/ajax/tugasluare/pengajuan/select-get-data-karyawan",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                let selectedIds = [];
                $("select[name='id_pengikut[]']").each(function () {
                    let val = $(this).val();
                    if (val) {
                        selectedIds.push(val);
                    }
                });
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    selectedIds: selectedIds
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true,
            },
        });
    })

    $('.btnAddPengikutEdit').on("click", function (){
        pengikutCount++;
        let tbody = $('#list-pengikutEdit');
        tbody.append(`
            <div class="form-group d-flex align-items-center">
            <select class="form-control mr-2" name="id_pengikutEdit[]" id="id_pengikutEdit_${pengikutCount}" required>
                <option value="">Pilih Karyawan</option>
            </select>
            <button type="button" class="btn btn-danger m-2 btnRemovePengikutEdit"><i class="fas fa-times"></i></button>
            </div>
        `);

        $('.btnRemovePengikutEdit').last().on('click', function () {
            $(this).closest('.form-group').remove();
        });

        $("#id_pengikutEdit_" + pengikutCount).select2({
            dropdownParent: $('#modal-edit'),
            ajax: {
            url: base_url + "/ajax/tugasluare/pengajuan/select-get-data-karyawan",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                let selectedIds = [];
                $("select[name='id_pengikutEdit[]']").each(function () {
                    let val = $(this).val();
                    if (val) {
                        selectedIds.push(val);
                    }
                });
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    selectedIds: selectedIds
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true,
            },
        });
    })

    $('#pengemudi').select2({
        dropdownParent: $('#modal-input'),
    })

    $('#pengemudiEdit').select2({
        dropdownParent: $('#modal-edit'),
    })

    $('#jenis_kendaraan').select2({
        dropdownParent: $('#modal-input'),
    })

    $('#jenis_kendaraanEdit').select2({
        dropdownParent: $('#modal-edit'),
    })

    $('#jenis_kepemilikan').select2({
        dropdownParent: $('#modal-input'),
    })

    $('#jenis_kepemilikanEdit').select2({
        dropdownParent: $('#modal-edit'),
    })

    $('#jenis_keberangkatan').select2({
        dropdownParent: $('#modal-input'),
    })

    $('#jenis_keberangkatanEdit').select2({
        dropdownParent: $('#modal-edit'),
    })

    $('#jenis_keberangkatan').on('change', function (){
        let value = $(this).val();
        if (value == 'RMH' || value == 'LNA'){
            console.log(value)
            $('#conditional-field').empty().append(`
                <div class="form-group">
                    <label for="">Nomor Polisi</label>
                    <div class="row">
                        <div class="col-3">
                            <input type="text" name="kode_wilayah" id="kode_wilayah" class="form-control"
                                required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="nomor_polisi" id="nomor_polisi" class="form-control"
                                required>
                        </div>
                        <div class="col-3">
                            <input type="text" name="seri_akhir" id="seri_akhir" class="form-control"
                                required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">KM Awal</label>
                    <div class="input-group mb-2" style="width:100%;">
                        <input type="text" name="km_awal" id="km_awal" class="form-control"
                            style="width:100%;">
                    </div>
                </div>
            `);
        } else {
            $('#conditional-field').empty();
        }
    })

    $('#jenis_keberangkatanEdit').on('change', function (){
        let value = $(this).val();
        if (value == 'RMH' || value == 'LNA'){
            console.log(value)
            $('#conditional-fieldEdit').empty().append(`
                <div class="form-group">
                    <label for="">Nomor Polisi</label>
                    <div class="row">
                        <div class="col-3">
                            <input type="text" name="kode_wilayahEdit" id="kode_wilayahEdit" class="form-control"
                                required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="nomor_polisiEdit" id="nomor_polisiEdit" class="form-control"
                                required>
                        </div>
                        <div class="col-3">
                            <input type="text" name="seri_akhirEdit" id="seri_akhirEdit" class="form-control"
                                required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">KM Awal</label>
                    <div class="input-group mb-2" style="width:100%;">
                        <input type="text" name="km_awalEdit" id="km_awalEdit" class="form-control"
                            style="width:100%;">
                    </div>
                </div>
            `);
        } else {
            $('#conditional-fieldEdit').empty();
        }
    })

    $('.btnCloseQrcode').on('click', function(){
        loadingSwalShow();
        var url = base_url + '/delete-qrcode-img';
        $.ajax({
            url: url,
            type: "POST",
            data: {
                _method: "delete",
                file_path: qrCodeTemp
            },
            dataType: "JSON",
            success: function (response) {
                loadingSwalClose();
                closeQrCode();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    $('#pengajuan-table').on('click', '.btnShowQR', function (){
        loadingSwalShow();
        var idTugasLuar = $(this).data('id-tugasluar');
        var url = base_url + '/generate-qrcode';
        let formData = new FormData();
        formData.append('id', idTugasLuar);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (response) {
                let qrCodeImg = response.data;
                qrCodeTemp = qrCodeImg;
                $('#qr-code').attr('src', qrCodeImg);
                openQrCode();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#form-verification').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idTugasluar = $('#id_tugasluarVerif').val();
        console.log(idTugasluar);
        let url = base_url + '/tugasluare/pengajuan/verifikasi/' + idTugasluar;
        var formData = new FormData($('#form-verification')[0]);
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
                closeVerification();
                refreshTable();
                updatePengajuanNotification();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#pengajuan-table').on('click', '.btnPergi', function (){
        let idTugasLuar = $(this).data('id-tugasluar');
        let kodeWilayah = $(this).data('kode-wilayah');
        let nomorPolisi = $(this).data('nomor-polisi');
        let seriAkhir = $(this).data('seri-akhir');
        let kilometers = $(this).data('km');

        $('#id_tugasluarVerif').val(idTugasLuar);
        $('#kode_wilayahVerif').val(kodeWilayah);
        $('#nomor_polisiVerif').val(nomorPolisi);
        $('#seri_akhirVerif').val(seriAkhir);
        $('#kilometerVerif').val(kilometers);
        openVerification();
    });

    $('#pengajuan-table').on('click', '.btnKembali', function (){
        let idTugasLuar = $(this).data('id-tugasluar');
        let kodeWilayah = $(this).data('kode-wilayah');
        let nomorPolisi = $(this).data('nomor-polisi');
        let seriAkhir = $(this).data('seri-akhir');

        $('#id_tugasluarVerif').val(idTugasLuar);
        $('#kode_wilayahVerif').val(kodeWilayah);
        $('#nomor_polisiVerif').val(nomorPolisi);
        $('#seri_akhirVerif').val(seriAkhir);
        openVerification();
    });
    // END EVENT
});