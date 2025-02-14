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

                dataFilter.departemen = departemen;
                dataFilter.organisasi = organisasi;
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
        responsive: true,
        columns: columnsTable,
        scrollX: true,
        columnDefs: [
            
        ],
    })

    // SELECT CHECKBOX
    $('#select-all').on('click', function() {
        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        selectedRow = [];
        if (this.checked) {
            for (let i = 1; i <= totalRow; i++) {
                selectedRow.push('row_' + i);
            }
        }
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
    });

    $('#review-table').on('draw.dt', function () {
        var rows = reviewTable.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).each(function() {
            var value = $(this).val();
            if (selectedRow.includes(value)) {
                $(this).prop('checked', true);
            }
        });
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

    $('#review-table').on('click', '.btnDetail', function() {
        loadingSwalShow();
        let departemenId = $(this).data('departemen-id');
        let divisiId = $(this).data('divisi-id');
        let organisasiId = $(this).data('organisasi-id');
        let tanggalLembur = $(this).data('tanggal-lembur');
        let status = $(this).data('status');
        let url = base_url + '/lembure/review-lembur/get-review-lembur-detail'
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                departemen_id: departemenId,
                divisi_id: divisiId,
                organisasi_id: organisasiId,
                tanggal_lembur: tanggalLembur,
                status: status
            },
            success: function(response){
                console.log(response);
                loadingSwalClose();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    });
});