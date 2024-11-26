import { TempusDominus } from "@eonasdan/tempus-dominus";
import { faFiveIcons } from '@eonasdan/tempus-dominus/dist/plugins/fa-five';
import '@eonasdan/tempus-dominus/dist/css/tempus-dominus.min.css';
import '@popperjs/core';

$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    // ALERT & LOADING
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

    function updateLemburNotification(){
        $.ajax({
            url: base_url + '/get-planned-pengajuan-lembur-notification',
            method: 'GET',
            success: function(response){
                $('.notification-planned-pengajuan-lembur').html(response.data);
            }
        })
    }

    function initializeTempus(element, minDate = '', dateTime = '') {
        const tempusDominus = new TempusDominus(element, {
            useCurrent: false,
            stepping: 15,
            restrictions: {
                minDate: minDate || undefined
            },
            display: {
                icons: faFiveIcons,
                components: {
                    calendar: true,
                    date: true,
                    month: true,
                    year: true,
                    decades: true,
                    clock: true,
                    hours: true,
                    minutes: true,
                    seconds: false,
                },
                viewMode: 'calendar',
                calendarWeeks: true,
                sideBySide: true,
            },
            localization: {
                hourCycle: 'h23',
                dateFormats: {
                    LTS: 'h:mm:ss T',
                    LT: 'h:mm T',
                    L: 'yyyy-MM-dd HH:mm',
                    LL: 'MMMM d, yyyy',
                    LLL: 'MMMM d, yyyy h:mm T',
                    LLLL: 'dddd, MMMM d, yyyy h:mm T'
                },
                format: 'L',
            },
        });

        if(dateTime){
            tempusDominus.dates.setValue(dateTime);
        }
        return tempusDominus;
    }

    function tempusFormatter(datetime){
        let formattedDateTime = moment(dateTime, 'YYYY-MM-DD HH:mm').format('YYYY-MM-DDTHH:mm');
        return formattedDateTime;
    }

    function tempusReverseFormatter(datetime){
        let formattedDateTime = moment(dateTime, 'YYYY-MM-DDTHH:mm').format('YYYY-MM-DD HH:mm');
        return formattedDateTime;
    }

    // DATATABLE
    var columnsTable = [
        { data: "id_lembur" },
        { data: "issued_date" },
        { data: "rencana_mulai_lembur" },
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
        // responsive: true,
        scrollX: true,
        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [2,-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    function refreshTable() {
        lembureTable.search("").draw();
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    // ADD
    $('.btnAdd').on("click", function (){
        openForm();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

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
                    <input id="rencana_mulai_lembur_${count}" name="rencana_mulai_lembur[]" type="datetime-local" class="form-control rencanaMulaiLembur" data-urutan="${count}">
                </td>
                <td>
                    <input id="rencana_selesai_lembur_${count}" name="rencana_selesai_lembur[]" type="datetime-local" class="form-control rencanaSelesaiLembur" data-urutan="${count}">
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


        //DATE
        // const rencanaMulaiElement = document.getElementById('rencana_mulai_lembur_'+count);
        // const rencanaSelesaiElement = document.getElementById('rencana_selesai_lembur_'+count);
        // let now = moment().format('YYYY-MM-DD 00:00');
        
        // initializeTempus(rencanaMulaiElement, now);
        // initializeTempus(rencanaSelesaiElement, now);
        // rencanaMulaiElement.value = now;
        // rencanaSelesaiElement.value = now;

        // rencanaMulaiElement.addEventListener('change', function() {
        //     const selectedValue = this.value;
        //     rencanaSelesaiElement.value = selectedValue;
        //     initializeTempus(rencanaSelesaiElement, selectedValue);
        // });
        
        // rencanaSelesaiElement.addEventListener('change', function() {
        //     if(this.value < rencanaMulaiElement.value){
        //         rencanaMulaiElement.value = this.value;
        //     }
        // });

        let minDate = moment().format('YYYY-MM-DDT00:00');
         
        $('#rencana_mulai_lembur_' + count).attr('min', minDate);
        $('#rencana_selesai_lembur_' + count).attr('min', minDate);

        // $('#rencana_mulai_lembur_' + count).on('change', function(){
        //     let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
        //     $('#rencana_selesai_lembur_' + count).attr('min', startTime);
        //     if($(this).val() > $('#rencana_selesai_lembur_' + count).val()){
        //         $('#rencana_selesai_lembur_' + count).val($(this).val());
        //     }
        // });

        // $('#rencana_selesai_lembur_' + count).on('change', function(){
        //     if($(this).val() < $('#rencana_mulai_lembur_' + count).val()){
        //         $(this).val( $('#rencana_mulai_lembur_' + count).val());
        //     }
        // })
        

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
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })
    
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

    $('#jenis_hari').select2({
        dropdownParent: $('#modal-pengajuan-lembur')
    })

    // EDIT
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
                                <input id="rencana_mulai_lemburEdit_${i}" name="rencana_mulai_lemburEdit[]" type="datetime-local" class="form-control rencanaMulaiLemburEdit"
                                        data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>
                            </td>
                            <td>
                                <input id="rencana_selesai_lemburEdit_${i}" name="rencana_selesai_lemburEdit[]" type="datetime-local" class="form-control rencanaSelesaiLemburEdit"
                                         data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>
                            </td>
                            <td>
                                -
                            </td>
                        </tr>
                    `)


                    //DATE
                    // const rencanaMulaiElement = document.getElementById('rencana_mulai_lemburEdit_'+i);
                    // const rencanaSelesaiElement = document.getElementById('rencana_selesai_lemburEdit_'+i);
                    // let minDateMulai = moment(val.rencana_mulai_lembur).format('YYYY-MM-DD 00:00');
                    
                    // initializeTempus(rencanaMulaiElement, minDateMulai);
                    // initializeTempus(rencanaSelesaiElement, minDateMulai);

                    // rencanaMulaiElement.addEventListener('change', function() {
                    //     const selectedValue = this.value;
                    //     rencanaSelesaiElement.value = selectedValue;
                    //     if(rencanaSelesaiElement.value < selectedValue){
                    //         rencanaSelesaiElement.value = selectedValue;
                    //     }
                    //     initializeTempus(rencanaSelesaiElement, this.value);
                    // });

                    // rencanaSelesaiElement.addEventListener('change', function() {
                    //     if(this.value < rencanaMulaiElement.value){
                    //         rencanaMulaiElement.value = this.value;
                    //     }
                    // });

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');
         
                    $('#rencana_mulai_lemburEdit_' + i).attr('min', minDate);
                    $('#rencana_selesai_lemburEdit_' + i).attr('min', minDate);

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
    // $('#table-detail-lembur').on("change", '.rencanaMulaiLembur', function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $(this).val();
    //     // $('#rencana_selesai_lembur_' + urutan).val('').attr('min', startTime);
    // });

    // $('#table-detail-lembur').on("change", '.rencanaSelesaiLembur' , function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $('#rencana_mulai_lembur_' + urutan).val();
    //     let endTime = $(this).val();
    //     if (endTime < startTime) {
    //         $(this).val('');
    //         showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
    //     }
    // });

    // $('#table-detail-lembur-edit').on("change", '.rencanaMulaiLemburEditNew', function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $(this).val();
    //     $('#rencana_selesai_lemburEditNew_' + urutan).val('').attr('min', startTime);
    // });

    // $('#table-detail-lembur-edit').on("change", '.rencanaSelesaiLemburEditNew' , function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $('#rencana_mulai_lemburEditNew_' + urutan).val();
    //     let endTime = $(this).val();
    //     if (endTime < startTime) {
    //         $(this).val('');
    //         showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
    //     }
    // });

    // $('#table-detail-lembur-edit').on("change", '.rencanaMulaiLemburEdit', function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $(this).val();
    //     $('#rencana_selesai_lemburEdit_' + urutan).val('').attr('min', startTime);
    // });

    // $('#table-detail-lembur-edit').on("change", '.rencanaSelesaiLemburEdit' , function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $('#rencana_mulai_lemburEdit_' + urutan).val();
    //     let endTime = $(this).val();
    //     let oldEndTime = $(this).data('selesai');
    //     if (endTime < startTime) {
    //         $(this).val(oldEndTime);
    //         showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
    //     }
    // });

    //NEW 
    $('#table-detail-lembur').on('change','.rencanaMulaiLembur', function(){
        let urutan = $(this).data('urutan');
        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
        $('#rencana_selesai_lembur_' + urutan).attr('min', startTime);
        if($(this).val() > $('#rencana_selesai_lembur_' + urutan).val()){
            $('#rencana_selesai_lembur_' + urutan).val($(this).val());
        }
    });

    $('#table-detail-lembur').on('change', '.rencanaSelesaiLembur', function(){
        let urutan = $(this).data('urutan');
        if($(this).val() < $('#rencana_mulai_lembur_' + urutan).val()){
            $(this).val( $('#rencana_mulai_lembur_' + urutan).val());
        }
    })

    $('#table-detail-lembur-edit').on('change', '.rencanaMulaiLemburEdit', function(){
        let urutan = $(this).data('urutan');
        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
        $('#rencana_selesai_lemburEdit_' + urutan).attr('min', startTime);
        if($(this).val() > $('#rencana_selesai_lemburEdit_' + urutan).val()){
            $('#rencana_selesai_lemburEdit_' + urutan).val($(this).val());
        }
    });

    $('#table-detail-lembur-edit').on('change', '.rencanaSelesaiLemburEdit', function(){
        let urutan = $(this).data('urutan');
        if($(this).val() < $('#rencana_mulai_lemburEdit_' + urutan).val()){
            $(this).val($(this).data('selesai'));
        }
    })

    $('#table-detail-lembur-edit').on('change', '.rencanaMulaiLemburEditNew', function(){
        let urutan = $(this).data('urutan');
        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
        $('#rencana_selesai_lemburEditNew_' + urutan).attr('min', startTime);
        if($(this).val() > $('#rencana_selesai_lemburEditNew_' + urutan).val()){
            $('#rencana_selesai_lemburEditNew_' + urutan).val($(this).val());
        }
    });

    $('#table-detail-lembur-edit').on('change', '.rencanaSelesaiLemburEditNew',  function(){
        let urutan = $(this).data('urutan');
        if($(this).val() < $('#rencana_mulai_lemburEditNew_' + urutan).val()){
            $(this).val( $('#rencana_mulai_lemburEditNew_' + urutan).val());
        }
    })

    // EDIT
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
                    <input id="rencana_mulai_lemburEditNew_${detailCount}" name="rencana_mulai_lemburEditNew[]" type="datetime-local" class="form-control rencanaMulaiLemburEditNew"
                             data-urutan="${detailCount}" required>
                </td>
                <td>
                    <input id="rencana_selesai_lemburEditNew_${detailCount}" name="rencana_selesai_lemburEditNew[]" type="datetime-local" class="form-control rencanaSelesaiLemburEditNew"
                            data-urutan="${detailCount}" required>
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

        // const rencanaMulaiElement = document.getElementById('rencana_mulai_lemburEditNew_'+detailCount);
        // const rencanaSelesaiElement = document.getElementById('rencana_selesai_lemburEditNew_'+detailCount);
        // let now = moment().format('YYYY-MM-DD 00:00');
        
        // initializeTempus(rencanaMulaiElement, now);
        // initializeTempus(rencanaSelesaiElement, now);

        // rencanaMulaiElement.addEventListener('change', function() {
        //     const selectedValue = this.value;
        //     rencanaSelesaiElement.value = selectedValue;
        //     if(rencanaSelesaiElement.value < selectedValue){
        //         rencanaSelesaiElement.value = selectedValue;
        //     }
        //     initializeTempus(rencanaSelesaiElement, this.value);
        // });

        // rencanaSelesaiElement.addEventListener('change', function() {
        //     if(this.value < rencanaMulaiElement.value){
        //         rencanaMulaiElement.value = this.value;
        //     }
        // });

        let minDate = moment().format('YYYY-MM-DDT00:00');
         
        $('#rencana_mulai_lemburEditNew_' + detailCount).attr('min', minDate);
        $('#rencana_selesai_lemburEditNew_' + detailCount).attr('min', minDate);

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

    // $('#table-detail-lembur-edit').on("click",'.btnDeleteDetailLemburEdit', function (){
    //     detailCount--;
    //     let urutan = $(this).data('urutan');
    //     $(`#btn_delete_detail_lemburEdit_${urutan}`).closest('tr').remove();

    //     if(detailCount == 0){
    //         $('.btnUpdateDetailLembur').attr('disabled', true);
    //     } else {
    //         $('.btnUpdateDetailLembur').attr('disabled', false);
    //     }
    // });

    // $('#table-detail-lembur-edit').on("click",'.btnDeleteDetailLemburEditNew', function (){
    //     detailCount--;
    //     console.log(detailCount);
    //     let urutan = $(this).data('urutan');
    //     $(`#btn_delete_detail_lemburEditNew_${urutan}`).closest('tr').remove();

    //     if(detailCount == 0){
    //         $('.btnUpdateDetailLembur').attr('disabled', true);
    //     } else {
    //         $('.btnUpdateDetailLembur').attr('disabled', false);
    //     }
    // });

    // DELETE
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

    // DONE
    var modalDoneLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDoneLembur = new bootstrap.Modal(
        document.getElementById("modal-detail-lembur-done"),
        modalDoneLemburOptions
    );

    function openDone() {
        modalDoneLembur.show();
    }

    function closeDone() {
        resetDone();
        modalDoneLembur.hide();
    }

    function resetDone() {
        $('#list-detail-lembur-done').empty();
    }

    $('.btnDone').on("click", function (){
        openDone();
    })

    $('.btnCloseDone').on("click", function (){
        closeDone();
    })

    // $('#table-detail-lembur-done').on("change", '.aktualMulaiLembur', function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $(this).val();
    //     $('#aktual_selesai_lembur_' + urutan).val('').attr('min', startTime);
    // });

    // $('#table-detail-lembur-done').on("change", '.aktualSelesaiLembur' , function () {
    //     let urutan = $(this).data('urutan');
    //     let startTime = $('#aktual_mulai_lembur_' + urutan).val();
    //     let endTime = $(this).val();
    //     let oldEndTime = $(this).data('selesai');
    //     if (endTime < startTime) {
    //         $(this).val(oldEndTime);
    //         showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
    //     }
    // });

    $('#lembur-table').on('click', '.btnDone', function(){
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lemburDone').val(idLembur);

        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                loadingSwalClose();
                let header = response.data.header;
                let textTanggal = response.data.text_tanggal
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-detail-lembur-done').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if(attachmentLembur.length > 0){
                    $.each(attachmentLembur, function (i, val){
                        let ext = val.path.split('.').pop();
                        if(ext == 'pdf'){
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`);
                        } else {
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${val.path}" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`);
                        }
                    });
                } else {
                    previewElement.append(`<p>No Attachment Uploaded</p>`);
                }

                //REINITALIZE LIGHTBOX
                if ($('.image-popup-vertical-fit').length) {
                    $('.image-popup-vertical-fit').magnificPopup({
                        type: 'image',
                        closeOnContentClick: true,
                        mainClass: 'mfp-img-mobile',
                        image: {
                            verticalFit: true
                        }
                    });
                }
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : `-`}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' ? `<input id="aktual_mulai_lembur_${i}" name="aktual_mulai_lembur[]" type="datetime-local" class="form-control aktualMulaiLembur" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' ? `<input id="aktual_selesai_lembur_${i}" name="aktual_selesai_lembur[]" type="datetime-local" class="form-control aktualSelesaiLembur" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>` : `-`}
                            </td>
                            <td>
                            ${val.is_rencana_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_${i}" class="form-control ${val.is_rencana_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' ? `<input type="checkbox" name="is_aktual_approved" data-urutan="${i}" id="is_aktual_approved_${i}" class="filled-in chk-col-primary" ${val.is_aktual_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                <label for="is_aktual_approved_${i}"></label>` : `-`}
                            </td>
                        </tr>
                    `)

                    // if(val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'){
                    //     const aktualMulaiElement = document.getElementById('aktual_mulai_lembur_'+i);
                    //     const aktualSelesaiElement = document.getElementById('aktual_selesai_lembur_'+i);
                    //     let mulai = moment(val.rencana_mulai_lembur).format('YYYY-MM-DD 00:00');
                        
                    //     initializeTempus(aktualMulaiElement, mulai);
                    //     initializeTempus(aktualSelesaiElement, mulai);

                    //     aktualMulaiElement.addEventListener('change', function() {
                    //         const selectedValue = this.value;
                    //         aktualSelesaiElement.value = selectedValue;
                    //         if(aktualSelesaiElement.value < selectedValue){
                    //             aktualSelesaiElement.value = selectedValue;
                    //         }
                    //         initializeTempus(aktualSelesaiElement, this.value);
                    //     });

                    //     aktualSelesaiElement.addEventListener('change', function() {
                    //         if(this.value < aktualMulaiElement.value){
                    //             this.value = this.dataset.selesai;
                    //         }
                    //     });
                    // }

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');
         
                    $('#aktual_mulai_lembur_' + i).attr('min', minDate);
                    $('#aktual_selesai_lembur_' + i).attr('min', minDate);

                    $('#aktual_mulai_lembur_' + i).on('change', function(){
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#aktual_selesai_lembur_' + i).attr('min', startTime);
                        if($(this).val() > $('#aktual_selesai_lembur_' + i).val()){
                            $('#aktual_selesai_lembur_' + i).val($(this).val());
                        }
                    });

                    $('#aktual_selesai_lembur_' + i).on('change', function(){
                        if($(this).val() < $('#aktual_mulai_lembur_' + i).val()){
                            $(this).val($(this).data('selesai'));
                        }
                    })

                    $('#is_aktual_approved_' + i).on('change', function(){
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#aktual_mulai_lembur_' + i).attr('readonly', false);
                            $('#aktual_selesai_lembur_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#aktual_mulai_lembur_' + i).attr('readonly', true);
                            $('#aktual_selesai_lembur_' + i).attr('readonly', true);
                        }
                    });

                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    if(val.is_rencana_approved == 'Y'){
                        $('#aktual_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#aktual_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    } 
                    $('#karyawan_id_' + i).text(val.nama);

                    $('#form-detail-lembur-done').attr('action', base_url + '/lembure/pengajuan-lembur/done/' + idLembur);
                    $('.btnSubmitDoneLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-detail-lembur-done').attr('action');
                        let formData = new FormData($('#form-detail-lembur-done')[0]);

                        let aktualApproved = []; 
                        $("input:checkbox[name=is_aktual_approved]:checked").each(function() { 
                            aktualApproved.push($(this).val()); 
                        }); 
                        
                        formData.append('is_aktual_approved', aktualApproved);

                        Swal.fire({
                            title: "Aktual Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Selesai!",
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.value) {
                                loadingSwalShow();
                                $.ajax({
                                    url: url,
                                    data: formData,
                                    method:"POST",
                                    contentType: false,
                                    processData: false,
                                    dataType: "JSON",
                                    success: function (data) {
                                        updateLemburNotification();
                                        showToast({ title: data.message });
                                        refreshTable();
                                        loadingSwalClose();
                                        closeDone();
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        loadingSwalClose();
                                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                    },
                                });
                            }
                        });
                    });
                });
                $('#jenis_hariDone').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalDone').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusDone').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusDone').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusDone').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusDone').text(status).removeClass().addClass('badge badge-danger')
                }
                openDone();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
        
    })

    // DETAIL
    var modalDetailApprovalLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDetailApprovalLembur = new bootstrap.Modal(
        document.getElementById("modal-detail-approval-lembur"),
        modalDetailApprovalLemburOptions
    );
    
    function openDetail() {
        modalDetailApprovalLembur.show();
    }

    function closeDetail() {
        modalDetailApprovalLembur.hide();
    }

    $('.btnCloseDetail').on("click", function (){
        closeDetail();
    })

    $('#lembur-table').on("click", '.btnDetail' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        let isMember = $(this).data('is-member');

        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let header = response.data.header;
                let textTanggal = response.data.text_tanggal
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-detail-approval-lembur').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if(attachmentLembur.length > 0){
                    $.each(attachmentLembur, function (i, val){
                        let ext = val.path.split('.').pop();
                        if(ext == 'pdf'){
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`);
                        } else {
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${val.path}" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`);
                        }
                    });
                } else {
                    previewElement.append(`<p>No Attachment Uploaded</p>`);
                }

                //REINITALIZE LIGHTBOX
                if ($('.image-popup-vertical-fit').length) {
                    $('.image-popup-vertical-fit').magnificPopup({
                        type: 'image',
                        closeOnContentClick: true,
                        mainClass: 'mfp-img-mobile',
                        image: {
                            verticalFit: true
                        }
                    });
                }
                
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <span id="karyawan_id_detail_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_detail_${i}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_detail_${i}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_detail_${i}"></span>
                            </td>
                            <td>
                                `+val.durasi_rencana+`
                            </td>
                            <td>
                                <span id="aktual_mulai_lembur_detail_${i}"></span>
                            </td>
                            <td>
                                <span id="aktual_selesai_lembur_detail_${i}"></span>
                            </td>
                            <td>
                                `+val.durasi_aktual+`
                            </td>
                            <td>
                                `+(val.keterangan ? val.keterangan : '-')+`
                            </td>
                             <td>
                                `+(!isMember ? val.nominal : '-')+`
                            </td>
                        </tr>
                    `)
                    
                    $('#karyawan_id_detail_' + i).text(val.nama);

                    //JOB DESCRIPTION
                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_detail_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    //WAKTU
                    $('#rencana_mulai_lembur_detail_' + i).text(val.rencana_mulai_lembur ? val.rencana_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#rencana_selesai_lembur_detail_' + i).text(val.rencana_selesai_lembur ? val.rencana_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_mulai_lembur_detail_' + i).text(val.aktual_mulai_lembur ? val.aktual_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_selesai_lembur_detail_' + i).text(val.aktual_selesai_lembur ? val.aktual_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
                });
                $('#jenis_hariDetail').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalDetail').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-danger')
                }

                openDetail();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    //ATTACHMENT LEMBUR (LKH) HANDLING
    function checkIfImage(fileType) {
        const imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp']; 
        return imageMimeTypes.includes(fileType.toLowerCase());
    }   

    function compressAndDisplayImageSave(input, idLembur ) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
      
          reader.onload = function (e) {
            var image = new Image();
            const isImage = checkIfImage(input.files[0].type);
            if (isImage) {
                image.onload = function () {
                var canvas = document.createElement("canvas");
                var ctx = canvas.getContext("2d");
        
                const maxWidth = 1280;
                const maxHeight = 720; 
                const ratio = Math.min(maxWidth / image.width, maxHeight / image.height);
                canvas.width = Math.round(image.width * ratio);
                canvas.height = Math.round(image.height * ratio);
                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
        
                    const quality = 0.9; 
                    const compressedImageData = canvas.toDataURL("image/jpeg", quality);
                    const blob = dataURItoBlob(compressedImageData);

                    const fileName = input.files[0].name.split('.')[0] + '-compressed.jpg';
                    const fileType = input.files[0].type; 
                    const compressedFile = new File([blob], fileName, {type: fileType});

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    input.files = dataTransfer.files;
            
                    let formData = new FormData();
                    formData.append('lembur_id', idLembur);
                    formData.append('attachment_lembur', compressedFile);
                    let url = base_url + '/lembure/pengajuan-lembur/store-lkh';
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: "JSON",
                        success: function (data) {
                            showToast({ title: data.message });
                            getPreviewAttachmentLembur(idLembur);
                            $('#attachment_lembur').val('');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            showToast({ icon: "error", title: jqXHR.responseJSON.message });
                        }
                    });
                
                };
            } else {
                let formData = new FormData();
                formData.append('lembur_id', idLembur);
                formData.append('attachment_lembur', input.files[0]);
                let url = base_url + '/lembure/pengajuan-lembur/store-lkh';
                
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (data) {
                        showToast({ title: data.message });
                        getPreviewAttachmentLembur(idLembur);
                        $('#attachment_lembur').val('');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    }
                });
            }
      
            image.src = e.target.result;
          };
      
          reader.readAsDataURL(input.files[0]);
        }
    }

    $('#attachment_lembur').on("change", function () {
        loadingSwalShow();
        let idLembur = $('#id_lemburDone').val();
        compressAndDisplayImageSave(this, idLembur);
    });

    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeType = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const intArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
          intArray[i] = byteString.charCodeAt(i);
        }
        const blob = new Blob([arrayBuffer], { type: mimeType });
        return blob;
    }

    function getPreviewAttachmentLembur(idLembur){
        let url = base_url + '/lembure/pengajuan-lembur/get-attachment-lembur/' + idLembur;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let attachmentLembur = response.data;
                let previewElement = $('.previewAttachmentLembur').empty();

                if(attachmentLembur.length > 0){
                    $.each(attachmentLembur, function (i, val){
                        let ext = val.path.split('.').pop();
                        if(ext == 'pdf'){
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`);
                        } else {
                            previewElement.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${val.path}" data-title="Attachment Ke-${i}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${val.path}" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`);
                        }
                    });
                } else {
                    previewElement.append(`<p>No Attachment Uploaded</p>`);
                }

                //REINITALIZE LIGHTBOX
                if ($('.image-popup-vertical-fit').length) {
                    $('.image-popup-vertical-fit').magnificPopup({
                        type: 'image',
                        closeOnContentClick: true,
                        mainClass: 'mfp-img-mobile',
                        image: {
                            verticalFit: true
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    }

    //REJECT
    $('.btnRejectClose').on("click", function (){
        closeReject();
    })

    var modalRejectLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalRejectLembur = new bootstrap.Modal(
        document.getElementById("modal-reject-lembur"),
        modalRejectLemburOptions
    );
    
    function openReject() {
        $('.modal-title').text('Alasan Cancel')
        $('#btnSubmitReject').text('Cancel')
        modalRejectLembur.show();
    }

    function closeReject() {
        modalRejectLembur.hide();
    }

    $('#lembur-table').on("click", '.btnRejectLembur', function () {
        let idLembur = $(this).data('id-lembur');
        let url = base_url + '/lembure/approval-lembur/rejected/' + idLembur;
        $('#form-reject-lembur').attr('action', url);
        openReject();
    })

    $('#form-reject-lembur').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-reject-lembur').attr('action');

        var formData = new FormData($('#form-reject-lembur')[0]);
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
                closeReject();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });


});