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

    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "jenis" },
        { data: "durasi" },
        { data: "isUrgent" },
        { data: "isWorkday" },
        { data: "aksi" },
    ];

    var cutieTable = $("#setting-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/setting-cuti/datatable",
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
        cutieTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openForm();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

    // MODAL TAMBAH KARYAWAN
    var modalSettingCutiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalSettingCuti = new bootstrap.Modal(
        document.getElementById("modal-setting-cuti"),
        modalSettingCutiOptions
    );

    function openForm() {
        modalSettingCuti.show();
    }

    function closeForm() {
        modalSettingCuti.hide();
        resetForm();
    }

    function resetForm(){
        let url = base_url + '/cutie/setting-cuti/store';
        $('#id_jenis_cuti').val('');
        $('.modal-title').text('Tambah Jenis Cuti Khusus')
        $('#jenis').val('');
        $('#durasi').val('');
        $('#isUrgent').val('Y');
        $('#isWorkday').val('Y');
        $('#form-setting-cuti').attr('action', url);
        $('input[name="_method"]').val('POST');
    }

    $('#setting-table').on('click', '.btnEdit', function (){
        loadingSwalShow();
        var idJenisCuti = $(this).data('id');
        var url = base_url + '/cutie/ajax/get-data-detail-jenis-cuti/' + idJenisCuti;
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                var data = response.data;
                $('#id_jenis_cuti').val(data.id_cuti);
                $('#jenis').val(data.jenis);
                $('#durasi').val(data.durasi);
                $('#isUrgent').val(data.isUrgent);
                $('#isWorkday').val(data.isWorkday);
                $('.modal-title').text('Edit Jenis Cuti Khusus')

                $('#form-setting-cuti').attr('action', base_url + '/cutie/setting-cuti/update/' + idJenisCuti);
                $('#form-setting-cuti').append('<input type="hidden" name="_method" value="PATCH">');
                loadingSwalClose();
                openForm();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    $('#setting-table').on('click', '.btnDelete', function (){
        var idJenisCuti = $(this).data('id');
        Swal.fire({
            title: "Delete Jenis Cuti Khusus",
            text: "Apakah kamu yakin untuk menghapus Pengajuan Jenis Cuti Khusus ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/cutie/setting-cuti/delete/' + idJenisCuti;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        refreshTable();
                        showToast({ title: data.message });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

    $('#form-setting-cuti').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-setting-cuti').attr('action');

        var formData = new FormData($('#form-setting-cuti')[0]);
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
                closeForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });
});
