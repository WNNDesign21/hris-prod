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

    //GET JENIS CUTI KHUSUS AND SAVE TO LOCAL STORAGE
    getJenisCutiKhusus();
    let jenisCutiKhusus;
    function getJenisCutiKhusus() {
        loadingSwalShow();
        $.ajax({
            url: base_url + '/cutie/ajax/get-data-jenis-cuti-khusus',
            type: "get",
            success: function (response) {
                var data = response.data;
                jenisCutiKhusus = data;
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    };

    function updateNotification(){
        $.ajax({
            url: base_url + '/get-notification',
            method: 'GET',
            success: function(response){
                $('.notifications-menu').html(response.data);
            }
        })
    }

    function updatePengajuanCutiNotification(){
        $.ajax({
            url: base_url + '/get-pengajuan-cuti-notification',
            method: 'GET',
            success: function(response){
                $('.notification-pengajuan-cuti').html(response.data);
            }
        })
    }

    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "aksi" },
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

    var cutieTable = $("#personal-table").DataTable({
        search: {
            return: true,
        },
        order: [[13, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/pengajuan-cuti/datatable",
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
    var modalPengajuanCutiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalPengajuanCuti = new bootstrap.Modal(
        document.getElementById("modal-pengajuan-cuti"),
        modalPengajuanCutiOptions
    );

    function openForm() {
        modalPengajuanCuti.show();
    }

    function closeForm() {
        modalPengajuanCuti.hide();
        resetForm();
    }

    //SELECT2 & CONDITIONAL FIELD
    $('#jenis_cuti').select2({
        dropdownParent: $('#modal-pengajuan-cuti'),
    });

    $('#penggunaan_sisa_cuti').select2({
        dropdownParent: $('#modal-pengajuan-cuti'),
    });

    $('#jenis_cuti').on('change', function() {
        let jenisCuti = $(this).val();

        $('#rencana_mulai_cuti').attr('min', minDate.toISOString().split('T')[0]);
        $('#rencana_mulai_cuti').val('');
        if (jenisCuti == 'KHUSUS') {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            conditionalField.append('<label for="jenis_cuti_khusus">Jenis Cuti Khusus</label>');
            var selectField = $('<select style="width:100%;"></select>').attr('id', 'jenis_cuti_khusus').attr('name', 'jenis_cuti_khusus');
            $.each(jenisCutiKhusus, function (i, val){
                selectField.append('<option value="'+val.id+'" data-durasi="'+val.durasi+'" data-isurgent="'+val.isurgent+'" data-isworkday="'+val.isworkday+'">'+val.text+'</option>');
            });
            conditionalField.append(selectField);
            $('#rencana_selesai_cuti').val('');
            $('#rencana_selesai_cuti').prop('readonly', true);
            $('#jenis_cuti_khusus').select2({
                dropdownParent: $('#modal-pengajuan-cuti'),
            });

            $('#jenis_cuti_khusus').on('change', function() {
                $('#rencana_mulai_cuti').val('');
                $('#rencana_selesai_cuti').val('');
                var isUrgent = $(this).find('option:selected').data('isurgent');
                if(isUrgent == 'Y'){
                    $('#rencana_mulai_cuti').removeAttr('min');
                } else {
                    $('#rencana_mulai_cuti').attr('min', minDate.toISOString().split('T')[0]);
                }
            });

            $('#rencana_mulai_cuti').on('change', function() {
                var rencanaMulaiCuti = $(this).val();
                var durasi = $('#jenis_cuti_khusus').find('option:selected').data('durasi');
                var isWorkday = $('#jenis_cuti_khusus').find('option:selected').data('isworkday');
                $('#durasi_cuti').val(durasi);
                var rencanaSelesaiCuti = new Date(rencanaMulaiCuti);

                if (isWorkday === 'N') {
                    // Logic mengecek hari sabtu dan minggu, jika durasi cuti jatuh pada hari sabtu dan minggu,
                    // maka akan di skip dan dilanjutkan di hari kerja berikutnya
                    for (var i = 0; i < durasi - 1; i++) {
                        rencanaSelesaiCuti.setDate(rencanaSelesaiCuti.getDate() + 1);
                        if (rencanaSelesaiCuti.getDay() === 6) { // Saturday
                            rencanaSelesaiCuti.setDate(rencanaSelesaiCuti.getDate() + 2);
                        } else if (rencanaSelesaiCuti.getDay() === 0) { // Sunday
                            rencanaSelesaiCuti.setDate(rencanaSelesaiCuti.getDate() + 1);
                        }
                    }
                } else {
                    // Jika isWorkday bukan 'N', cukup tambahkan rencanaMulaiCuti + durasi
                    rencanaSelesaiCuti.setDate(rencanaSelesaiCuti.getDate() + (durasi - 1));
                }

                var year = rencanaSelesaiCuti.getFullYear();
                var month = ("0" + (rencanaSelesaiCuti.getMonth() + 1)).slice(-2);
                var day = ("0" + rencanaSelesaiCuti.getDate()).slice(-2);
                var formattedDate = year + "-" + month + "-" + day;
                $('#rencana_selesai_cuti').val(formattedDate);
            });
        } else if (jenisCuti == 'SAKIT') {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            conditionalField.append('<label for="alasan_sakit">Bukti Surat Dokter</label>');
            var inputField = $('<input type="file" class="form-control">').attr('id', 'attachment').attr('name', 'attachment');
            conditionalField.append(inputField);
            $('#rencana_selesai_cuti').val('');
            $('#rencana_selesai_cuti').prop('readonly', false);
        } else {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            $('#rencana_selesai_cuti').val('');
            $('#rencana_selesai_cuti').prop('readonly', false);
            $('#penggunaan_sisa_cuti').select2({
                dropdownParent: $('#modal-pengajuan-cuti'),
            });

            let minDate = new Date();
            minDate.setDate(minDate.getDate() + 7);
            $('#rencana_mulai_cuti').attr('min', minDate.toISOString().split('T')[0]);

            $('#rencana_mulai_cuti').change(function() {
                var selesaiCutiInput = $('#rencana_selesai_cuti');
                $('#rencana_selesai_cuti').val('');
                selesaiCutiInput.attr('min', $(this).val());
            });

            $('#rencana_selesai_cuti').change(function() {
                var rencanaMulaiCuti = $('#rencana_mulai_cuti').val();
                var rencanaSelesaiCuti = $(this).val();
                var durasi = calculateDuration(rencanaMulaiCuti, rencanaSelesaiCuti) + 1;
                $('#durasi_cuti').val(durasi);
            });
        }
    });

    $('#form-pengajuan-cuti').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-pengajuan-cuti').attr('action');

        var formData = new FormData($('#form-pengajuan-cuti')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                $('#sisa_cuti_total_display').text(data.data.sisa_cuti_tahunan+' Hari');
                $('#sisa_cuti_pribadi').text(data.data.sisa_cuti_pribadi+' Hari');
                $('#sisa_cuti_tahun_lalu').text(data.data.sisa_cuti_tahun_lalu+' Hari');
                updateNotification();
                updatePengajuanCutiNotification();
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

    let minDate = new Date();
    minDate.setDate(minDate.getDate() + 7);
    $('#rencana_mulai_cuti').attr('min', minDate.toISOString().split('T')[0]);

    $('#rencana_mulai_cuti').change(function() {
        var selesaiCutiInput = $('#rencana_selesai_cuti');
        $('#rencana_selesai_cuti').val('');
        selesaiCutiInput.attr('min', $(this).val());
    });

    $('#rencana_selesai_cuti').change(function() {
        var rencanaMulaiCuti = $('#rencana_mulai_cuti').val();
        var rencanaSelesaiCuti = $(this).val();
        var durasi = calculateDuration(rencanaMulaiCuti, rencanaSelesaiCuti) + 1;
        $('#durasi_cuti').val(durasi);
    });

    function calculateDuration(start, end) {
        var startDate = new Date(start);
        var endDate = new Date(end);
        var timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
        var duration = Math.ceil(timeDiff / (1000 * 3600 * 24));
        return duration;
    }

    function resetForm(){
        let url = base_url + '/cutie/pengajuan-cuti/store';
        $('#id_cuti').val('');
        $('#rencana_mulai_cuti').val('');
        $('#rencana_mulai_selesai').val('');
        $('#alasan_cuti').val('');
        $('#durasi_cuti').val('');
        $('#jenis_cuti').val('PRIBADI').trigger('change');
        $('#penggunaan_sisa_cuti').val('TB').trigger('change');
        $('#form-pengajuan-cuti').attr('action', url);
        $('input[name="_method"]').val('POST');
    }

    $('#personal-table').on('click', '.btnDelete', function (){
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
                loadingSwalShow();
                var url = base_url + '/cutie/pengajuan-cuti/delete/' + idCuti;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        $('#sisa_cuti_total_display').text(data.data.sisa_cuti_tahunan+' Hari');
                        $('#sisa_cuti_pribadi').text(data.data.sisa_cuti_pribadi+' Hari');
                        $('#sisa_cuti_tahun_lalu').text(data.data.sisa_cuti_tahun_lalu+' Hari');
                        updateNotification();
                        updatePengajuanCutiNotification();
                        refreshTable();
                        showToast({ title: data.message });
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

    $('#personal-table').on("click", ".btnAlasan", function () {
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

    $('#personal-table').on('click', '.btnCancel', function (){
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
                        updatePengajuanCutiNotification();
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

    // $('#personal-table').on('click', '.btnEdit', function (){
    //     var idCuti = $(this).data('id');
    //     var url = base_url + '/cutie/ajax/get-data-detail-cuti/' + idCuti;
    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         success: function (response) {
    //             var data = response.data;
    //             $('#id_cuti').val(data.id_cuti);
    //             $('#alasan_cuti').val(data.alasan_cuti);
    //             $('#durasi_cuti').val(data.durasi_cuti);

    //             if(data.jenis_cuti == 'KHUSUS') {
    //                 $('#jenis_cuti').val(data.jenis_cuti).trigger('change');
    //                 $('#jenis_cuti_khusus').val(data.jenis_cuti_id).trigger('change');
    //                 $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti).trigger('change');
    //                 $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti).trigger('change');
    //             } else if (data.jenis_cuti == 'SAKIT') {
    //                 $('#jenis_cuti').val(data.jenis_cuti).trigger('change');
    //                 $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti);
    //                 $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti);
    //             } else {
    //                 $('#jenis_cuti').val(data.jenis_cuti);
    //                 $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti);
    //                 $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti);
    //             }
    //             $('#form-pengajuan-cuti').attr('action', base_url + '/cutie/pengajuan-cuti/update/' + idCuti);
    //             $('#form-pengajuan-cuti').append('<input type="hidden" name="_method" value="PATCH">');
    //             openForm();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //         },
    //     });
    // })
});
