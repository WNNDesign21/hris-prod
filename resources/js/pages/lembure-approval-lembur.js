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
            url: base_url + '/get-approval-lembur-notification',
            method: 'GET',
            success: function(response){
                $('.notification-approval-lembur').html(response.data);
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

    // DATATABLE
    var columnsTable = [
        { data: "id_lembur" },
        { data: "issued_date" },
        { data: "issued_by" },
        { data: "jenis_hari" },
        { data: "total_durasi"},
        { data: "total_nominal"},
        { data: "status"},
        { data: "plan_checked_by"},
        { data: "plan_approved_by"},
        { data: "plan_legalized_by"},
        { data: "actual_checked_by"},
        { data: "actual_approved_by"},
        { data: "actual_legalized_by"},
        { data: "action"},
    ];

    var approvalTable = $("#approval-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/approval-lembur-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var urutan = $('#filterUrutan').val();
                var jenisHari = $('#filterJenisHari').val();
                var aksi = $('#filterAksi').val();
                var status = $('#filterStatus').val();

                dataFilter.urutan = urutan;
                dataFilter.jenisHari = jenisHari;
                dataFilter.aksi = aksi;
                dataFilter.status = status;
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
                            "</strong></p>"+
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
        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [0,5,-1],
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
        approvalTable.search("").draw();
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })
    

    // PLANNING
    $('.btnClose').on("click", function (){
        closeForm();
    })

    var modalApprovalLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalApprovalLembur = new bootstrap.Modal(
        document.getElementById("modal-approval-lembur"),
        modalApprovalLemburOptions
    );
    
    function openForm() {
        modalApprovalLembur.show();
    }

    function closeForm() {
        $('#id_lembur').val('');
        $('.btnUpdateStatusDetailLembur').show();
        $('.btnUpdateStatusDetailLembur').text('Checked');
        $('#form-approval-lembur').attr('href', '#');
        modalApprovalLembur.hide();
    }

    $('#approval-table').on('click', '.btnChecked', function(){
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let canApproved = $(this).data('can-approved');
        let canChecked = $(this).data('can-checked');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lembur').val(idLembur);

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
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : ``}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N'  ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_mulai_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="rencana_mulai_lembur_${i}" name="mulai_lembur[]" type="text" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_${i}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_mulai_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_selesai_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="rencana_selesai_lembur_${i}" name="selesai_lembur[]" type="text" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_${i}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_selesai_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td> 
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_rencana : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && !isPlanned && val.is_rencana_approved == 'Y' && val.is_aktual_approved == 'Y' ? 
                                `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-' )+`
                            </td>
                        </tr>
                    `)

                    //DATE
                    if(val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'){
                        const rencanaMulaiElement = document.getElementById('rencana_mulai_lembur_'+i);
                        const rencanaSelesaiElement = document.getElementById('rencana_selesai_lembur_'+i);
                        console.log(rencanaMulaiElement, rencanaSelesaiElement)
                        let mulai = moment(val.rencana_mulai_lembur).format('YYYY-MM-DD 00:00');
                        
                        initializeTempus(rencanaMulaiElement, mulai);
                        initializeTempus(rencanaSelesaiElement, mulai);

                        rencanaMulaiElement.addEventListener('change', function() {
                            const selectedValue = this.value;
                            rencanaSelesaiElement.value = selectedValue;
                            if(rencanaSelesaiElement.value < selectedValue){
                                rencanaSelesaiElement.value = selectedValue;
                            }
                            initializeTempus(rencanaSelesaiElement, this.value);
                        });

                        rencanaSelesaiElement.addEventListener('change', function() {
                            if(this.value < rencanaMulaiElement.value){
                                this.value = this.dataset.selesai;
                            }
                        });
                    }

                    $('#is_rencana_approved_' + i).on('change', function(){
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#rencana_mulai_lembur_' + i).attr('readonly', false);
                            $('#rencana_selesai_lembur_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#rencana_mulai_lembur_' + i).attr('readonly', true);
                            $('#rencana_selesai_lembur_' + i).attr('readonly', true);
                        }
                    });

                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    if(val.is_rencana_approved == 'Y'){
                        $('#rencana_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    } 
                    $('#karyawan_id_' + i).text(val.nama);

                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Checked');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);

                    $('.btnUpdateStatusDetailLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let formData = new FormData($('#form-approval-lembur')[0]);
                        let approvedDetail = []; 
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function() { 
                            approvedDetail.push($(this).val()); 
                        }); 
                        
                        formData.append('is_planned', isPlanned ? 'Y' : 'N');
                        formData.append('approved_detail', approvedDetail);
                        formData.forEach((value, key) => {
                            console.log(key + ": " + value);
                        });

                        Swal.fire({
                            title: "Rencana Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Checked!",
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
                                        closeForm();
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
                $('#jenis_hariApproval').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-danger')
                }
                openForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
        
    })

    $('#approval-table').on('click', '.btnApproved', function(){
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let canApproved = $(this).data('can-approved');
        let canChecked = $(this).data('can-checked');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lembur').val(idLembur);

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
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : ``}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N'  ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_mulai_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="rencana_mulai_lembur_${i}" name="mulai_lembur[]" type="text" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_${i}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_mulai_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_selesai_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="rencana_selesai_lembur_${i}" name="selesai_lembur[]" type="text" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_${i}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_selesai_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td> 
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_rencana : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && !isPlanned && val.is_rencana_approved == 'Y' && val.is_aktual_approved == 'Y' ? 
                                `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-' )+`
                            </td>
                        </tr>
                    `)

                    //DATE
                    if(val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'){
                        const rencanaMulaiElement = document.getElementById('rencana_mulai_lembur_'+i);
                        const rencanaSelesaiElement = document.getElementById('rencana_selesai_lembur_'+i);
                        console.log(rencanaMulaiElement, rencanaSelesaiElement)
                        let mulai = moment(val.rencana_mulai_lembur).format('YYYY-MM-DD 00:00');
                        
                        initializeTempus(rencanaMulaiElement, mulai);
                        initializeTempus(rencanaSelesaiElement, mulai);

                        rencanaMulaiElement.addEventListener('change', function() {
                            const selectedValue = this.value;
                            rencanaSelesaiElement.value = selectedValue;
                            if(rencanaSelesaiElement.value < selectedValue){
                                rencanaSelesaiElement.value = selectedValue;
                            }
                            initializeTempus(rencanaSelesaiElement, this.value);
                        });

                        rencanaSelesaiElement.addEventListener('change', function() {
                            if(this.value < rencanaMulaiElement.value){
                                this.value = this.dataset.selesai;
                            }
                        });
                    }

                    $('#is_rencana_approved_' + i).on('change', function(){
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#rencana_mulai_lembur_' + i).attr('readonly', false);
                            $('#rencana_selesai_lembur_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#rencana_mulai_lembur_' + i).attr('readonly', true);
                            $('#rencana_selesai_lembur_' + i).attr('readonly', true);
                        }
                    });

                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    if(val.is_rencana_approved == 'Y'){
                        $('#rencana_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    } 
                    $('#karyawan_id_' + i).text(val.nama);

                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/approved/' + idLembur);

                    $('.btnUpdateStatusDetailLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let formData = new FormData($('#form-approval-lembur')[0]);
                        let approvedDetail = []; 
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function() { 
                            approvedDetail.push($(this).val()); 
                        }); 
                        
                        formData.append('is_planned', isPlanned ? 'Y' : 'N');
                        formData.append('approved_detail', approvedDetail);
                        formData.forEach((value, key) => {
                            console.log(key + ": " + value);
                        });

                        Swal.fire({
                            title: "Rencana Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Approved!",
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
                                        closeForm();
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
                $('#jenis_hariApproval').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-danger')
                }
                openForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
        
    })

    $('#approval-table').on('click', '.btnLegalized', function(){
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;

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
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    console.log(val)
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <span id="karyawan_id_${i}"></span>
                                </td>
                            <td>
                                <div id="job_description_${i}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_${i}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_${i}"></span>
                            </td>
                            <td>
                                `+(val.durasi_rencana)+`
                            </td>
                            <td>
                                `+(val.nominal)+`
                            </td>
                            <td>
                                -
                            </td>
                        </tr>
                    `)

                    $('#karyawan_id_' + i).text(val.nama);
                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    $('#rencana_mulai_lembur_' + i).text(val.rencana_mulai_lembur.replace('T', ' ').replace(':', '.'));
                    $('#rencana_selesai_lembur_' + i).text(val.rencana_selesai_lembur.replace('T', ' ').replace(':', '.'));
                    
                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Legalized');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/legalized/' + idLembur);
                    let formData = new FormData($('#form-approval-lembur')[0]);
                    formData.append('is_planned', isPlanned ? 'Y' : 'N');

                    $('.btnUpdateStatusDetailLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        Swal.fire({
                            title: "Legalized Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Legalized!",
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
                                        closeForm();
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        loadingSwalClose();
                                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                    },
                                });
                            }
                        });
                    })
                });
                $('#jenis_hariApproval').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-danger')
                }

                openForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
        
    })

    // AKTUAL
    $('#table-approval-lembur').on("change", '.mulaiLembur', function () {
        let urutan = $(this).data('urutan');
        let startTime = $(this).val();
        $('#rencana_selesai_lembur_' + urutan).val('').attr('min', startTime);
    });

    $('#table-approval-lembur').on("change", '.selesaiLembur' , function () {
        let urutan = $(this).data('urutan');
        let startTime = $('#rencana_mulai_lembur_' + urutan).val();
        let endTime = $(this).val();
        let oldEndTime = $(this).data('selesai');
        if (endTime < startTime) {
            $(this).val(oldEndTime);
            showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
        }
        console.log($(this).val())
    });

    $('.btnCloseAktual').on("click", function (){
        closeAktual();
    })

    var modalAktualApprovalLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalAktualApprovalLembur = new bootstrap.Modal(
        document.getElementById("modal-aktual-approval-lembur"),
        modalAktualApprovalLemburOptions
    );
    
    function openAktual() {
        modalAktualApprovalLembur.show();
    }

    function closeAktual() {
        modalAktualApprovalLembur.hide();
    }

    // $('#approval-table').on("click", '.btnCheckedAktual' , function () {
    //     loadingSwalShow();
    //     let idLembur = $(this).data('id-lembur');
    //     let isPlanned = $(this).data('is-planned');
    //     let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
    //     $('#id_lemburAktual').val(idLembur);

    //     $.ajax({
    //         url: url,
    //         method: "GET",
    //         dataType: "JSON",
    //         success: function (response) {
    //             let header = response.data.header;
    //             let textTanggal = response.data.text_tanggal
    //             let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
    //             let status = header.status;
    //             let detail = response.data.detail_lembur;
    //             let tbody = $('#list-aktual-approval-lembur').empty();
                
    //             $.each(detail, function (i, val){
    //                 tbody.append(`
    //                      <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
    //                         <td>
    //                             <span id="karyawan_id_aktual_${i}"></span>
    //                         </td>
    //                         <td>
    //                             <div id="job_description_aktual_${i}"></div>
    //                         </td>
    //                         <td>
    //                             <span id="rencana_mulai_lembur_aktual_${i}"></span>
    //                         </td>
    //                         <td>
    //                             <span id="rencana_selesai_lembur_aktual_${i}"></span>
    //                         </td>
    //                         <td>
    //                             `+(val.durasi_rencana)+`
    //                         </td>
    //                         <td>
    //                             <span id="aktual_mulai_lembur_aktual_${i}"></span>
    //                         </td>
    //                         <td>
    //                             <span id="aktual_selesai_lembur_aktual_${i}"></span>
    //                         </td>
    //                         <td>
    //                             `+(val.durasi_aktual)+`
    //                         </td>
    //                         <td>
    //                             `+(val.keterangan ? val.keterangan : '-')+`
    //                         </td>
    //                         <td>
    //                             `+(val.nominal)+`
    //                         </td>
    //                     </tr>
    //                 `)
                    
    //                 $('#karyawan_id_aktual_' + i).text(val.nama);

    //                 //JOB DESCRIPTION
    //                 let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
    //                 $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
    //                 //WAKTU
    //                 $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur ? val.rencana_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
    //                 $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur ? val.rencana_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
    //                 $('#aktual_mulai_lembur_aktual_' + i).text(val.aktual_mulai_lembur ? val.aktual_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
    //                 $('#aktual_selesai_lembur_aktual_' + i).text(val.aktual_selesai_lembur ? val.aktual_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
    //             });
    //             $('#jenis_hariAktual').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
    //             $('#text_tanggalAktual').text(textTanggal);

    //             //STATUS
    //             if (status == 'WAITING'){
    //                 $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
    //             } else if (status == 'PLANNED'){
    //                 $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
    //             } else if (status == 'COMPLETED'){
    //                 $('#statusAktual').text(status).removeClass().addClass('badge badge-success')
    //             } else {
    //                 $('#statusAktual').text(status).removeClass().addClass('badge badge-danger')
    //             }

    //             $('.btnUpdateAktualLembur').text('Checked');
    //             $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);
    //             let formData = new FormData($('#form-aktual-approval-lembur')[0]);
    //             formData.append('is_planned', isPlanned ? 'Y' : 'N');

    //             $('.btnUpdateAktualLembur').on("click", function (e){
    //                 loadingSwalShow();
    //                 e.preventDefault();
    //                 let url = $('#form-aktual-approval-lembur').attr('action');
    //                 Swal.fire({
    //                     title: "Aktual Lembur",
    //                     text: "Apakah anda yakin dengan detail lembur ini?",
    //                     icon: "warning",
    //                     showCancelButton: true,
    //                     confirmButtonColor: "#3085d6",
    //                     cancelButtonColor: "#d33",
    //                     confirmButtonText: "Yes, Tandai sebagai Checked!",
    //                     allowOutsideClick: false,
    //                 }).then((result) => {
    //                     if (result.value) {
    //                         loadingSwalShow();
    //                         $.ajax({
    //                             url: url,
    //                             data: formData,
    //                             method:"POST",
    //                             contentType: false,
    //                             processData: false,
    //                             dataType: "JSON",
    //                             success: function (data) {
    //                                 updateLemburNotification();
    //                                 showToast({ title: data.message });
    //                                 refreshTable();
    //                                 loadingSwalClose();
    //                                 closeAktual();
    //                             },
    //                             error: function (jqXHR, textStatus, errorThrown) {
    //                                 loadingSwalClose();
    //                                 showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //                             },
    //                         });
    //                     }
    //                 });
    //             })

    //             openAktual();
    //             loadingSwalClose();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //         },
    //     }); 
    // })
    $('#approval-table').on("click", '.btnCheckedAktual' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lemburAktual').val(idLembur);

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
                let tbody = $('#list-aktual-approval-lembur').empty();
             
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_aktual_${i}"></input>` : `-`}
                                <span id="karyawan_id_aktual_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_aktual_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                `+(val.durasi_rencana)+`
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_mulai_aktual_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="aktual_mulai_lembur_aktual_${i}" name="mulai_lembur[]" type="text" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_aktual_${i}" data-urutan="${i}" data-mulai="${val.aktual_mulai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_mulai_aktual_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_selesai_aktual_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="aktual_selesai_lembur_aktual_${i}" name="selesai_lembur[]" type="text" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_aktual_${i}" data-urutan="${i}" data-selesai="${val.aktual_selesai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_selesai_aktual_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td> 
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_aktual : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_aktual_${i}" class="form-control ${val.is_aktual_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                        </tr>
                    `)

                    //DATE
                    if(val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'){
                        const aktualMulaiElement = document.getElementById('aktual_mulai_lembur_aktual_'+i);
                        const aktualSelesaiElement = document.getElementById('aktual_selesai_lembur_aktual_'+i);
                        console.log(aktualMulaiElement, aktualSelesaiElement)
                        let mulai = moment(val.aktual_mulai_lembur).format('YYYY-MM-DD 00:00');
                        
                        initializeTempus(aktualMulaiElement, mulai);
                        initializeTempus(aktualSelesaiElement, mulai);

                        aktualMulaiElement.addEventListener('change', function() {
                            const selectedValue = this.value;
                            aktualSelesaiElement.value = selectedValue;
                            if(aktualSelesaiElement.value < selectedValue){
                                aktualSelesaiElement.value = selectedValue;
                            }
                            initializeTempus(aktualSelesaiElement, this.value);
                        });

                        aktualSelesaiElement.addEventListener('change', function() {
                            if(this.value < aktualMulaiElement.value){
                                this.value = this.dataset.selesai;
                            }
                        });
                    }

                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    if(val.is_rencana_approved == 'Y'){
                        $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur);
                        $('#keterangan_aktual_' + i).val(val.keterangan);
                        $('#id_detail_lembur_aktual_' + i).val(val.id_detail_lembur);
                        $('#aktual_mulai_lembur_aktual_' + i).val(val.aktual_mulai_lembur);
                        $('#aktual_selesai_lembur_aktual_' + i).val(val.aktual_selesai_lembur);
                    } 
                    $('#karyawan_id_aktual_' + i).text(val.nama);

                    $('.btnUpdateAktualLembur').removeClass('btn-warning').addClass('btn-success').text('Checked');
                    $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);

                    $('.btnUpdateAktualLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-aktual-approval-lembur').attr('action');
                        let formData = new FormData($('#form-aktual-approval-lembur')[0]);
                        formData.append('is_planned', isPlanned ? 'Y' : 'N');

                        Swal.fire({
                            title: "Aktual Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Checked!",
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
                                        closeAktual();
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
                $('#jenis_hariAktual').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalAktual').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-danger')
                }
                openAktual();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    $('#approval-table').on("click", '.btnApprovedAktual' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lemburAktual').val(idLembur);

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
                let tbody = $('#list-aktual-approval-lembur').empty();
             
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_aktual_${i}"></input>` : `-`}
                                <span id="karyawan_id_aktual_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_aktual_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                `+(val.durasi_rencana)+`
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_mulai_aktual_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="aktual_mulai_lembur_aktual_${i}" name="mulai_lembur[]" type="text" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_aktual_${i}" data-urutan="${i}" data-mulai="${val.aktual_mulai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_mulai_aktual_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'  ? `<div class="input-group" id="datetimepicker_selesai_aktual_${i}" data-td-target-input="nearest"
                                    data-td-target-toggle="nearest">
                                    <input id="aktual_selesai_lembur_aktual_${i}" name="selesai_lembur[]" type="text" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N'  ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_aktual_${i}" data-urutan="${i}" data-selesai="${val.aktual_selesai_lembur}" required>
                                    <span class="input-group-text" data-td-target="#datetimepicker_selesai_aktual_${i}"
                                        data-td-toggle="datetimepicker">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>` : `-`}
                            </td>
                            <td> 
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_aktual : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_aktual_${i}" class="form-control ${val.is_aktual_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                        </tr>
                    `)

                    //DATE
                    if(val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N'){
                        const aktualMulaiElement = document.getElementById('aktual_mulai_lembur_aktual_'+i);
                        const aktualSelesaiElement = document.getElementById('aktual_selesai_lembur_aktual_'+i);
                        console.log(aktualMulaiElement, aktualSelesaiElement)
                        let mulai = moment(val.aktual_mulai_lembur).format('YYYY-MM-DD 00:00');
                        
                        initializeTempus(aktualMulaiElement, mulai);
                        initializeTempus(aktualSelesaiElement, mulai);

                        aktualMulaiElement.addEventListener('change', function() {
                            const selectedValue = this.value;
                            aktualSelesaiElement.value = selectedValue;
                            if(aktualSelesaiElement.value < selectedValue){
                                aktualSelesaiElement.value = selectedValue;
                            }
                            initializeTempus(aktualSelesaiElement, this.value);
                        });

                        aktualSelesaiElement.addEventListener('change', function() {
                            if(this.value < aktualMulaiElement.value){
                                this.value = this.dataset.selesai;
                            }
                        });
                    }

                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    if(val.is_rencana_approved == 'Y'){
                        $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur);
                        $('#keterangan_aktual_' + i).val(val.keterangan);
                        $('#id_detail_lembur_aktual_' + i).val(val.id_detail_lembur);
                        $('#aktual_mulai_lembur_aktual_' + i).val(val.aktual_mulai_lembur);
                        $('#aktual_selesai_lembur_aktual_' + i).val(val.aktual_selesai_lembur);
                    } 
                    $('#karyawan_id_aktual_' + i).text(val.nama);

                    $('.btnUpdateAktualLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/approved/' + idLembur);

                    $('.btnUpdateAktualLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-aktual-approval-lembur').attr('action');
                        let formData = new FormData($('#form-aktual-approval-lembur')[0]);
                        formData.append('is_planned', isPlanned ? 'Y' : 'N');

                        Swal.fire({
                            title: "Aktual Lembur",
                            text: "Apakah anda yakin dengan detail lembur ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Tandai sebagai Approved!",
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
                                        closeAktual();
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
                $('#jenis_hariAktual').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalAktual').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-danger')
                }
                openAktual();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    $('#approval-table').on("click", '.btnLegalizedAktual' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;
        $('#id_lemburAktual').val(idLembur);

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
                let tbody = $('#list-aktual-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <span id="karyawan_id_aktual_${i}"></span>
                                </td>
                            <td>
                                <div id="job_description_aktual_${i}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                `+(val.durasi_rencana)+`
                            </td>
                            <td>
                                <span id="aktual_mulai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                <span id="aktual_selesai_lembur_aktual_${i}"></span>
                            </td>
                            <td>
                                `+(val.durasi_aktual)+`
                            </td>
                            <td>
                                `+(val.keterangan ? val.keterangan : '-')+`
                            </td>
                            <td>
                                `+(val.nominal)+`
                            </td>
                        </tr>
                    `)
                    
                    $('#karyawan_id_aktual_' + i).text(val.nama);

                    //JOB DESCRIPTION
                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    //WAKTU
                    $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur ? val.rencana_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur ? val.rencana_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_mulai_lembur_aktual_' + i).text(val.aktual_mulai_lembur ? val.aktual_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_selesai_lembur_aktual_' + i).text(val.aktual_selesai_lembur ? val.aktual_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
                });
                $('#jenis_hariAktual').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalAktual').text(textTanggal);

                //STATUS
                if (status == 'WAITING'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED'){
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-success')
                } else {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-danger')
                }

                $('.btnUpdateAktualLembur').text('Legalized');
                $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/legalized/' + idLembur);
                let formData = new FormData($('#form-aktual-approval-lembur')[0]);
                formData.append('is_planned', isPlanned ? 'Y' : 'N');

                $('.btnUpdateAktualLembur').on("click", function (e){
                    loadingSwalShow();
                    e.preventDefault();
                    let url = $('#form-aktual-approval-lembur').attr('action');
                    Swal.fire({
                        title: "Aktual Lembur",
                        text: "Apakah anda yakin dengan detail lembur ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Tandai sebagai Legalized!",
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
                                    closeAktual();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    loadingSwalClose();
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                },
                            });
                        }
                    });
                })

                openAktual();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    // DETAIL
    $('.btnCloseDetail').on("click", function (){
        closeDetail();
    })

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


    $('#approval-table').on("click", '.btnDetail' , function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let url = base_url + '/lembure/pengajuan-lembur/get-data-lembur/' + idLembur;

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
                                `+val.nominal+`
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
        modalRejectLembur.show();
    }

    function closeReject() {
        modalRejectLembur.hide();
    }

    $('#approval-table').on("click", '.btnRejectLembur', function () {
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

    //FILTER
    $('.btnFilter').on("click", function (){
        openFilter();
    });

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    });

    var modalFilterOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilter = new bootstrap.Modal(
        document.getElementById("modal-filter"),
        modalFilterOptions
    );
    
    function openFilter() {
        modalFilter.show();
    }

    function closeFilter() {
        modalFilter.hide();
    }

    $('.btnResetFilter').on('click', function(){
        $('#filterUrutan').val('');
        $('#filterJenisHari').val([]);
        $('#filterAksi').val('');
        $('#filterStatus').val([]);
    })

    $('#filterUrutan').select2({
        dropdownParent: $('#modal-filter')
    });
    $('#filterJenisHari').select2({
        dropdownParent: $('#modal-filter')
    });
    $('#filterAksi').select2({
        dropdownParent: $('#modal-filter')
    });
    $('#filterStatus').select2({
        dropdownParent: $('#modal-filter')
    });

    $(".btnSubmitFilter").on("click", function () {
        approvalTable.draw();
        closeFilter();
    });
});