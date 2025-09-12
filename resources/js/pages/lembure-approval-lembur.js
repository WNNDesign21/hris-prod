import { TempusDominus } from "@eonasdan/tempus-dominus";
import '@eonasdan/tempus-dominus/dist/css/tempus-dominus.min.css';
import { faFiveIcons } from '@eonasdan/tempus-dominus/dist/plugins/fa-five';
import '@popperjs/core';

$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () { };

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

    function updateLemburNotification() {
        $.ajax({
            url: base_url + '/get-approval-lembur-notification',
            method: 'GET',
            success: function (response) {
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

        if (dateTime) {
            tempusDominus.dates.setValue(dateTime);
        }
        return tempusDominus;
    }

    // DATATABLE
    var columnsTable = [
        { data: "id_lembur" },
        { data: "issued_date" },
        { data: "rencana_mulai_lembur" },
        { data: "issued_by" },
        { data: "departemen" },
        { data: "jenis_hari" },
        { data: "total_durasi" },
        { data: "total_nominal" },
        { data: "status" },
        { data: "plan_checked_by" },
        { data: "plan_approved_by" },
        { data: "plan_reviewed_by" },
        { data: "plan_legalized_by" },
        { data: "actual_checked_by" },
        { data: "actual_approved_by" },
        { data: "actual_reviewed_by" },
        { data: "actual_legalized_by" },
        { data: "action" },
    ];

    let mustChecked = false;
    var approvalTable = $("#approval-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        stateSave: !0,
        ajax: {
            url: base_url + "/lembure/approval-lembur-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var urutan = $('#filterUrutan').val();
                var jenisHari = $('#filterJenisHari').val();
                var aksi = $('#filterAksi').val();
                var status = $('#filterStatus').val();
                var departemen = $('#filterDepartemen').val();
                var periode = $('#filterPeriode').val();

                dataFilter.urutan = urutan;
                dataFilter.jenisHari = jenisHari;
                dataFilter.aksi = aksi;
                dataFilter.status = status;
                dataFilter.mustChecked = mustChecked;
                dataFilter.departemen = departemen;
                dataFilter.periode = periode;
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
        scrollX: true,
        columnDefs: [
            {
                orderable: false,
                targets: [2, 7, -1],
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
        var currentPage = approvalTable.page();
        approvalTable.search('').draw(false);
        approvalTable.page(currentPage).draw(false);
    }

    $('.btnReload').on("click", function () {
        refreshTable();
    })


    // PLANNING
    $('.btnClose').on("click", function () {
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

    $('#approval-table').on('click', '.btnChecked', function () {
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
                let jenisLembur = header.jenis_lembur;
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();

                $.each(detail, function (i, val) {
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : ``}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_mulai_lembur_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_selesai_lembur_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}"  data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>` : `-`}
                            </td>
                            <td id="durasi-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_rencana : '-'}
                            </td>
                            <td id="nominal-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && !isPlanned && val.is_rencana_approved == 'Y' && val.is_aktual_approved == 'Y' ?
                            `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#rencana_mulai_lembur_' + i).attr('min', minDate);
                    $('#rencana_selesai_lembur_' + i).attr('min', minDate);

                    $('#rencana_mulai_lembur_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#rencana_selesai_lembur_' + i).attr('min', startTime);
                        if ($(this).val() > $('#rencana_selesai_lembur_' + i).val()) {
                            $('#rencana_selesai_lembur_' + i).val($(this).val());
                            $('#durasi-' + i).text('-');
                            $('#nominal-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#rencana_selesai_lembur_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-' + i).text(durasi);
                                    $('#nominal-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#rencana_selesai_lembur_' + i).on('change', function () {
                        if ($(this).val() < $('#rencana_mulai_lembur_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#rencana_mulai_lembur_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-' + i).text(durasi);
                                $('#nominal-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })

                    $('#is_rencana_approved_' + i).on('change', function () {
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

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    }
                    $('#karyawan_id_' + i).text(val.nama);

                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);

                    $('.btnUpdateStatusDetailLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let formData = new FormData($('#form-approval-lembur')[0]);
                        let approvedDetail = [];
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('is_planned', isPlanned ? 'Y' : 'N');
                        formData.append('approved_detail', approvedDetail);

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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariApproval').val(jenisHari).select2({
                    dropdownParent: $('#modal-approval-lembur'),
                    disabled: true
                });
                $('#jenis_lemburApproval').text(jenisLembur ? jenisLembur : '-');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    $('#approval-table').on('click', '.btnApproved', function () {
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
                let jenisLembur = header.jenis_lembur;
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();

                $.each(detail, function (i, val) {
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : ``}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_mulai_lembur_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_selesai_lembur_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}"  data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>` : `-`}
                            </td>
                            <td id="durasi-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_rencana : '-'}
                            </td>
                            <td id="nominal-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && !isPlanned && val.is_rencana_approved == 'Y' && val.is_aktual_approved == 'Y' ?
                            `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    }

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#rencana_mulai_lembur_' + i).attr('min', minDate);
                    $('#rencana_selesai_lembur_' + i).attr('min', minDate);

                    $('#rencana_mulai_lembur_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#rencana_selesai_lembur_' + i).attr('min', startTime);
                        if ($(this).val() > $('#rencana_selesai_lembur_' + i).val()) {
                            $('#rencana_selesai_lembur_' + i).val($(this).val());
                            $('#durasi-' + i).text('-');
                            $('#nominal-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#rencana_selesai_lembur_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-' + i).text(durasi);
                                    $('#nominal-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#rencana_selesai_lembur_' + i).on('change', function () {
                        if ($(this).val() < $('#rencana_mulai_lembur_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#rencana_mulai_lembur_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-' + i).text(durasi);
                                $('#nominal-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })

                    $('#is_rencana_approved_' + i).on('change', function () {
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

                    $('#karyawan_id_' + i).text(val.nama);

                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/approved/' + idLembur);

                    $('.btnUpdateStatusDetailLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let formData = new FormData($('#form-approval-lembur')[0]);
                        let approvedDetail = [];
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('is_planned', isPlanned ? 'Y' : 'N');
                        formData.append('approved_detail', approvedDetail);

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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariApproval').val(jenisHari).select2({
                    dropdownParent: $('#modal-approval-lembur'),
                    disabled: true
                });
                $('#jenis_lemburApproval').text(jenisLembur ? jenisLembur : '-');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    $('#approval-table').on('click', '.btnLegalized', function () {
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
                let jenisLembur = header.jenis_lembur;
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-approval-lembur').empty();

                $.each(detail, function (i, val) {
                    tbody.append(`
                         <tr class="${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}">
                            <td>
                            ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>` : ``}
                                <span id="karyawan_id_${i}"></span>
                            </td>
                            <td>
                                <div id="job_description_${i}" class="${val.is_rencana_approved == 'N' && val.is_aktual_approved !== 'N' ? 'text-white' : ''}"></div>
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_mulai_lembur_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}" data-urutan="${i}" data-mulai="${val.rencana_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="rencana_selesai_lembur_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}"  data-urutan="${i}" data-selesai="${val.rencana_selesai_lembur}" required>` : `-`}
                            </td>
                            <td id="durasi-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_rencana : '-'}
                            </td>
                            <td id="nominal-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ (!isPlanned && val.is_rencana_approved == 'Y' && val.is_aktual_approved == 'Y' ?
                            `
                                    <input type="checkbox" name="is_rencana_approved" data-urutan="${i}" id="is_rencana_approved_${i}" class="filled-in chk-col-primary" ${val.is_rencana_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_rencana_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_' + i).val(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_' + i).val(val.rencana_selesai_lembur);
                        $('#id_detail_lembur_' + i).val(val.id_detail_lembur);
                    }

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#rencana_mulai_lembur_' + i).attr('min', minDate);
                    $('#rencana_selesai_lembur_' + i).attr('min', minDate);

                    $('#rencana_mulai_lembur_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#rencana_selesai_lembur_' + i).attr('min', startTime);
                        if ($(this).val() > $('#rencana_selesai_lembur_' + i).val()) {
                            $('#rencana_selesai_lembur_' + i).val($(this).val());
                            $('#durasi-' + i).text('-');
                            $('#nominal-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#rencana_selesai_lembur_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-' + i).text(durasi);
                                    $('#nominal-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#rencana_selesai_lembur_' + i).on('change', function () {
                        if ($(this).val() < $('#rencana_mulai_lembur_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#rencana_mulai_lembur_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-' + i).text(durasi);
                                $('#nominal-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })

                    $('#is_rencana_approved_' + i).on('change', function () {
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

                    $('#karyawan_id_' + i).text(val.nama);

                    $('.btnUpdateStatusDetailLembur').removeClass('btn-warning').addClass('btn-success').text('Legalized');
                    $('#form-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/legalized/' + idLembur);

                    $('.btnUpdateStatusDetailLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-approval-lembur').attr('action');
                        let formData = new FormData($('#form-approval-lembur')[0]);
                        let approvedDetail = [];
                        $("input:checkbox[name=is_rencana_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('is_planned', isPlanned ? 'Y' : 'N');
                        formData.append('approved_detail', approvedDetail);

                        Swal.fire({
                            title: "Rencana Lembur",
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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariApproval').val(jenisHari).select2({
                    dropdownParent: $('#modal-approval-lembur')
                });
                $('#jenis_lemburApproval').text(jenisLembur ? jenisLembur : '-');
                $('#text_tanggalApproval').text(textTanggal);

                //STATUS
                if (status == 'WAITING') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusApproval').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    $('.btnCloseAktual').on("click", function () {
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

    $('#approval-table').on("click", '.btnCheckedAktual', function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let canApproved = $(this).data('can-approved');
        let canChecked = $(this).data('can-checked');
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
                let jenisLembur = header.jenis_lembur;
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-aktual-approval-lembur').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if (attachmentLembur.length > 0) {
                    $.each(attachmentLembur, function (i, val) {
                        let ext = val.path.split('.').pop();
                        if (ext == 'pdf') {
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

                $.each(detail, function (i, val) {
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
                            <td id="checkin-aktual-${i}">-</td>
                            <td id="checkout-aktual-${i}">-</td>
                            <td id="match_status-aktual-${i}">-</td>
                            <td>
                                `+ (val.durasi_rencana) + `
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="aktual_mulai_lembur_aktual_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_aktual_${i}" data-urutan="${i}" data-mulai="${val.aktual_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="aktual_selesai_lembur_aktual_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_aktual_${i}" data-urutan="${i}" data-selesai="${val.aktual_selesai_lembur}" required>` : `-`}
                            </td>
                            <td id="durasi-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_aktual : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_aktual_${i}" class="form-control ${val.is_aktual_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td id="nominal-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && isPlanned && val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ?
                            `
                                    <input type="checkbox" name="is_aktual_approved" data-urutan="${i}" id="is_aktual_approved_${i}" class="filled-in chk-col-primary" ${val.is_aktual_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_aktual_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur);
                        $('#keterangan_aktual_' + i).val(val.keterangan);
                        $('#id_detail_lembur_aktual_' + i).val(val.id_detail_lembur);
                        $('#aktual_mulai_lembur_aktual_' + i).val(val.aktual_mulai_lembur);
                        $('#aktual_selesai_lembur_aktual_' + i).val(val.aktual_selesai_lembur);
                    }

                    $('#is_aktual_approved_' + i).on('change', function () {
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', false);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', true);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', true);
                        }
                    });

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#aktual_mulai_lembur_aktual_' + i).attr('min', minDate);
                    $('#aktual_selesai_lembur_aktual_' + i).attr('min', minDate);

                    $('#aktual_mulai_lembur_aktual_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#aktual_selesai_lembur_aktual_' + i).attr('min', startTime);
                        if ($(this).val() > $('#aktual_selesai_lembur_aktual_' + i).val()) {
                            $('#aktual_selesai_lembur_aktual_' + i).val($(this).val());
                            $('#durasi-aktual-' + i).text('-');
                            $('#nominal-aktual-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#aktual_selesai_lembur_aktual_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-aktual-' + i).text(durasi);
                                    $('#nominal-aktual-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#aktual_selesai_lembur_aktual_' + i).on('change', function () {
                        if ($(this).val() < $('#aktual_mulai_lembur_aktual_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#aktual_mulai_lembur_aktual_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-aktual-' + i).text(durasi);
                                $('#nominal-aktual-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })


                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);

                    // Panggil fetch data presensi
                    const idKaryawan = val.karyawan_id;
                    const isoDate = getIsoDate(val, header, textTanggal);
                    const overtimeData = { jenis_hari: header.jenis_hari, jenis_lembur: header.jenis_lembur, aktual_mulai: val.aktual_mulai_lembur, aktual_selesai: val.aktual_selesai_lembur };
                    if (idKaryawan && isoDate) {
                        fillCheckInOutForRow(i, idKaryawan, isoDate, overtimeData);
                    }

                    $('#karyawan_id_aktual_' + i).text(val.nama);

                    $('.btnUpdateAktualLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/checked/' + idLembur);

                    $('.btnUpdateAktualLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-aktual-approval-lembur').attr('action');
                        let formData = new FormData($('#form-aktual-approval-lembur')[0]);

                        let approvedDetail = [];
                        $("input:checkbox[name=is_aktual_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('approved_detail', approvedDetail);
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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariAktual').val(jenisHari).select2({
                    dropdownParent: $('#modal-aktual-approval-lembur'),
                    disabled: true
                });
                $('#jenis_lemburAktual').text(jenisLembur ? jenisLembur : '-');
                $('#text_tanggalAktual').text(textTanggal);

                //STATUS
                if (status == 'WAITING') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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


    $('#approval-table').on("click", '.btnApprovedAktual', function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        let isPlanned = $(this).data('is-planned');
        let canApproved = $(this).data('can-approved');
        let canChecked = $(this).data('can-checked');
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
                let jenisLembur = header.jenis_lembur;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-aktual-approval-lembur').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if (attachmentLembur.length > 0) {
                    $.each(attachmentLembur, function (i, val) {
                        let ext = val.path.split('.').pop();
                        if (ext == 'pdf') {
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

                $.each(detail, function (i, val) {
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
                                ${val.durasi_rencana ? val.durasi_rencana : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="aktual_mulai_lembur_aktual_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_aktual_${i}" data-urutan="${i}" data-mulai="${val.aktual_mulai_lembur}" required>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input id="aktual_selesai_lembur_aktual_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_aktual_${i}" data-urutan="${i}" data-selesai="${val.aktual_selesai_lembur}" required>` : `-`}
                            </td>
                            <td id="checkin-aktual-${i}">-</td>
                            <td id="checkout-aktual-${i}">-</td>
                            <td id="match_status-aktual-${i}">-</td>
                            <td id="durasi-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_aktual : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_aktual_${i}" class="form-control ${val.is_aktual_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td id="nominal-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ ((canApproved || canChecked) && isPlanned && val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ?
                            `
                                    <input type="checkbox" name="is_aktual_approved" data-urutan="${i}" id="is_aktual_approved_${i}" class="filled-in chk-col-primary" ${val.is_aktual_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_aktual_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur);
                        $('#keterangan_aktual_' + i).val(val.keterangan);
                        $('#id_detail_lembur_aktual_' + i).val(val.id_detail_lembur);
                        $('#aktual_mulai_lembur_aktual_' + i).val(val.aktual_mulai_lembur);
                        $('#aktual_selesai_lembur_aktual_' + i).val(val.aktual_selesai_lembur);
                    }

                    $('#is_aktual_approved_' + i).on('change', function () {
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', false);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', true);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', true);
                        }
                    });

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#aktual_mulai_lembur_aktual_' + i).attr('min', minDate);
                    $('#aktual_selesai_lembur_aktual_' + i).attr('min', minDate);

                    $('#aktual_mulai_lembur_aktual_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#aktual_selesai_lembur_aktual_' + i).attr('min', startTime);
                        if ($(this).val() > $('#aktual_selesai_lembur_aktual_' + i).val()) {
                            $('#aktual_selesai_lembur_aktual_' + i).val($(this).val());
                            $('#durasi-aktual-' + i).text('-');
                            $('#nominal-aktual-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#aktual_selesai_lembur_aktual_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-aktual-' + i).text(durasi);
                                    $('#nominal-aktual-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#aktual_selesai_lembur_aktual_' + i).on('change', function () {
                        if ($(this).val() < $('#aktual_mulai_lembur_aktual_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#aktual_mulai_lembur_aktual_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-aktual-' + i).text(durasi);
                                $('#nominal-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })


                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);

                    // Panggil fetch data presensi
                    const idKaryawan = val.karyawan_id;
                    const isoDate = getIsoDate(val, header, textTanggal);
                    const overtimeData = { jenis_hari: header.jenis_hari, jenis_lembur: header.jenis_lembur, aktual_mulai: val.aktual_mulai_lembur, aktual_selesai: val.aktual_selesai_lembur };
                    if (idKaryawan && isoDate) {
                        fillCheckInOutForRow(i, idKaryawan, isoDate, overtimeData);
                    }

                    $('#karyawan_id_aktual_' + i).text(val.nama);

                    $('.btnUpdateAktualLembur').removeClass('btn-warning').addClass('btn-success').text('Approved');
                    $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/approved/' + idLembur);

                    $('.btnUpdateAktualLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-aktual-approval-lembur').attr('action');
                        let formData = new FormData($('#form-aktual-approval-lembur')[0]);
                        let approvedDetail = [];
                        $("input:checkbox[name=is_aktual_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('approved_detail', approvedDetail);
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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariAktual').val(jenisHari).select2({
                    dropdownParent: $('#modal-aktual-approval-lembur'),
                    disabled: true
                });
                $('#text_tanggalAktual').text(textTanggal);
                $('#jenis_lemburAktual').text(jenisLembur ? jenisLembur : '-');

                //STATUS
                if (status == 'WAITING') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    $('#approval-table').on("click", '.btnLegalizedAktual', function () {
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
                let jenisLembur = header.jenis_lembur;
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-aktual-approval-lembur').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if (attachmentLembur.length > 0) {
                    $.each(attachmentLembur, function (i, val) {
                        let ext = val.path.split('.').pop();
                        if (ext == 'pdf') {
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

                $.each(detail, function (i, val) {
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
                                `+ (val.durasi_rencana) + `
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<div class="input-group">
                                    <input id="aktual_mulai_lembur_aktual_${i}" name="mulai_lembur[]" type="datetime-local" class="form-control mulaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_mulai_aktual_${i}" data-urutan="${i}" data-mulai="${val.aktual_mulai_lembur}" required>
                                </div>` : `-`}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<div class="input-group">
                                    <input id="aktual_selesai_lembur_aktual_${i}" name="selesai_lembur[]" type="datetime-local" class="form-control selesaiLembur ${val.is_rencana_approved == 'N' || val.is_aktual_approved == 'N' ? 'bg-danger' : ''}""
                                        data-td-target="#datetimepicker_selesai_aktual_${i}" data-urutan="${i}" data-selesai="${val.aktual_selesai_lembur}" required>
                                </div>` : `-`}
                            </td>
                            <td id="checkin-aktual-${i}">
                                ${val.check_in ? val.check_in : '-'}
                            </td>
                            <td id="checkout-aktual-${i}">
                                ${val.check_out ? val.check_out : '-'}
                            </td>
                            <td id="match_status-aktual-${i}">
                                ${val.match_status ? val.match_status : '-'}
                            </td>
                            <td id="durasi-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.durasi_aktual : '-'}
                            </td>
                            <td>
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? `<input type="text" name="keterangan[]"
                                    id="keterangan_aktual_${i}" class="form-control ${val.is_aktual_approved == 'N' ? 'bg-danger text-white' : ''}"
                                    style="width: 100%;">
                                </input>` : '-'}
                            </td>
                            <td id="nominal-aktual-${i}">
                                ${val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ? val.nominal : '-'}
                            </td>
                            <td>
                                `+ (isPlanned && val.is_rencana_approved !== 'N' && val.is_aktual_approved !== 'N' ?
                            `
                                    <input type="checkbox" name="is_aktual_approved" data-urutan="${i}" id="is_aktual_approved_${i}" class="filled-in chk-col-primary" ${val.is_aktual_approved == 'Y' ? 'checked' : ''} value="${val.id_detail_lembur}"/>
                                    <label for="is_aktual_approved_${i}"></label>
                                ` : '-') + `
                            </td>
                        </tr>
                    `)

                    if (val.is_rencana_approved == 'Y') {
                        $('#rencana_mulai_lembur_aktual_' + i).text(val.rencana_mulai_lembur);
                        $('#rencana_selesai_lembur_aktual_' + i).text(val.rencana_selesai_lembur);
                        $('#keterangan_aktual_' + i).val(val.keterangan);
                        $('#id_detail_lembur_aktual_' + i).val(val.id_detail_lembur);
                        $('#aktual_mulai_lembur_aktual_' + i).val(val.aktual_mulai_lembur);
                        $('#aktual_selesai_lembur_aktual_' + i).val(val.aktual_selesai_lembur);
                    }

                    $('#is_aktual_approved_' + i).on('change', function () {
                        if ($(this).is(':checked')) {
                            $(this).attr('checked', true);
                            if ($(this).closest('tr').hasClass('bg-danger')) {
                                $(this).closest('tr').removeClass('bg-danger');
                            }
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', false);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', false);
                        } else {
                            $(this).removeAttr('checked');
                            $(this).closest('tr').addClass('bg-danger');
                            $('#aktual_mulai_lembur_aktual_' + i).attr('readonly', true);
                            $('#aktual_selesai_lembur_aktual_' + i).attr('readonly', true);
                        }
                    });

                    let minDate = moment(val.rencana_mulai_lembur).format('YYYY-MM-DDT00:00');

                    $('#aktual_mulai_lembur_aktual_' + i).attr('min', minDate);
                    $('#aktual_selesai_lembur_aktual_' + i).attr('min', minDate);

                    $('#aktual_mulai_lembur_aktual_' + i).on('change', function () {
                        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
                        $('#aktual_selesai_lembur_aktual_' + i).attr('min', startTime);
                        if ($(this).val() > $('#aktual_selesai_lembur_aktual_' + i).val()) {
                            $('#aktual_selesai_lembur_aktual_' + i).val($(this).val());
                            $('#durasi-aktual-' + i).text('-');
                            $('#nominal-aktual-' + i).text('-');
                        } else {
                            $.ajax({
                                url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                                method: 'POST',
                                data: { mulai_lembur: $(this).val(), selesai_lembur: $('#aktual_selesai_lembur_aktual_' + i).val() },
                                dataType: 'JSON',
                                success: function (response) {
                                    let durasi = response.data.durasi;
                                    let nominal = response.data.nominal;
                                    $('#durasi-aktual-' + i).text(durasi);
                                    $('#nominal-aktual-' + i).text(nominal);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                                }
                            });
                        }
                    });

                    $('#aktual_selesai_lembur_aktual_' + i).on('change', function () {
                        if ($(this).val() < $('#aktual_mulai_lembur_aktual_' + i).val()) {
                            $(this).val($(this).data('selesai'));
                        }

                        $.ajax({
                            url: base_url + '/lembure/approval-lembur/get-calculation-durasi-and-nominal-lembur/' + $('#id_detail_lembur_aktual_' + i).val(),
                            method: 'POST',
                            data: { mulai_lembur: $('#aktual_mulai_lembur_aktual_' + i).val(), selesai_lembur: $(this).val() },
                            dataType: 'JSON',
                            success: function (response) {
                                let durasi = response.data.durasi;
                                let nominal = response.data.nominal;
                                $('#durasi-aktual-' + i).text(durasi);
                                $('#nominal-aktual-' + i).text(nominal);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                showToast({ icon: "error", title: jqXHR.responseJSON.message });
                            }
                        });
                    })


                    let jobDescriptions = val.deskripsi_pekerjaan.split(',').map(desc => `<li>${desc.trim()}</li>`).join('');
                    $('#job_description_aktual_' + i).html(`<ul>${jobDescriptions}</ul>`);

                    $('#karyawan_id_aktual_' + i).text(val.nama);
                    const idKaryawan = val.karyawan_id;
                    const isoDate = getIsoDate(val, header, textTanggal);

                    // Debug jika kosong
                    if (!idKaryawan) console.warn('idKaryawan kosong di row', i, val);
                    if (!isoDate) console.warn('Tanggal referensi tidak valid di row', i, val, header, textTanggal);

                    // Panggil fetch checkin/checkout jika keduanya ada
                    if (val.karyawan_id && isoDate) {
                        fillCheckInOutForRow(i, val.karyawan_id, isoDate, { jenis_hari: header.jenis_hari, jenis_lembur: header.jenis_lembur, aktual_mulai: val.aktual_mulai_lembur, aktual_selesai: val.aktual_selesai_lembur });
                    }

                    $('.btnUpdateAktualLembur').removeClass('btn-warning').addClass('btn-success').text('Legalized');
                    $('#form-aktual-approval-lembur').attr('action', base_url + '/lembure/approval-lembur/legalized/' + idLembur);

                    $('.btnUpdateAktualLembur').off('click').on("click", function (e) {
                        loadingSwalShow();
                        e.preventDefault();
                        let url = $('#form-aktual-approval-lembur').attr('action');
                        let formData = new FormData($('#form-aktual-approval-lembur')[0]);
                        let approvedDetail = [];
                        $("input:checkbox[name=is_aktual_approved]:checked").each(function () {
                            approvedDetail.push($(this).val());
                        });

                        formData.append('approved_detail', approvedDetail);
                        formData.append('is_planned', isPlanned ? 'Y' : 'N');

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
                                    method: "POST",
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
                            } else {
                                loadingSwalClose();
                            }
                        });
                    });
                });
                $('#jenis_hariAktual').val(jenisHari).select2({
                    dropdownParent: $('#modal-aktual-approval-lembur'),
                });
                $('#text_tanggalAktual').text(textTanggal);
                $('#jenis_lemburAktual').text(jenisLembur ? jenisLembur : '-');

                //STATUS
                if (status == 'WAITING') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusAktual').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    // DETAIL
    $('.btnCloseDetail').on("click", function () {
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


    $('#approval-table').on("click", '.btnDetail', function () {
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
                let jenisLembur = header.jenis_lembur; // This was already correct
                let status = header.status;
                let detail = response.data.detail_lembur;
                let tbody = $('#list-detail-approval-lembur').empty();

                //attachment
                let attachmentLembur = response.data.attachment;
                let previewElement = $('.previewAttachmentLembur').empty();

                if (attachmentLembur.length > 0) {
                    $.each(attachmentLembur, function (i, val) {
                        let ext = val.path.split('.').pop();
                        if (ext == 'pdf') {
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

                // Show/Hide Check In/Out columns based on status
                if (status === 'COMPLETED' || status === 'REJECTED') { // Show if actuals are relevant
                    $('#th-check-in, #th-check-out, #th-match-status').show();
                } else { // Hide for WAITING, PLANNED
                    $('#th-check-in, #th-check-out, #th-match-status').hide();
                }

                $.each(detail, function (i, val) {
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
                                ${val.durasi_rencana} ${val.rencana_last_changed_by ? `<br><p>Last Changed : <br><small>${val.rencana_last_changed_by} <br>(${val.rencana_last_changed_at})</small></p>` : ''}
                            </td>
                            <td>
                                <span id="aktual_mulai_lembur_detail_${i}"></span>
                            </td>
                            <td>
                                <span id="aktual_selesai_lembur_detail_${i}"></span>
                            </td>
                            <td id="checkin-aktual-${i}" class="col-check-in" style="display: none;">-</td>
                            <td id="checkout-aktual-${i}" class="col-check-out" style="display: none;">-</td>
                            <td id="match_status-aktual-${i}" class="col-match-status" style="display: none;">-</td>
                            </td>
                            <td>
                                ${val.durasi_aktual} ${val.aktual_last_changed_by ? `<br><p>Last Changed : <br><small>${val.aktual_last_changed_by} <br>(${val.aktual_last_changed_at})</small></p>` : ''}
                            </td>
                            <td>
                                ${val.keterangan ? val.keterangan : '-'}
                            </td>
                            <td>
                                ${val.nominal}
                            </td>
                        </tr>
                    `);
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

                // Show/Hide Check In/Out columns based on status
                if (status === 'COMPLETED' || status === 'REJECTED') { // Show if actuals are relevant
                    $('#th-check-in, #th-check-out, #th-match-status').show();
                    $('.col-check-in, .col-check-out, .col-match-status').show();

                    // Panggil fetch data presensi setelah kolom ditampilkan
                    $.each(detail, function (i, val) {
                        const idKaryawan = val.karyawan_id;
                        const isoDate = getIsoDate(val, header, textTanggal);
                        const overtimeData = { jenis_hari: header.jenis_hari, jenis_lembur: header.jenis_lembur, aktual_mulai: val.aktual_mulai_lembur, aktual_selesai: val.aktual_selesai_lembur };
                        if (idKaryawan && isoDate) {
                            fillCheckInOutForRow(i, idKaryawan, isoDate, overtimeData);
                        }
                    });
                } else { // Hide for WAITING, PLANNED
                    $('#th-check-in, #th-check-out, #th-match-status').hide();
                    $('.col-check-in, .col-check-out, .col-match-status').hide();
                }
                $('#jenis_hariDetail').text(jenisHari == 'WE' ? 'Weekend' : 'Weekday');
                $('#text_tanggalDetail').text(textTanggal);
                $('#jenis_lemburDetail').text(jenisLembur ? jenisLembur : '-');

                //STATUS
                if (status == 'WAITING') {
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-warning')
                } else if (status == 'PLANNED') {
                    $('#statusDetail').text(status).removeClass().addClass('badge badge-info')
                } else if (status == 'COMPLETED') {
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

    $('#approval-table').on("click", '.btnRollback', function () {
        loadingSwalShow();
        let idLembur = $(this).data('id-lembur');
        Swal.fire({
            title: "Rollback Lembur",
            text: "Apakah kamu yakin untuk mengembalikan Lembur ini ke kondisi sebelum legalized?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, rollback it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/lembure/approval-lembur/rollback/' + idLembur;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "patch",
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

    //REJECT
    $('.btnRejectClose').on("click", function () {
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

    $('#form-reject-lembur').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-reject-lembur').attr('action');

        var formData = new FormData($('#form-reject-lembur')[0]);
        $.ajax({
            url: url,
            data: formData,
            method: "POST",
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
    $('.btnFilter').on("click", function () {
        openFilter();
    });

    $('.btnCloseFilter').on("click", function () {
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

    $('.btnResetFilter').on('click', function () {
        $('#filterPeriode').val('');
        $('#filterUrutan').val('');
        $('#filterDepartemen').val('');
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
    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter')
    });

    $(".btnSubmitFilter").on("click", function () {
        approvalTable.draw();
        closeFilter();
    });

    //MUST CHECKED
    $('.btnMustChecked').on("click", function () {
        mustChecked = !mustChecked;
        if (mustChecked == true) {
            $(this).text('Unchecked');
            $(this).prepend('<i class="far fa-check-circle"></i> ');
        } else {
            $(this).text('Must Checked');
            $(this).prepend('<i class="far fa-check-circle"></i> ');
        }
        approvalTable.draw();
    });

    $('.btnCloseCC').on("click", function () {
        closeCrossCheck();
    })

    var modalCrossCheckOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalCrossCheck = new bootstrap.Modal(
        document.getElementById("modal-cross-check-approval-lembur"),
        modalCrossCheckOptions
    );

    function openCrossCheck() {
        modalCrossCheck.show();
    }

    function closeCrossCheck() {
        modalCrossCheck.hide();
    }

    function getDataCrossCheck(idKaryawan, date) {
        loadingSwalShow();
        $.ajax({
            url: base_url + '/lembure/approval-lembur/get-list-data-cross-check',
            method: 'POST',
            data: { id_karyawan: idKaryawan, date: date },
            dataType: 'JSON',
            success: function (response) {
                let data = response.data;
                let body = $('#list-data-cross-check').empty();
                $.each(data, function (i, val) {
                    body.append(`
                        <div class="box">
                            <div class="box-body p-4">
                                <h4>${val.scan_date} WIB</h4>
                            </div>
                        </div>
                    `)
                });
                loadingSwalClose();
                openCrossCheck();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    // pakai ini untuk cari YYYY-MM-DD dari berbagai sumber
    function getIsoDate(val, header, textTanggal) {
        // urutkan kandidat dari yang paling andal
        const candidates = [
            val?.rencana_mulai_lembur,        // ex: 2025-08-29 16:00 atau 2025-08-29T16:00
            header?.tanggal_iso,              // kirim ini dari backend
            header?.tanggal,                  // kalau sudah ISO
        ];

        // kalau benar-benar terpaksa baru coba textTanggal + locale id
        if (textTanggal) candidates.push(textTanggal);

        // daftar format yang dicoba
        const fmts = [
            'YYYY-MM-DDTHH:mm',
            'YYYY-MM-DD HH:mm:ss',
            'YYYY-MM-DD HH:mm',
            'YYYY-MM-DD',
            'MM/DD/YYYY hh:mm A',
            'DD/MM/YYYY HH:mm',
            'DD-MM-YYYY HH:mm',
            'DD-MM-YYYY',
            'D MMMM YYYY',            // fallback umum
            'dddd, D MMMM YYYY',      // “Jumat, 29 Agustus 2025”
        ];

        // coba parse berurutan
        if (typeof moment?.locale === 'function') moment.locale('id');
        for (const c of candidates) {
            if (!c) continue;
            for (const f of fmts) {
                const m = moment(c, f, true);
                if (m.isValid()) return m.format('YYYY-MM-DD');
            }
            // coba parse bebas
            const m2 = moment(c);
            if (m2.isValid()) return m2.format('YYYY-MM-DD');
        }
        return null;
    }

    function fillCheckInOutForRow(i, idKaryawan, anyDatetimeStr, overtimeData) {
        // Ambil tanggal referensi dari rencana_mulai (atau header) -> YYYY-MM-DD
        const date = moment(anyDatetimeStr).format('YYYY-MM-DD');

        $.ajax({
            url: base_url + '/lembure/approval-lembur/get-list-data-cross-check',
            method: 'POST',
            dataType: 'JSON',
            data: { id_karyawan: idKaryawan, date },
            success: function (res) {
                const d = res.data || {};
                const checkIn = d.check_in ? moment(d.check_in) : null;
                const checkOut = d.check_out ? moment(d.check_out) : null;
                const aktualMulai = overtimeData.aktual_mulai ? moment(overtimeData.aktual_mulai) : null;
                const aktualSelesai = overtimeData.aktual_selesai ? moment(overtimeData.aktual_selesai) : null;

                $('#checkin-aktual-' + i).text(checkIn ? checkIn.format('YYYY-MM-DD hh:mm A') : '-');
                $('#checkout-aktual-' + i).text(checkOut ? checkOut.format('YYYY-MM-DD hh:mm A') : '-');

                let matchStatus = '-';
                let match = false;

                if (overtimeData.jenis_hari === 'WEEKEND') {
                    if (checkIn && aktualMulai && checkOut && aktualSelesai) {
                        // Toleransi 0 menit, checkin harus <= aktual mulai, checkout harus >= aktual selesai
                        if (checkIn.isSameOrBefore(aktualMulai) && checkOut.isSameOrAfter(aktualSelesai)) {
                            match = true;
                        }
                    }
                } else { // WEEKDAY
                    if (overtimeData.jenis_lembur === 'AWAL') {
                        if (checkIn && aktualMulai) {
                            // Toleransi 0 menit, checkin harus <= aktual mulai
                            if (checkIn.isSameOrBefore(aktualMulai)) {
                                match = true;
                            }
                        }
                    } else if (overtimeData.jenis_lembur === 'AKHIR') {
                        if (checkOut && aktualSelesai) {
                            // Toleransi 0 menit, checkout harus >= aktual selesai
                            if (checkOut.isSameOrAfter(aktualSelesai)) {
                                match = true;
                            }
                        }
                    }
                }

                if (match) {
                    matchStatus = '<span class="badge badge-success">MATCH</span>';
                } else if (aktualMulai || aktualSelesai) { // Hanya tampilkan UNMATCH jika ada data aktual
                    matchStatus = '<span class="badge badge-danger">UNMATCH</span>';
                }

                $('#match_status-aktual-' + i).html(matchStatus);

            },
            error: function (xhr) {
                // 404 = tidak ada data presensi
                $('#checkin-aktual-' + i).text('-');
                $('#checkout-aktual-' + i).text('-');
                $('#match_status-aktual-' + i).text('-');
            }
        });
    }


    var modalExportOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalExport = new bootstrap.Modal(
        document.getElementById("modal-export"),
        modalExportOptions
    );

    function openExport() {
        modalExport.show();
    }

    function closeExport() {
        modalExport.hide();
    }

    $('#btnExport').on("click", function () {
        openExport();
    });

    $('.btnCloseExport').on("click", function () {
        closeExport();
    });
});
//