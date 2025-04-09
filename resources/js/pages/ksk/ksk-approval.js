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

    var modalApprovalOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalApproval = new bootstrap.Modal(
        document.getElementById("modal-approval"),
        modalApprovalOptions
    );

    function openApproval() {
        modalApproval.show();
    }

    function closeApproval() {
        modalApproval.hide();
    }

    var modalDetailOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDetail = new bootstrap.Modal(
        document.getElementById("modal-detail"),
        modalDetailOptions
    );

    function openDetail() {
        modalDetail.show();
    }

    function closeDetail() {
        modalDetail.hide();
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

    function updateKskNotification(){
        $.ajax({
            url: base_url + '/ajax/ksk/get-ksk-notification',
            method: 'GET',
            success: function(response){
                $('.notification-release').html(response.html_release);
                $('.notification-approval').html(response.html_approval);
            }, error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    var columnsMustApprovedTable = [
        { data: "id_ksk" },
        { data: "nama_divisi" },
        { data: "nama_departemen" },
        { data: "parent_name" },
        { data: "release_date" },
        { data: "released_by" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "reviewed_div_by" },
        { data: "reviewed_ph_by" },
        { data: "reviewed_dir_by" },
        { data: "legalized_by" },
    ];

    var mustApprovedTable = $("#must-approved-table").DataTable({
        search: {
            return: true,
        },
        order: [[3, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/approval/datatable-must-approved",
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
        columns: columnsMustApprovedTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    var columnsHistoryTable = [
        { data: "id_ksk" },
        { data: "nama_divisi" },
        { data: "nama_departemen" },
        { data: "parent_name" },
        { data: "release_date" },
        { data: "released_by" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "reviewed_div_by" },
        { data: "reviewed_ph_by" },
        { data: "reviewed_dir_by" },
        { data: "legalized_by" },
    ];

    var historyTable = $("#history-table").DataTable({
        search: {
            return: true,
        },
        order: [[3, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/approval/datatable-history",
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
        columns: columnsHistoryTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    // //REFRESH TABLE
    function refreshTable() {
        var searchValueMustApproved = mustApprovedTable.search();
        if (searchValueMustApproved) {
            mustApprovedTable.search(searchValueMustApproved).draw();
        } else {
            mustApprovedTable.search("").draw();
        }

        var searchValueHistory = historyTable.search();
        if (searchValueHistory) {
            historyTable.search(searchValueHistory).draw();
        } else {
            historyTable.search("").draw();
        }
    }

    function onChangeStatusKsk() {
        $('select[name="status_ksk[]"]').on('change', function(){
            let statusKsk = $(this).val();
            let id = $(this).data('id');

            if (statusKsk == 'PHK') {
                $('#durasi_renewal'+id).val(0);
            }
        })
    }

    function onClickUpdate() {
        $('.btnUpdate').on('click', function(){
            loadingSwalShow();
            let id = $(this).data('id');
            let idKskDetail = $(this).data('id-ksk-detail');
            let statusKsk = $('#status_ksk'+id).val();
            let durasiRenewal = $('#durasi_renewal'+id).val();
            let alasan = $('#alasan'+id).val();

            let url = base_url + '/ksk/approval/update-detail-ksk/' + idKskDetail;
            let formData = new FormData();

            formData.append('_method', 'PATCH');
            formData.append('status_ksk', statusKsk);
            formData.append('durasi_renewal', durasiRenewal);
            formData.append('reason', alasan);

            $.ajax({
                url: url,
                data: formData,
                method: 'POST',
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function (data){
                    loadingSwalClose();
                    showToast({ title: data.message });
                },
                error: function (jqXHR, textStatus, errorThrown){
                    loadingSwalClose();
                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                }
            })
        })
    }

    function onSubmitApproval(idKSK) {
        $('#btnSubmitApprove').on('click', function(){
            loadingSwalShow();
            let action = $(this).data('action');
            let formData = new FormData();
            let url = base_url + '/ksk/approval/'+action+'/' + idKSK;

            $('input[name="id_ksk_detail[]"]').each(function() {
                formData.append('id_ksk_detail[]', $(this).val());
            });

            $('select[name="status_ksk[]"]').each(function() {
                formData.append('status_ksk[]', $(this).val());
            });

            $('input[name="durasi_renewal[]"]').each(function() {
                formData.append('durasi_renewal[]', $(this).val());
            });

            $('textarea[name="alasan[]"]').each(function() {
                formData.append('reason[]', $(this).val());
            });

            formData.append('_method', 'PATCH');

            $.ajax({
                url: url,
                data: formData,
                method: 'POST',
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function (data){
                    updateKskNotification();
                    loadingSwalClose();
                    showToast({ title: data.message });
                    closeApproval();
                    refreshTable();
                },
                error: function (jqXHR, textStatus, errorThrown){
                    loadingSwalClose();
                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                }
            })
        })
    }

    $('#must-approved-table').on('click', '.btnApproved', function(){
        loadingSwalShow();
        let idKsk = $(this).data('id-ksk');
        let url = base_url + '/ksk/ajax/approval/get-ksk/' + idKsk;
        onSubmitApproval(idKsk);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response){
                let html = response.html;
                $('#modal-approval-body').empty().html(html);
                $('.select2').select2({
                    dropdownParent: $('#modal-approval')
                });
                loadingSwalClose();
                openApproval();
                onClickUpdate();
                onChangeStatusKsk();
            },
            error: function(jqXHR, textStatus, errorThrown){
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    });

    $('#history-table').on('click', '.btnDetail', function(){
        loadingSwalShow();
        let idKsk = $(this).data('id-ksk');
        let url = base_url + '/ksk/ajax/approval/get-detail-ksk/' + idKsk;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response){
                let html = response.html;
                $('#modal-detail-body').empty().html(html);
                loadingSwalClose();
                openDetail();
            },
            error: function(jqXHR, textStatus, errorThrown){
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    });

    $('.btnCloseApproval').on('click', function(){
        closeApproval();
    });

    $('.btnCloseDetail').on('click', function(){
        closeDetail();
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
