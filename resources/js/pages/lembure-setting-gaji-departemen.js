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

    // DATATABLE
    var columnsTable = [
        { data: "departemen" },
        { data: "periode" },
        { data: "nominal_batas_lembur" },
        { data: "presentase" },
        { data: "total_gaji" },
    ];

    var settingGajiDepartemenTable = $("#setting-gaji-departemen-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/setting-gaji-departemen-datatable",
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
        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-2],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center");
                },
            },
        ],
    })

    function refreshTable() {
        var searchValue = settingGajiDepartemenTable.search();
        if (searchValue) {
            settingGajiDepartemenTable.search(searchValue).draw();
        } else {
            settingGajiDepartemenTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('#setting-gaji-departemen-table').on('input', '.inputGajiDepartemen', function () {
        let inputValue = $(this).val();

        if (!/^\d+$/.test(inputValue)) {
            $(this).val('');
        } else if (inputValue.length > 1 && inputValue.startsWith('0')) {
            $(this).val(inputValue.replace(/^0+/, ''));
        }
    })

    $('#setting-gaji-departemen-table').on('click', '.updateGajiDepartemen', function (){
        loadingSwalShow();
        let idGajiDepartemen = $(this).data('id-gaji-departemen');
        let departemenId = $(this).data('departemen-id');
        let urutan = $(this).data('urutan');
        let presentase = $('#presentase_'+urutan).val();
        let totalGaji = $('#total_gaji_'+urutan).val();
        let formData = new FormData();
        formData.append('id_gaji_departemen', idGajiDepartemen);
        formData.append('total_gaji', totalGaji);
        formData.append('departemen_id', departemenId);
        formData.append('presentase', presentase);
        formData.append('_method', 'PATCH');

        let url = base_url + '/lembure/setting-gaji-departemen/update';
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
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    // TAMBAH GAJI DEPARTEMEN
    $('.btnAdd').on("click", function (){
        openForm();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

    var modalTambahGajiDepartemenOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalTambahGajiDepartemen = new bootstrap.Modal(
        document.getElementById("modal-tambah-gaji-departemen"),
        modalTambahGajiDepartemenOptions
    );
    
    function openForm() {
        modalTambahGajiDepartemen.show();
    }

    function closeForm() {
        modalTambahGajiDepartemen.hide();
    }

    $('#form-tambah-gaji-departemen').on('submit', function (e) {
        e.preventDefault();
        loadingSwalShow();
        let formData = new FormData($(this)[0]);
        let url = $(this).attr('action');
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                loadingSwalClose();
                closeForm();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });
});