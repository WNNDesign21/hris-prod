$(function () {
    // GLOBAL VARIABLES
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    let loadingSwal;
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
        { data: "karyawan" },
        { data: "tanggal" },
        { data: "kendaraan" },
        { data: "pergi" },
        { data: "kembali" },
        { data: "rute" },
        { data: "jarak_tempuh" },
        { data: "pengikut" },
        { data: "keterangan" },
        { data: "status" },
        { data: "checked" },
        { data: "legalized" },
        { data: "known" },
    ];

    var approvalTable = $("#approval-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/tugasluare/approval/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let nopolFilter = $('#nopolFilter').val();
                let statusFilter = $('#statusFilter').val();
                let fromFilter = $('#fromFilter').val();
                let toFilter = $('#toFilter').val();
                // let departemenFilter = '';

                dataFilter.nopol = nopolFilter;
                dataFilter.status = statusFilter;
                dataFilter.from = fromFilter;
                dataFilter.to = toFilter;
                // dataFilter.departemen = departemenFilter;
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
                targets: [8],
            },
        ],
    })
    // END DATATABLE
    
    // MODAL
    var modalFilterOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilter = new bootstrap.Modal(
        document.getElementById("modal-filter"),
        modalFilterOptions
    );

    var modalRejectOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalReject = new bootstrap.Modal(
        document.getElementById("modal-reject"),
        modalRejectOptions
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
        var searchValue = approvalTable.search();
        if (searchValue) {
            approvalTable.search(searchValue).draw();
        } else {
            approvalTable.search("").draw();
        }
    }

    function openFilter() {
        modalFilter.show();
    }

    function closeFilter() {
        modalFilter.hide();
    }

    function openReject() {
        modalReject.show();
    }

    function closeReject() {
        modalReject.hide();
    }

    function openVerification() {
        modalVerification.show();
    }

    function closeVerification() {
        modalVerification.hide();
    }
    // END FUNCTION


    // EVENT
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('#approval-table').on('click', '.btnReject', function(){
        let idTugasLuar = $(this).data('id-tugasluar');
        let url = base_url + '/tugasluare/approval/rejected/' + idTugasLuar;
        $('#form-reject').attr('action', url);
        openReject();
    })

    $('.btnCloseReject').on("click", function (){
        closeReject();
    });

    $('#form-reject').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let url = $(this).attr('action');
        var formData = new FormData($(this)[0]);
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
    })

    $('#approval-table').on('click', '.btnChecked', function(){
        let idTugasLuar = $(this).data('id-tugasluar');
        let url = base_url + '/tugasluare/approval/checked/' + idTugasLuar;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Checked Tugas Luar",
            text: "Data yang sudah di Checked tidak bisa diubah!",
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
    })

    $('#approval-table').on('click', '.btnLegalized', function(){
        let idTugasLuar = $(this).data('id-tugasluar');
        let url = base_url + '/tugasluare/approval/legalized/' + idTugasLuar;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Legalized Tugas Luar",
            text: "Data yang sudah di legalized tidak bisa diubah!",
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
    })

    $('#approval-table').on('click', '.btnKnown', function(){
        let idTugasLuar = $(this).data('id-tugasluar');
        let status = $(this).data('status');
        let kodeWilayah = $(this).data('kode-wilayah');
        let noPolisi = $(this).data('nomor-polisi');
        let seriAkhir = $(this).data('seri-akhir');

        if (status == 'PERGI') {
            $('#kode_wilayahVerif').prop('disabled', false);
            $('#nomor_polisiVerif').prop('disabled', false);
            $('#seri_akhirVerif').prop('disabled', false);
            $('#status').text('Kilomeneter Pergi');
        } else {
            $('#kode_wilayahVerif').prop('disabled', true);
            $('#nomor_polisiVerif').prop('disabled', true);
            $('#seri_akhirVerif').prop('disabled', true);
            $('#status').text('Kilomeneter Kembali');
        }

        $('#kode_wilayahVerif').val(kodeWilayah);
        $('#nomor_polisiVerif').val(noPolisi);
        $('#seri_akhirVerif').val(seriAkhir);
        let url = base_url + '/tugasluare/approval/known/' + idTugasLuar;
        $('#form-verification').attr('action', url);
        openVerification();
    })

    $('#form-verification').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let url = $(this).attr('action');
        var formData = new FormData($(this)[0]);
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
                closeVerification();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    })

    $('.btnFilter').on("click", function (){
        openFilter();
    })

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    })

    $('.btnCloseVerification').on("click", function (){
        $('#status').text('');
        closeVerification();
    })

    $('.btnSubmitFilter').on("click", function () {
        let fromFilter = $('#fromFilter').val();
        let toFilter = $('#toFilter').val();

        if ((fromFilter && !toFilter) || (!fromFilter && toFilter)) {
            showToast({
                icon: 'error',
                title: 'Harap isi kedua tanggal mulai dan tanggal akhir'
            });
            return;
        }

        if (toFilter && fromFilter && new Date(toFilter) < new Date(fromFilter)) {
            showToast({
                icon: 'error',
                title: 'Tanggal akhir tidak boleh kurang dari tanggal mulai'
            });
            return;
        } else {
            closeFilter();
            refreshTable();
        }
    })

    $('.btnResetFilter').on("click", function (){
        $('#nopolFilter').val('');
        $('#statusFilter').val('').trigger('change');
        $('#fromFilter').val('');
        $('#toFilter').val('');
    })

    $('#statusFilter').select2({
        dropdownParent: $('#modal-filter'),
    });
    // END EVENT
});