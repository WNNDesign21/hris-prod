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

    function updateApprovalIzinNotification(){
        $.ajax({
            url: base_url + '/get-approval-izin-notification',
            method: 'GET',
            success: function(response){
                $('.notification-approval-izin').html(response.data);
            }
        })
    }

    var mustApprovedColumnsTable = [
        { data: "id_izin" },
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "rencana_mulai_or_masuk" },
        { data: "rencana_selesai_or_keluar" },
        { data: "aktual_mulai_or_masuk" },
        { data: "aktual_selesai_or_keluar" },
        { data: "jenis_izin" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "legalized_by" }
    ];

    var mustApprovedTable = $("#must-approved-table").DataTable({
        search: {
            return: true,
        },
        order: [[4, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/must-approved-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let urutan = $('#filterUrutan').val();
                let departemen = $('#filterDepartemen').val();
                let status = $('#filterStatus').val();

                dataFilter.urutan = urutan;
                dataFilter.departemen = departemen;
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
                    })
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
                    })
                }
            },
        },
        scrollX: true,
        columns: mustApprovedColumnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    var alldataColumnsTable = [
        { data: "id_izin" },
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "rencana_mulai_or_masuk" },
        { data: "rencana_selesai_or_keluar" },
        { data: "aktual_mulai_or_masuk" },
        { data: "aktual_selesai_or_keluar" },
        { data: "jenis_izin" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "legalized_by" }
    ];

    var alldataTable = $("#alldata-table").DataTable({
        search: {
            return: true,
        },
        order: [[4, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/alldata-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let urutan = $('#filterUrutan').val();
                let departemen = $('#filterDepartemen').val();
                let status = $('#filterStatus').val();

                dataFilter.urutan = urutan;
                dataFilter.departemen = departemen;
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
                    })
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
                    })
                }
            },
        },
        // responsive: true,
        scrollX: true,
        columns: alldataColumnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
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
        var mustApprovedSearchValue = mustApprovedTable.search();
        var alldataSearchValue = alldataTable.search();

        if (mustApprovedSearchValue) {
            mustApprovedTable.search(mustApprovedSearchValue).draw();
        } else {
            mustApprovedTable.search("").draw();
        }

        if (alldataSearchValue) {
            alldataTable.search(alldataSearchValue).draw();
        } else {
            alldataTable.search("").draw();
        }
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openInputForm();
    })

    $('.btnClose').on("click", function (){
        closeInputForm();
    })

     // MODAL REJECT
     var modalRejectOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalReject = new bootstrap.Modal(
        document.getElementById("modal-reject-izin"),
        modalRejectOptions
    );

    function openReject() {
        modalReject.show();
    }

    function closeReject() {
        modalReject.hide();
        $('#rejected_note').val('');
        $('#is_shift_malam').val('');
    }

    $('#must-approved-table').on('click', '.btnReject', function(){
        let idIzin = $(this).data('id-izin');
        let url = base_url + '/izine/approval-izin/rejected/' + idIzin;
        let isShiftMalam = $(this).data('is-shift-malam');
        $('#is_shift_malam').val(isShiftMalam);
        $('#form-reject-izin').attr('action', url);
        openReject();
    });

    $('#form-reject-izin').on('submit', function (e) {
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
                updateApprovalIzinNotification();
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

    $('#must-approved-table').on('click', '.btnChecked', function(){
        let idIzin = $(this).data('id-izin');
        let url = base_url + '/izine/approval-izin/checked/' + idIzin;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Checked Izin",
            text: "Data yang sudah di checked tidak bisa diubah!",
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
                        updateApprovalIzinNotification();
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
    });

    $('#must-approved-table').on('click', '.btnApproved', function(){
        let idIzin = $(this).data('id-izin');
        let url = base_url + '/izine/approval-izin/approved/' + idIzin;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Approved Izin",
            text: "Data yang sudah di approved tidak bisa diubah!",
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
                    data : formData,
                    method:"POST",
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function (data) {
                        updateApprovalIzinNotification();
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

    $('#must-approved-table').on('click', '.btnLegalized', function(){
        let idIzin = $(this).data('id-izin');
        let isShiftMalam = $(this).data('is-shift-malam');
        let url = base_url + '/izine/approval-izin/legalized/' + idIzin;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('is_shift_malam', isShiftMalam);
        Swal.fire({
            title: "Legalized Izin",
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
                        updateApprovalIzinNotification();
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

    // FILTER
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
        $('#filterDepartemen').val('');
        $('#filterStatus').val('');
    })

    $('#filterUrutan').select2({
        dropdownParent: $('#modal-filter')
    });

    $('#filterStatus').select2({
        dropdownParent: $('#modal-filter')
    });
    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter')
    });

    $(".btnSubmitFilter").on("click", function () {
        mustApprovedTable.draw();
        alldataTable.draw();
        closeFilter();
    });

    $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        var target = $(e.target).attr("href");
        if ($(target).find("table").hasClass("dataTable")) {
            if (!$.fn.DataTable.isDataTable($(target).find("table"))) {
                $(target).find("table").DataTable();
            } else {
                $(target).find("table").DataTable().columns.adjust().draw();
            }
        }
    });
});
