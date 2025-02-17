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
    
    var columnsTable = [
        { data: "karyawan" },
        { data: "departemen" },
        { data: "expired_date" },
        { data: "aksi" },
    ];

    var piketTable = $("#piket-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/piket-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let departemen = $('#filterDepartemen').val();
                let periode = $('#filterPeriode').val();

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
        var searchValue = piketTable.search();
        if (searchValue) {
            piketTable.search(searchValue).draw();
        } else {
            piketTable.search("").draw();
        }
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openPiket();
    })

    $('.btnClose').on("click", function (){
        closePiket();
    })

    var modalPiketOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalPiket = new bootstrap.Modal(
        document.getElementById("modal-piket"),
        modalPiketOptions
    );
    
    function openPiket() {
        modalPiket.show();
    }

    function closePiket() {
        modalPiket.hide();
    }

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

    $('#karyawan_id').select2({
        dropdownParent: $('#modal-piket'),
    });

    $('.btnResetFilter').on('click', function(){
        $('#filterPeriode').val('');
    })

    $(".btnSubmitFilter").on("click", function () {
        piketTable.draw();
        closeFilter();
    });

    $('#form-piket').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-piket').attr('action');
        var formData = new FormData($('#form-piket')[0]);
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
                closePiket();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });

    $('#form-piket-edit').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let idPiket = $('#id_piketEdit').val();
        let url = base_url + '/izine/piket/update/' + idPiket;
        let formData = new FormData($('#form-piket-edit')[0]);
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
                closeEdit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });

    $('#piket-table').on("click", '.btnEdit', function (){
        let karyawanId = $(this).data('id-karyawan');
        let expiredDate = $(this).data('expired-date');
        let idPiket = $(this).data('id-piket');

        $('#karyawan_idEdit').val(karyawanId).trigger('change');
        $('#expired_dateEdit').val(expiredDate);
        $('#id_piketEdit').val(idPiket);
        openEdit();
    })

    $('#karyawan_idEdit').select2({
        dropdownParent: $('#modal-piket-edit'),
    });

    $('.btnCloseEdit').on("click", function (){
        closeEdit();
    })

    var modalEditPiketOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditPiket = new bootstrap.Modal(
        document.getElementById("modal-piket-edit"),
        modalEditPiketOptions
    );
    
    function openEdit() {
        modalEditPiket.show();
    }

    function closeEdit() {
        $('#karyawan_idEdit').val('').trigger('change');
        $('#expired_dateEdit').val('');
        $('#id_piketEdit').val('');
        modalEditPiket.hide();
    }
});