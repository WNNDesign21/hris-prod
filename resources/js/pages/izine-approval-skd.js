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

    function updateNotification(){
        $.ajax({
            url: base_url + '/get-notification',
            method: 'GET',
            success: function(response){
                $('.notifications-menu').html(response.data);
            }
        })
    }

    // function updatePengajuanCutiNotification(){
    //     $.ajax({
    //         url: base_url + '/get-approval-skd-notification',
    //         method: 'GET',
    //         success: function(response){
    //             $('.notification-approval-skd').html(response.data);
    //         }
    //     })
    // }
    
    var columnsTable = [
        { data: "nama" },
        { data: "departemen" },
        { data: "posisi" },
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "lampiran" },
        { data: "approved_by" },
        { data: "legalized_by" }
    ];

    var ApprovalSkdTable = $("#approval-skd-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/approval-skd-datatable",
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
        initComplete: function () {
            $('.image-popup-vertical-fit').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                image: {
                    verticalFit: true
                }
            });
        },
        drawCallback: function () { 
            $('.image-popup-vertical-fit').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                image: {
                    verticalFit: true
                }
            });
        },
        // responsive: true,
        scrollX: true,
        columns: columnsTable,
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
        var searchValue = ApprovalSkdTable.search();
        if (searchValue) {
            ApprovalSkdTable.search(searchValue).draw();
        } else {
            ApprovalSkdTable.search("").draw();
        }
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
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
        document.getElementById("modal-reject-skd"),
        modalRejectOptions
    );

    function openReject() {
        modalReject.show();
    }

    function closeReject() {
        modalReject.hide();
        $('#rejected_note').val('');
    }

    $('#approval-skd-table').on('click', '.btnReject', function(){
        let idSakit = $(this).data('id-sakit');
        let url = base_url + '/izine/approval-skd/rejected/' + idSakit;
        $('#form-reject-skd').attr('action', url);
        openReject();
    });

    $('#form-reject-skd').on('submit', function (e) {
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

    $('#approval-skd-table').on('click', '.btnApproved', function(){
        let idSakit = $(this).data('id-sakit');
        let url = base_url + '/izine/approval-skd/approved/' + idSakit;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Approved SKD",
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

    $('#approval-skd-table').on('click', '.btnLegalized', function(){
        let idSakit = $(this).data('id-sakit');
        let url = base_url + '/izine/approval-skd/legalized/' + idSakit;
        var formData = new FormData();
        formData.append('_method', 'PATCH');
        Swal.fire({
            title: "Legalized SKD",
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
        ApprovalSkdTable.draw();
        closeFilter();
    });
});