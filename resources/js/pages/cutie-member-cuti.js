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

    function updateMemberCutiNotification(){
        $.ajax({
            url: base_url + '/get-member-cuti-notification',
            method: 'GET',
            success: function(response){
                $('.notification-member-cuti').html(response.data);
            }
        })
    }

    //DATATABLE MEMBER
    var columnsTable = [
        { data: "nama" },
        { data: "departemen" },
        { data: "rencana_mulai_cuti" },
        { data: "rencana_selesai_cuti" },
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
    ];

    var cutieTable = $("#member-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/member-cuti-datatable",
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

    $('#member-table').on("click",".btnReject", function (){
        let idCuti = $(this).data('id');
        let namaAtasan = $(this).data('nama-atasan');
        let url = base_url + '/cutie/member-cuti/reject/' + idCuti;
        $('#nama_atasan').val(namaAtasan);
        $('#id_cuti').val(idCuti);
        $('#form-reject-cuti').attr('action', url);
        openReject();
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
                updateMemberCutiNotification();
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

    $('#member-table').on("click", ".btnAlasan", function () {
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

    $('#member-table').on('click', '.btnUpdateDokumen', function (){
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
                let issuedId = $(this).data('issued-id');
                let type = $(this).data('type');
                let url = base_url + '/cutie/member-cuti/update-dokumen-cuti/' + idCuti;
        
                var formData = new FormData();
                formData.append('issued_name', issuedName);
                formData.append('issued_id', issuedId);
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
                        updateMemberCutiNotification();
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

    //PILIH KARYAWAN PENGGANTI
     // MODAL REJECT
     var modalKaryawanPenggantiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalKaryawanPengganti = new bootstrap.Modal(
        document.getElementById("modal-karyawan-pengganti"),
        modalKaryawanPenggantiOptions
    );

    function openKaryawanPengganti() {
        modalKaryawanPengganti.show();
    }

    function closeKaryawanPengganti() {
        modalKaryawanPengganti.hide();
        resetKaryawanPengganti();
    }

    function resetKaryawanPengganti(){
        $('#id_cuti').val();
        $('#form-karyawan-pengganti').attr('action', '#');
    }

    function getKaryawanPengganti(idKaryawan, idKaryawanPengganti){
        $.ajax({
            url: base_url + '/cutie/member-cuti/get-karyawan-pengganti/' + idKaryawan,
            method: 'GET',
            dataType: 'JSON',
            success: function (data){
                let option = '';
                $.each(data, function (key, value){
                    option += '<option value="'+value.id+'">'+value.text+'</option>';
                })
                $('#karyawan_pengganti_id').empty();
                $('#karyawan_pengganti_id').append(option);
                if(idKaryawanPengganti != null){
                    $('#karyawan_pengganti_id').val(idKaryawanPengganti).trigger('change');
                }
                $('#karyawan_pengganti_id').select2({
                    dropdownParent: $('#modal-karyawan-pengganti')
                });
            },
            error: function (jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    $('.btnClose').on('click', function (){
        closeKaryawanPengganti();
    })

    $('#member-table').on("click",".btnKaryawanPengganti", function (){
        let idCuti = $(this).data('id');
        let idKaryawan = $(this).data('karyawan-id');
        let idKaryawanPengganti = $(this).data('karyawan-pengganti-id');
        let url = base_url + '/cutie/member-cuti/update-karyawan-pengganti/' + idCuti;
        $('#id_cuti').val(idCuti);
        $('#form-karyawan-pengganti').attr('action', url);
        openKaryawanPengganti();
        getKaryawanPengganti(idKaryawan,idKaryawanPengganti);
    })

    $('#form-karyawan-pengganti').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-karyawan-pengganti').attr('action');

        var formData = new FormData($('#form-karyawan-pengganti')[0]);
        formData.append('_method', 'PATCH');
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
                closeKaryawanPengganti();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

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

})