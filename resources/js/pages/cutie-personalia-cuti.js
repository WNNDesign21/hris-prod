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
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "nama" },
        { data: "departemen" },
        { data: "rencana_mulai_cuti" },
        { data: "rencana_selesai_cuti" },
        { data: "aktual_mulai_cuti" },
        { data: "aktual_selesai_cuti" },
        { data: "durasi" },
        { data: "jenis" },
        { data: "checked_1" },
        { data: "checked_2" },
        { data: "approved" },
        { data: "legalized" },
        { data: "status_dokumen" },
        { data: "status" },
        { data: "alasan" },
        { data: "karyawan_pengganti" },
        { data: "created_at" },
        { data: "attachment" },
        { data: "aksi" },
    ];

    var cutieTable = $("#personalia-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/personalia-cuti-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var departemen = $('#filterDepartemen').val();
                var jenisCuti = $('#filterJenisCuti').val();
                var nama = $('#filterNama').val();
                var durasi = $('#filterDurasi').val();
                var rencanaMulai = $('#filterRencanaMulai').val();
                var statusCuti = $('#filterStatusCuti').val();
                var statusDokumen = $('#filterStatusDokumen').val();

                dataFilter.departemen = departemen;
                dataFilter.jenisCuti = jenisCuti;
                dataFilter.durasi = durasi;
                dataFilter.rencanaMulai = rencanaMulai;
                dataFilter.nama = nama;
                dataFilter.statusCuti = statusCuti;
                dataFilter.statusDokumen = statusDokumen;
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
                targets: [-2,-1],
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

    // MODAL REJECT
    var modalRejectOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalReject = new bootstrap.Modal(
        document.getElementById("modal-reject-cuti"),
        modalRejectOptions
    );

    function openReject() {
        modalReject.show();
    }

    function closeReject() {
        modalReject.hide();
        resetReject();
    }

    function resetReject(){
        $('#alasan_reject').val('');
        $('#id_cuti').val();
        $('#nama_atasan').val();
        $('#form-reject-cuti').attr('action', '#');
    }

    $('.btnClose').on('click', function (){
        closeReject();
    })

    $('#personalia-table').on("click",".btnReject", function (){
        let idCuti = $(this).data('id');
        let namaAtasan = $(this).data('nama-atasan');
        let url = base_url + '/cutie/personalia-cuti/reject/' + idCuti;
        $('#nama_atasan').val(namaAtasan);
        $('#id_cuti').val(idCuti);
        $('#form-reject-cuti').attr('action', url);
        openReject();
    })

    $('#personalia-table').on("click", ".btnAlasan", function () {
        let alasan = $(this).data('alasan');
        const toast = Swal.mixin({
            toast: true,
            position: "center",
            showCloseButton: true,
            showConfirmButton: false,
            didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
            }
        });

        toast.fire({
            icon: "info",
            title: alasan
        });
    });

    $('#personalia-table').on('click', '.btnDelete', function (){
        var idCuti = $(this).data('id');
        Swal.fire({
            title: "Delete Cuti",
            text: "Apakah kamu yakin untuk menghapus Pengajuan Cuti ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/cutie/personalia-cuti/delete/' + idCuti;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        updateNotification();
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

    $('#form-reject-cuti').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-reject-cuti').attr('action');

        var formData = new FormData($('#form-reject-cuti')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                updateNotification();
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

    $('#personalia-table').on('click', '.btnUpdateDokumen', function (){
        Swal.fire({
            title: "Update Dokumen",
            text: "Apakah kamu yakin untuk update dokumen ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, saya yakin!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                let idCuti = $(this).data('id');
                let issuedName = $(this).data('issued-name');
                let type = $(this).data('type');
                let url = base_url + '/cutie/member-cuti/update-dokumen-cuti/' + idCuti;
        
                var formData = new FormData();
                formData.append('issued_name', issuedName);
                formData.append('type', type);
                formData.append('_method', 'PATCH');
                $.ajax({
                    url: url, 
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        updateNotification();
                        loadingSwalClose()
                        showToast({ title: data.message });
                        refreshTable();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    }
                });
            }
        })
    })

    //FILTER CUTI
    $('.btnFilter').on("click", function (){
        openFilter();
    })

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    })

    $('.btnResetFilter').on("click", function (){
        $('#filterDepartemen').val("").trigger('change');   
        $('#filterJenisCuti').val("").trigger('change');   
        $('#filterStatusCuti').val("").trigger('change');   
        $('#filterStatusDokumen').val("").trigger('change');   
        $('#filterNama').val("");
        $('#filterDurasi').val("");
        $('#filterRencanaMulai').val("");
    })


    // MODAL FILTER CUTI
    var modalFilterCutiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilterCuti = new bootstrap.Modal(
        document.getElementById("modal-filter"),
        modalFilterCutiOptions
    );

    function openFilter() {
        modalFilterCuti.show();
        $('#filterDepartemen').select2({
            dropdownParent: $('#modal-filter'),
        });
        $('#filterJenisCuti').select2({
            dropdownParent: $('#modal-filter'),
        });
        $('#filterStatusCuti').select2({
            dropdownParent: $('#modal-filter'),
        });
        $('#filterStatusDokumen').select2({
            dropdownParent: $('#modal-filter'),
        });
    }

    function closeFilter() {
        modalFilterCuti.hide();
    }

    $(".btnSubmitFilter").on("click", function () {
        cutieTable.draw();
        closeFilter();
    });

    $('#personalia-table').on('click', '.btnCancel', function (){
        Swal.fire({
            title: "Cancel Cuti",
            text: "Jatah cuti akan dikembalikan dan pengajuan cuti harus dimulai dari awal?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Cancel Cuti!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                let idCuti = $(this).data('id');
                let url = base_url + '/cutie/pengajuan-cuti/cancel/' + idCuti;
        
                var formData = new FormData();
                formData.append('_method', 'PATCH');
                $.ajax({
                    url: url, 
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#sisa_cuti_total_display').text(data.data.sisa_cuti_tahunan+' Hari');
                        $('#sisa_cuti_pribadi').text(data.data.sisa_cuti_pribadi+' Hari');
                        $('#sisa_cuti_tahun_lalu').text(data.data.sisa_cuti_tahun_lalu+' Hari');
                        updateNotification();
                        loadingSwalClose()
                        showToast({ title: data.message });
                        refreshTable();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    }
                });
            }
        })
    })
})