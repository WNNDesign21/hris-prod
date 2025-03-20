$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    let departemenId;
    let divisiId;
    let organisasiId;
    let tanggalLembur;
    let status;

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
            url: base_url + '/get-review-lembur-notification',
            method: 'GET',
            success: function(response){
                $('.notification-review-lembur').html(response.data);
            }
        })
    }

    // DATATABLE
    let totalRow = 0;
    let selectedRow = [];
    var columnsTable = [
        {
            data: "checkbox",
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return '<input type="checkbox" class="row-checkbox" style="opacity: 1!important; position:relative!important; left:0px!important;" value="' + data + '">';
            },
            className: "text-center",
        },
        { data: "tanggal_lembur" },
        { data: "departemen" },
        { data: "status" },
        { data: "total_durasi_lembur" },
        { data: "total_nominal_lembur" },
        { data: "total_karyawan" },
        { data: "total_dokumen" },
        { data: "aksi"},
    ];

    var reviewTable = $("#review-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/review-lembur-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var organisasi = $('#filterOrganisasi').val();
                var departemen = $('#filterDepartemen').val();
                var status = $('#filterStatus').val();
                var periode = $('#filterPeriode').val();

                dataFilter.departemen = departemen;
                dataFilter.organisasi = organisasi;
                dataFilter.status = status;
                dataFilter.periode = periode;
            },
            dataSrc: function(response) {
                totalRow = response.recordsFiltered;
                return response.data;
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
        columns: columnsTable,
        scrollX: true,
        columnDefs: [

        ],
    })

    // SELECT CHECKBOX LOGIC
    $('#unchecked-all').on('click', function() {
        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', false).trigger('change');

        selectedRow = [];
        $('#select-all').prop('checked', false).prop('indeterminate', false);
    });

    $('#select-all').on('click', function() {
        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        var anyChecked = $('input[type="checkbox"]:checked', rows).length > 0;
        if (anyChecked) {
            $('input[type="checkbox"]:checked', rows).trigger('change');
        }
        $('input[type="checkbox"]', rows).prop('checked', this.checked).trigger('change');
    });

    $('#review-table tbody').on('change', 'input.row-checkbox', function() {
        if (!this.checked) {
            var el = $('#select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
            el.indeterminate = true;
            }
            var value = $(this).val();
            selectedRow = selectedRow.filter(function(item) {
            return item !== value;
            });
        } else {
            selectedRow.push($(this).val());
        }

        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        var allUnchecked = $('input[type="checkbox"]:checked', rows).length === 0;
        if (allUnchecked) {
            $('#select-all').prop('checked', false).prop('indeterminate', false);
        }
    });

    $('#review-table').on('draw.dt', function () {
        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        var allChecked = true;
        $('input[type="checkbox"]', rows).each(function() {
            var value = $(this).val();
            if (selectedRow.includes(value)) {
                $(this).prop('checked', true);
            } else {
                allChecked = false;
            }
        });
        $('#select-all').prop('checked', allChecked);
    });

    function refreshTable() {
        reviewTable.search("").draw();
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    // DETAIL
    $('.btnClose').on("click", function (){
        closeDetail();
        $('#detail-review-table').DataTable().destroy();
    })

    var modalDetailReviewLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDetailReviewLembur = new bootstrap.Modal(
        document.getElementById("modal-detail-review-lembur"),
        modalDetailReviewLemburOptions
    );

    function openDetail() {
        modalDetailReviewLembur.show();
    }

    function closeDetail() {
        modalDetailReviewLembur.hide();
    }

    var modalRejectOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalReject = new bootstrap.Modal(
        document.getElementById("modal-reject"),
        modalRejectOptions
    );

    function openReject() {
        modalReject.show();
    }

    function closeReject() {
        modalReject.hide();
    }

    $('#review-table').on('click', '.btnDetail', function() {
        loadingSwalShow();
        let departemenId = $(this).data('departemen-id');
        let divisiId = $(this).data('divisi-id');
        let organisasiId = $(this).data('organisasi-id');
        let tanggalLembur = $(this).data('tanggal-lembur');
        let status = $(this).data('status');
        let departemen = $(this).data('departemen');
        let organisasi = $(this).data('organisasi');
        let url = base_url + '/lembure/review-lembur/get-review-lembur-detail'
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen_id: departemenId,
                divisi_id: divisiId,
                organisasi_id: organisasiId,
                tanggal_lembur: tanggalLembur,
                status: status
            },
            success: function(response){
                let data = response.data;
                let tbody = $('#detailReviewContent').empty();
                $('#detailReviewTanggal').text(tanggalLembur);
                $('#detailReviewDepartemen').text(departemen + ' (' + organisasi + ')');
                $('#detailReviewStatus').empty().html(status == 'PLANNING' ? '<span class="badge badge-info">PLANNING</span>' : '<span class="badge badge-success">ACTUAL</span>');

                if (data.length > 0) {
                    $.each(data, function (i, val){
                        tbody.append(`
                            <tr>
                                <td>
                                    ${status == 'PLANNING' ? `<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id-detail-lembur="${val.id_detail_lembur}" data-departemen-id="${departemenId}" data-divisi-id="${divisiId}" data-organisasi-id="${organisasiId}" data-tanggal-lembur="${tanggalLembur}" data-status="${status}">
                                        <i class="far fa-times-circle"></i> Cancel
                                    </button>` : '-'}
                                </td>
                                <td>${val.lembur_id}</td>
                                <td>${val.karyawan}</td>
                                <td>${val.deskripsi_pekerjaan}</td>
                                <td>${moment(val.tanggal_mulai).format('D MMM YYYY HH:mm')}</td>
                                <td>${moment(val.tanggal_selesai).format('D MMM YYYY HH:mm')}</td>
                                <td>${val.keterangan ?? ''}</td>
                                <td>${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(val.nominal)}</td>
                                <td>${status == 'PLANNING' ? '✅<br><small class="text-bold">' + val.plan_checked_by + '</small><br><small class="text-fade">' + moment(val.plan_checked_at).format('D MMM YYYY HH:mm') + '</small>' : '✅<br><small class="text-bold">' + val.actual_checked_by + '</small><br><small class="text-fade">' + moment(val.actual_checked_at).format('D MMM YYYY HH:mm') + '</small>'}</td>
                                <td>${status == 'PLANNING' ? '✅<br><small class="text-bold">' + val.plan_approved_by + '</small><br><small class="text-fade">' + moment(val.plan_approved_at).format('D MMM YYYY HH:mm') + '</small>' : '✅<br><small class="text-bold">' + val.actual_approved_by + '</small><br><small class="text-fade">' + moment(val.actual_approved_at).format('D MMM YYYY HH:mm') + '</small>'}</td>
                            </tr>
                        `);
                    });
                }
                $('#detail-review-table').DataTable({
                    order: [[0, 'asc']]
                });
                openDetail();
                loadingSwalClose();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    });

    $('#detail-review-table').on("click", '.btnReject', function () {
        departemenId = $(this).data('departemen-id');
        divisiId = $(this).data('divisi-id');
        organisasiId = $(this).data('organisasi-id');
        tanggalLembur = $(this).data('tanggal-lembur');
        status = $(this).data('status');
        let idDetailLembur = $(this).data('id-detail-lembur');
        let url = base_url + '/lembure/review-lembur/rejected/' + idDetailLembur;
        $('#form-reject').attr('action', url);
        openReject();
    })

    $('#form-reject').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-reject').attr('action');
        var formData = new FormData($('#form-reject')[0]);

        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                $('#rejected_note').val('');
                showToast({ title: data.message });
                refreshTable();
                closeReject();
                loadingSwalClose();

                $('#detail-review-table').DataTable().destroy();
                let url = base_url + '/lembure/review-lembur/get-review-lembur-detail'
                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        departemen_id: departemenId,
                        divisi_id: divisiId,
                        organisasi_id: organisasiId,
                        tanggal_lembur: tanggalLembur,
                        status: status
                    },
                    success: function(response){
                        let data = response.data;
                        let tbody = $('#detailReviewContent').empty();
                        if (data.length > 0) {
                            $.each(data, function (i, val){
                                tbody.append(`
                                    <tr>
                                        <td>
                                            ${status == 'PLANNING' ? `<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id-detail-lembur="${val.id_detail_lembur}" data-departemen-id="${departemenId}" data-divisi-id="${divisiId}" data-organisasi-id="${organisasiId}" data-tanggal-lembur="${tanggalLembur}" data-status="${status}">
                                                <i class="far fa-times-circle"></i> Cancel
                                            </button>` : '-'}
                                        </td>
                                        <td>${val.lembur_id}</td>
                                        <td>${val.karyawan}</td>
                                        <td>${val.deskripsi_pekerjaan}</td>
                                        <td>${moment(val.tanggal_mulai).format('D MMM YYYY HH:mm')}</td>
                                        <td>${moment(val.tanggal_selesai).format('D MMM YYYY HH:mm')}</td>
                                        <td>${val.keterangan ?? ''}</td>
                                        <td>${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(val.nominal)}</td>
                                        <td>${status == 'PLANNING' ? '✅<br><small class="text-bold">' + val.plan_checked_by + '</small><br><small class="text-fade">' + moment(val.plan_checked_at).format('D MMM YYYY HH:mm') + '</small>' : '✅<br><small class="text-bold">' + val.actual_checked_by + '</small><br><small class="text-fade">' + moment(val.actual_checked_at).format('D MMM YYYY HH:mm') + '</small>'}</td>
                                        <td>${status == 'PLANNING' ? '✅<br><small class="text-bold">' + val.plan_approved_by + '</small><br><small class="text-fade">' + moment(val.plan_approved_at).format('D MMM YYYY HH:mm') + '</small>' : '✅<br><small class="text-bold">' + val.actual_approved_by + '</small><br><small class="text-fade">' + moment(val.actual_approved_at).format('D MMM YYYY HH:mm') + '</small>'}</td>
                                    </tr>
                                `);
                            });
                            $('#detail-review-table').DataTable({
                                order: [[0, 'asc']]
                            });
                        } else {
                            closeDetail();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    }
                })
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('.btnRejectClose').on('click', function (){
        closeReject();
    });

    $('#accept').on('click', function (){
        let countRow = selectedRow.length;
        selectedRow = [...new Set(selectedRow)];

        if (countRow == 0) {
            showToast({ icon: "error", title: "Silahkan pilih data yang akan di review!" });
            return;
        }

        let formData = new FormData();
        formData.append('data', selectedRow);
        formData.append('_method', 'PATCH');

        Swal.fire({
            title: "Accept Lembur",
            text: "Dengan ini anda menyetujui " + countRow + " data lembur yang dipilih",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Accept it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/lembure/review-lembur/reviewed';
                $.ajax({
                    url: url,
                    data: formData,
                    method:"POST",
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (response) {
                        updateLemburNotification();
                        loadingSwalClose();
                        refreshTable();
                        showToast({ title: response.message });
                        selectedRow = [];
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

    // FILTER
    $('.btnFilter').on("click", function (){
        openFilter();
    });

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    });

    $('.btnResetFilter').on("click", function (){
        $('#filterPeriode').val('');
        $('#filterOrganisasi').val([]).trigger('change');
        $('#filterDepartemen').val([]).trigger('change');
        $('#filterStatus').val('PLANNING').trigger('change');
    });

    $(".btnSubmitFilter").on("click", function () {
        reviewTable.draw();
        closeFilter();
    });

    var modalFilterReviewLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilterReviewLembur = new bootstrap.Modal(
        document.getElementById("modal-filter-review-lembur"),
        modalFilterReviewLemburOptions
    );

    function openFilter() {
        modalFilterReviewLembur.show();
    }

    function closeFilter() {
        modalFilterReviewLembur.hide();
    }

    $('#filterOrganisasi').select2({
        dropdownParent: $('#modal-filter-review-lembur'),
        placeholder: 'Pilih Organisasi',
    });

    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter-review-lembur'),
        placeholder: 'Pilih Departemen',
    });

    $('#filterStatus').select2({
        dropdownParent: $('#modal-filter-review-lembur')
    });
});
