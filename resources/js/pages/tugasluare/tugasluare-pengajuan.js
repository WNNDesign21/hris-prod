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
    // END LOADING & ALERT

    // DATATABLE
    var columnsTable = [
        { data: "id_tugasluar" },
        { data: "tanggal" },
        { data: "kendaraan" },
        { data: "pergi" },
        { data: "kembali" },
        { data: "rute" },
        { data: "jarak" },
        { data: "pengikut" },
        { data: "keterangan" },
        { data: "checked" },
        { data: "legalized" },
        { data: "known" },
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
                targets: [0, -1],
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

    function resetEdit() {
        $('#list-pengikutEdit').empty();
        $('#jam_pergiEdit').val('');
        $('#jam_kembaliEdit').val('');
        $('#jenis_kendaraanEdit').val('');
        $('#kepemilikan_kendaraanEdit').val('');
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
        $('#kepemilikan_kendaraan').val('');
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
        let kepemilikanKendaraan = $(this).data('kepemilikan-kendaraan');
        let kodeWilayah = $(this).data('kode-wilayah');
        let nomorPolisi = $(this).data('nomor-polisi');
        let seriAkhir = $(this).data('seri-akhir');
        let tempatAsal = $(this).data('tempat-asal');
        let tempatTujuan = $(this).data('tempat-tujuan');
        let keterangan = $(this).data('keterangan');
        let pengemudi = $(this).data('pengemudi');
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
                $('#kepemilikan_kendaraanEdit').val(kepemilikanKendaraan).trigger('change');
                $('#kode_wilayahEdit').val(kodeWilayah);
                $('#nomor_polisiEdit').val(nomorPolisi);
                $('#seri_akhirEdit').val(seriAkhir);
                $('#tempat_asalEdit').val(tempatAsal);
                $('#tempat_tujuanEdit').val(tempatTujuan);
                $('#keteranganEdit').val(keterangan);
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

    $('#kepemilikan_kendaraan').select2({
        dropdownParent: $('#modal-input'),
    })

    $('#kepemilikan_kendaraanEdit').select2({
        dropdownParent: $('#modal-edit'),
    })
    // END EVENT
});