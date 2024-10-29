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
        approvalTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

    $('.btnCloseDetail').on("click", function (){
        closeDetail();
    })

    // MODAL APPROVAL LEMBUR
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

    //MODAL DETAIL LEMBUR
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
                console.log(response);
                let header = response.data.header;
                let textTanggal = response.data.text_tanggal
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-detail-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' ? 'bg-danger' : ''}">
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
                                <span id="aktual_mulai_lembur_${i}"></span>
                            </td>
                            <td>
                                <span id="aktual_selesai_lembur_${i}"></span>
                            </td>
                            <td>
                                `+val.durasi+`
                            </td>
                        </tr>
                    `)
                    
                    $('#karyawan_id_' + i).text(val.nama);

                    //JOB DESCRIPTION
                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_' + i).html(`<ul>${jobDescriptions}</ul>`);
                    
                    //WAKTU
                    $('#rencana_mulai_lembur_' + i).text(val.rencana_mulai_lembur ? val.rencana_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#rencana_selesai_lembur_' + i).text(val.rencana_selesai_lembur ? val.rencana_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_mulai_lembur_' + i).text(val.aktual_mulai_lembur ? val.aktual_mulai_lembur.replace('T', ' ').replace(':', '.') : '-');
                    $('#aktual_selesai_lembur_' + i).text(val.aktual_selesai_lembur ? val.aktual_selesai_lembur.replace('T', ' ').replace(':', '.') : '-');
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

    $('#table-approval-lembur').on("change", '.aktualMulaiLembur', function () {
        let urutan = $(this).data('urutan');
        let startTime = $(this).val();
        $('#aktual_selesai_lembur_' + urutan).val('').attr('min', startTime);
    });

    $('#table-approval-lembur').on("change", '.aktualSelesaiLembur' , function () {
        let urutan = $(this).data('urutan');
        let startTime = $('#aktual_mulai_lembur_' + urutan).val();
        let endTime = $(this).val();
        let oldEndTime = $(this).data('selesai');
        if (endTime < startTime) {
            $(this).val(oldEndTime);
            showToast({ title: "Waktu selesai lembur tidak boleh kurang dari waktu mulai lembur", icon: "error" });
        }
    });


    //APPROVAL EVENT
    $('#approval-table').on('click', '.btnChecked', function(){
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let canApproved = $(this).data('can-approved');
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
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}">
                                <input type="text" name="karyawan_id[]"
                                    id="karyawan_id_${i}" class="form-control"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="text" name="job_description[]"
                                    id="job_description_${i}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_mulai_lembur[]"
                                    id="aktual_mulai_lembur_${i}" class="form-control aktualMulaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_selesai_lembur[]"
                                    id="aktual_selesai_lembur_${i}" class="form-control aktualSelesaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                `+val.durasi+`
                            </td>
                            <td>
                                `+ (canApproved && !isPlanned ? 
                                `
                                    <input type="checkbox" data-urutan="${i}" id="btn_reject_approval_lembur_${i}" value="Y" class="filled-in chk-col-primary" checked />
                                    <label for="btn_reject_approval_lembur_${i}"></label>
                                ` : '-' )+`
                            </td>
                        </tr>
                    `)

                    //Backup jika butuh fitur hapus pada  data
                    // <div class="btn-group">
                    //     <button type="button"
                    //         class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${i}" id="btn_delete_detail_lembur_${i}"><i
                    //             class="fas fa-trash"></i></button>
                    // </div>
                    $('#karyawan_id_' + i).val(val.nama);
                    $('#job_description_' + i).val(val.deskripsi_pekerjaan);
                    $('#aktual_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                    $('#aktual_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                    $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Checked');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);
                    let formData = new FormData($('#form-approval-lembur')[0]);
                    formData.append('is_planned', isPlanned ? 'Y' : 'N');

                    $('.btnUpdateStatusDetailLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        Swal.fire({
                            title: "Check Lembur",
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
                $('#jenis_hari').val(jenisHari);
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
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}">
                                <input type="text" name="karyawan_id[]"
                                    id="karyawan_id_${i}" class="form-control"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="text" name="job_description[]"
                                    id="job_description_${i}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_mulai_lembur[]"
                                    id="aktual_mulai_lembur_${i}" class="form-control aktualMulaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_selesai_lembur[]"
                                    id="aktual_selesai_lembur_${i}" class="form-control aktualSelesaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                `+val.durasi+`
                            </td>
                            <td>
                                `+ (canApproved && !isPlanned ? 
                                `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-' )+`
                            </td>
                        </tr>
                    `)

                    //CHECKBOX
                    $('#is_rencana_approved_' + i).on('change', function(){
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                        }
                    });

                    //Backup jika butuh fitur hapus pada  data
                    // <div class="btn-group">
                    //     <button type="button"
                    //         class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${i}" id="btn_delete_detail_lembur_${i}"><i
                    //             class="fas fa-trash"></i></button>
                    // </div>

                    $('#karyawan_id_' + i).val(val.nama);
                    $('#job_description_' + i).val(val.deskripsi_pekerjaan);
                    $('#aktual_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                    $('#aktual_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                    $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/approved/' + idLembur);

                    let formData = new FormData($('#form-approval-lembur')[0]);
                    formData.append('is_planned', isPlanned ? 'Y' : 'N');

                    $('.btnUpdateStatusDetailLembur').on("click", function (e){
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let approvedDetail = []; 
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function() { 
                            approvedDetail.push($(this).val()); 
                        }); 
                        
                        formData.append('approved_detail', approvedDetail);

                        Swal.fire({
                            title: "Approve Lembur",
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
                $('#jenis_hari').val(jenisHari);
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
        let canApproved = $(this).data('can-approved');
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
                let jenisHari = header.jenis_hari == 'WEEKEND' ? 'WE' : 'WD';
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();
                
                $.each(detail, function (i, val){
                    console.log(val)
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                                <input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}">
                                <input type="text" name="karyawan_id[]"
                                    id="karyawan_id_${i}" class="form-control"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="text" name="job_description[]"
                                    id="job_description_${i}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                    style="width: 100%;" readonly>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_mulai_lembur[]"
                                    id="aktual_mulai_lembur_${i}" class="form-control aktualMulaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                <input type="datetime-local" name="aktual_selesai_lembur[]"
                                    id="aktual_selesai_lembur_${i}" class="form-control aktualSelesaiLembur"
                                    style="width: 100%;" min="${new Date().toISOString().slice(0, 16)}" data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" ${isPlanned ? 'required' : 'readonly'}>
                                </input>
                            </td>
                            <td>
                                `+val.durasi+`
                            </td>
                            <td>
                                `+ (canApproved && !isPlanned ? 
                                `
                                    <input type="checkbox" data-urutan="${i}" id="btn_reject_approval_lembur_${i}" value="Y" class="filled-in chk-col-primary" checked />
                                    <label for="btn_reject_approval_lembur_${i}"></label>
                                ` : '-' )+`
                            </td>
                        </tr>
                    `)

                    //Backup jika butuh fitur hapus pada  data
                    // <div class="btn-group">
                    //     <button type="button"
                    //         class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${i}" id="btn_delete_detail_lembur_${i}"><i
                    //             class="fas fa-trash"></i></button>
                    // </div>
                    $('#karyawan_id_' + i).val(val.nama);
                    $('#job_description_' + i).val(val.deskripsi_pekerjaan);
                    $('#aktual_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                    $('#aktual_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                    $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
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
                $('#jenis_hari').val(jenisHari);
                openForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
        
    })
});