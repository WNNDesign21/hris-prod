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
            timer: 3000, 
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
        { data: "id_lembur" },
        { data: "issued_date" },
        { data: "issued_by" },
        { data: "jenis_hari" },
        { data: "total_durasi"},
        { data: "status"},
        { data: "plan_checked_by"},
        { data: "plan_approved_by"},
        { data: "plan_legalized_by"},
        { data: "actual_checked_by"},
        { data: "actual_approved_by"},
        { data: "actual_legalized_by"},
        { data: "aksi"},
    ];

    var lembureTable = $("#lembur-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/pengajuan-lembur-datatable",
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
                targets: [0,-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        lembureTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openForm();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

    // MODAL TAMBAH LEMBUR
    var modalPengajuanLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalPengajuanLembur = new bootstrap.Modal(
        document.getElementById("modal-pengajuan-lembur"),
        modalPengajuanLemburOptions
    );

    function openForm() {
        modalPengajuanLembur.show();
    }

    function closeForm() {
        modalPengajuanLembur.hide();
    }

    function resetForm() {
        $('#list-detail-lembur').empty();
        count = 0;
        jumlah_detail_lembur = 0;
    }

    //SURAT PERINTAH LEMBUR
    let count = 0;
    let jumlah_detail_lembur = 0;
    $('#table-detail-lembur').on("click",'.btnDeleteDetailLembur', function (){
        jumlah_detail_lembur--;
        let urutan = $(this).data('urutan');
        $(`#btn_delete_detail_lembur_${urutan}`).closest('tr').remove();

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }
    });

    //TAMBAH DETAIL LEMBUR DAN LOGIC DIDALAMNYA
    $('.btnAddDetailLembur').on("click", function (){
        count++;
        jumlah_detail_lembur++;
        let tbody = $('#list-detail-lembur');
        tbody.append(`
             <tr>
                <td>
                    <select name="karyawan_id[]" id="karyawan_id_${count}" class="form-control" style="width: 100%;" required>
                    </select>
                </td>
                <td>
                    <input type="text" name="job_description[]"
                        id="job_description_${count}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                        style="width: 100%;" required>
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_mulai_lembur[]"
                        id="rencana_mulai_lembur_${count}" class="form-control rencanaMulaiLembur"
                        style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${count}" required>
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_selesai_lembur[]"
                        id="rencana_selesai_lembur_${count}" class="form-control rencanaSelesaiLembur"
                        style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${count}" required>
                    </input>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button"
                            class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${count}" id="btn_delete_detail_lembur_${count}"><i
                                class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `)

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }

        $("#karyawan_id_" + count).select2({
            dropdownParent: $('#modal-pengajuan-lembur'),
            ajax: {
            url: base_url + "/lembure/pengajuan-lembur/get-data-karyawan-lembur",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                search: params.term || "",
                page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='karyawan_id[]']").each(function () {
                if ($(this).val()) {
                    selectedIds.push($(this).val());
                }
                });

                let filteredData = data.results.filter(function (item) {
                return !selectedIds.includes(item.id);
                });

                return {
                    results: filteredData,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })
    //END TAMBAH DETAIL LEMBUR

    

    //Submit form lembur
    $('.btnSubmitDetailLembur').on("click", function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-pengajuan-lembur').attr('action');
        let formData = new FormData($('#form-pengajuan-lembur')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                refreshTable();
                loadingSwalClose();
                closeForm();
                resetForm();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    $('.btnUpdateDetailLembur').on("click", function (e){
        loadingSwalShow();
        e.preventDefault();
        let idLembur = $('#id_lembur').val();
        let url = base_url + '/lembure/pengajuan-lembur/update/' + idLembur;
        let formData = new FormData($('#form-pengajuan-lembur-edit')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                refreshTable();
                loadingSwalClose();
                closeFormEdit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    //Select2
    $('#jenis_hari').select2({
        dropdownParent: $('#modal-pengajuan-lembur')
    })

    //EDIT SPL
    // MODAL TAMBAH KARYAWAN
    $('.btnCloseEdit').on("click", function (){
        closeFormEdit();
    })

    var modalPengajuanLemburEditOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalPengajuanLemburEdit = new bootstrap.Modal(
        document.getElementById("modal-pengajuan-lembur-edit"),
        modalPengajuanLemburEditOptions
    );

    function openFormEdit() {
        modalPengajuanLemburEdit.show();
    }

    function closeFormEdit() {
        resetFormEdit();
        modalPengajuanLemburEdit.hide();
    }

    function resetFormEdit() {
        detailCount = 0;
        $('#id_lembur').val('');
        $('#jenis_hariEdit').val('');
        $('#list-detail-lembur-edit').empty();
        $('.btnUpdateDetailLembur').attr('disabled', false);
    }


    let detailCount = 0;
    $('#lembur-table').on("click", '.btnEdit' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lembur').val(idLembur);
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let header = response.data.header;
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let detail = response.data.detail_lembur;
                detailCount += detail.length;
                let tbody = $('#list-detail-lembur-edit').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr>
                            <td>
                                <input type="hidden" name="id_detail_lemburEdit[]" id="id_detail_lemburEdit_${i}">
                                <select name="karyawan_idEdit[]" id="karyawan_idEdit_${i}" class="form-control" style="width: 100%;" required>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="job_descriptionEdit[]"
                                    id="job_descriptionEdit_${i}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                    style="width: 100%;" required>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="rencana_mulai_lemburEdit[]"
                                    id="rencana_mulai_lemburEdit_${i}" class="form-control rencanaMulaiLemburEdit"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="rencana_selesai_lemburEdit[]"
                                    id="rencana_selesai_lemburEdit_${i}" class="form-control rencanaSelesaiLemburEdit"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>
                                </input>
                            </td>
                            <td>
                                -
                            </td>
                        </tr>
                    `)

                    //Backup jika butuh fitur hapus pada Edit data
                    // <div class="btn-group">
                    //     <button type="button"
                    //         class="btn btn-danger waves-effect btnDeleteDetailLemburEdit" data-urutan="${i}" id="btn_delete_detail_lemburEdit_${i}"><i
                    //             class="fas fa-trash"></i></button>
                    // </div>
                    selectedKaryawanLembur(i, val.karyawan_id);
                    $('#job_descriptionEdit_' + i).val(val.deskripsi_pekerjaan);
                    $('#rencana_mulai_lemburEdit_' + i).val(val.rencana_mulai_lembur);
                    $('#rencana_selesai_lemburEdit_' + i).val(val.rencana_selesai_lembur);
                    $('#id_detail_lemburEdit_' + i).val(val.id_detail_lembur);
                });
                
                $('#jenis_hariEdit').val(jenisHari);
                $('#jenis_hariEdit').select2({
                    dropdownParent: $('#modal-pengajuan-lembur-edit')
                })
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    function selectedKaryawanLembur(index, karyawanId){
        $.ajax({
            url: base_url + "/lembure/pengajuan-lembur/get-data-karyawan-lembur",
            method: 'GET',
            dataType: 'JSON',
            success: function (response) {
                let karyawan_ids = response.data;
                let select = $('#karyawan_idEdit_'+index);
                $.each(karyawan_ids, function (i, val){
                    select.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $("#karyawan_idEdit_" + index).val(karyawanId).trigger('change');
                $("#karyawan_idEdit_" + index).select2({
                    dropdownParent: $('#modal-pengajuan-lembur-edit'),
                    ajax: {
                    url: base_url + "/lembure/pengajuan-lembur/get-data-karyawan-lembur",
                    type: "post",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                        search: params.term || "",
                        page: params.page || 1,
                        };
                    },
                    processResults: function (data, params) {
                        let selectedIds = [];
                        $("select[name='karyawan_idEdit[]']").each(function () {
                        if ($(this).val()) {
                            selectedIds.push($(this).val());
                        }
                        });
        
                        let filteredData = data.results.filter(function (item) {
                        return !selectedIds.includes(item.id);
                        });
        
                        return {
                            results: filteredData,
                            pagination: {
                                more: (params.page * 10) < data.total_count
                            }
                        };
                    },
                    cache: true,
                    },
                });

                openFormEdit();
                loadingSwalClose();
            }
        })
    }

     //Rencana Mulai & Selesai Lembur Logic
    $('#table-detail-lembur').on("change", '.rencanaMulaiLembur', function () {
        let urutan = $(this).data('urutan');
        let startTime = $(this).val();
        $('#rencana_selesai_lembur_' + urutan).val('').attr('min', startTime);
    });

    $('#table-detail-lembur').on("change", '.rencanaSelesaiLembur' , function () {
        let urutan = $(this).data('urutan');
        let startTime = $('#rencana_mulai_lembur_' + urutan).val();
        let endTime = $(this).val();
        if (endTime < startTime) {
            $(this).val('');
            showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
        }
    });

    $('#table-detail-lembur-edit').on("change", '.rencanaMulaiLemburEditNew', function () {
        let urutan = $(this).data('urutan');
        let startTime = $(this).val();
        $('#rencana_selesai_lemburEditNew_' + urutan).val('').attr('min', startTime);
    });

    $('#table-detail-lembur-edit').on("change", '.rencanaSelesaiLemburEditNew' , function () {
        let urutan = $(this).data('urutan');
        let startTime = $('#rencana_mulai_lemburEditNew_' + urutan).val();
        let endTime = $(this).val();
        if (endTime < startTime) {
            $(this).val('');
            showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
        }
    });

    $('#table-detail-lembur-edit').on("change", '.rencanaMulaiLemburEdit', function () {
        let urutan = $(this).data('urutan');
        let startTime = $(this).val();
        $('#rencana_selesai_lemburEdit_' + urutan).val('').attr('min', startTime);
    });

    $('#table-detail-lembur-edit').on("change", '.rencanaSelesaiLemburEdit' , function () {
        let urutan = $(this).data('urutan');
        let startTime = $('#rencana_mulai_lemburEdit_' + urutan).val();
        let endTime = $(this).val();
        let oldEndTime = $(this).data('selesai');
        if (endTime < startTime) {
            $(this).val(oldEndTime);
            showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
        }
    });

    //ADD DATA IN DETAIL LEMBUR
    $('.btnAddDetailLemburEdit').on("click", function (){
        detailCount++;
        let tbody = $('#list-detail-lembur-edit');
        tbody.append(`
             <tr>
                <td>
                    <select name="karyawan_idEditNew[]" id="karyawan_idEditNew_${detailCount}" class="form-control" style="width: 100%;" required>
                    </select>
                </td>
                <td>
                    <input type="text" name="job_descriptionEditNew[]"
                        id="job_descriptionEditNew_${detailCount}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                        style="width: 100%;" required>
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_mulai_lemburEditNew[]"
                        id="rencana_mulai_lemburEditNew_${detailCount}" class="form-control rencanaMulaiLemburEditNew"
                        style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${detailCount}" required>
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_selesai_lemburEditNew[]"
                        id="rencana_selesai_lemburEditNew_${detailCount}" class="form-control rencanaSelesaiLemburEditNew"
                        style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${detailCount}" required>
                    </input>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button"
                            class="btn btn-danger waves-effect btnDeleteDetailLemburEditNew" data-urutan="${detailCount}" id="btn_delete_detail_lemburEditNew_${detailCount}"><i
                                class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `)

        if(detailCount == 0){
            $('.btnUpdateDetailLembur').attr('disabled', true);
        } else {
            $('.btnUpdateDetailLembur').attr('disabled', false);
        }

        $("#karyawan_idEditNew_" + detailCount).select2({
            dropdownParent: $('#modal-pengajuan-lembur-edit'),
            ajax: {
            url: base_url + "/lembure/pengajuan-lembur/get-data-karyawan-lembur",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                search: params.term || "",
                page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='karyawan_idEdit[]']").each(function () {
                    if ($(this).val()) {
                        selectedIds.push($(this).val());
                    }
                });

                $("select[name='karyawan_idEditNew[]']").each(function () {
                    if ($(this).val()) {
                        selectedIds.push($(this).val());
                    }
                });

                let filteredData = data.results.filter(function (item) {
                return !selectedIds.includes(item.id);
                });

                return {
                    results: filteredData,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })


    //DATA YANG EDIT
    $('#table-detail-lembur-edit').on("click",'.btnDeleteDetailLemburEdit', function (){
        detailCount--;
        let urutan = $(this).data('urutan');
        $(`#btn_delete_detail_lemburEdit_${urutan}`).closest('tr').remove();

        if(detailCount == 0){
            $('.btnUpdateDetailLembur').attr('disabled', true);
        } else {
            $('.btnUpdateDetailLembur').attr('disabled', false);
        }
    });

    //DATA TAMBAHAN KETIKA EDIT
    $('#table-detail-lembur-edit').on("click",'.btnDeleteDetailLemburEditNew', function (){
        detailCount--;
        let urutan = $(this).data('urutan');
        $(`#btn_delete_detail_lemburEditNew_${urutan}`).closest('tr').remove();

        if(detailCount == 0){
            $('.btnUpdateDetailLembur').attr('disabled', true);
        } else {
            $('.btnUpdateDetailLembur').attr('disabled', false);
        }
    });

    //DELETE
    $('#lembur-table').on('click', '.btnDelete', function (){
        var idLembur = $(this).data('id-lembur');
        Swal.fire({
            title: "Delete Lembur",
            text: "Apakah kamu yakin untuk menghapus Pengajuan Lembur ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/lembure/pengajuan-lembur/delete/' + idLembur;
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
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

});