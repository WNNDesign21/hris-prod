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
            url: base_url + '/cutie/pengajuan-cuti/get-data-jenis-cuti-khusus',
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
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "no" },
        { data: "rencana_mulai_cuti" },
        { data: "rencana_selesai_cuti" },
        { data: "aktual_mulai_cuti" },
        { data: "aktual_selesai_cuti" },
        { data: "durasi" },
        { data: "jenis" },
        { data: "alasan" },
        { data: "karyawan_pengganti" },
        { data: "checked" },
        { data: "approved" },
        { data: "legalized" },
        { data: "status_dokumen" },
        { data: "status" },
        { data: "created_at" },
        { data: "attachment" },
        { data: "aksi" },
    ];

    var cutieTable = $("#cutie-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/pengajuan-cuti-datatable",
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
    
    $('#jenis_cuti').on('change', function() {
        let jenisCuti = $(this).val();
        if (jenisCuti == 'KHUSUS') {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            conditionalField.append('<label for="jenis_cuti_khusus">Jenis Cuti Khusus</label>');
            var selectField = $('<select style="width:100%;"></select>').attr('id', 'jenis_cuti_khusus').attr('name', 'jenis_cuti_khusus');
            $.each(jenisCutiKhusus, function (i, val){
                selectField.append('<option value="'+val.id+'" data-durasi="'+val.durasi+'">'+val.text+'</option>');
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
            });

            $('#rencana_mulai_cuti').on('change', function() {
                var rencanaMulaiCuti = $(this).val();
                var durasi = $('#jenis_cuti_khusus').find('option:selected').data('durasi');
                $('#durasi_cuti').val(durasi);
                var rencanaSelesaiCuti = new Date(rencanaMulaiCuti);
                rencanaSelesaiCuti.setDate(rencanaSelesaiCuti.getDate() + durasi - 1); 
                var year = rencanaSelesaiCuti.getFullYear();
                var month = ("0" + (rencanaSelesaiCuti.getMonth() + 1)).slice(-2);
                var day = ("0" + rencanaSelesaiCuti.getDate()).slice(-2);
                var formattedDate = year + "-" + month + "-" + day;
                $('#rencana_selesai_cuti').val(formattedDate);
                console.log(formattedDate);
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
        $('#form-pengajuan-cuti').attr('action', url);
    }

    $('#cutie-table').on('click', '.btnDelete', function (){
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
                var url = base_url + '/cutie/pengajuan-cuti/delete/' + idCuti;
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

    $('#cutie-table').on('click', '.btnEdit', function (){
        var idCuti = $(this).data('id');
        var url = base_url + '/cutie/pengajuan-cuti/get-data-detail-cuti/' + idCuti;
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                var data = response.data;
                console.log(data)
                $('#id_cuti').val(data.id_cuti);
                $('#alasan_cuti').val(data.alasan_cuti);
                $('#durasi_cuti').val(data.durasi_cuti);

                if(data.jenis_cuti == 'KHUSUS') {
                    $('#jenis_cuti').val(data.jenis_cuti).trigger('change');
                    $('#jenis_cuti_khusus').val(data.jenis_cuti_id).trigger('change');
                    $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti).trigger('change');
                    $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti).trigger('change');
                } else if (data.jenis_cuti == 'SAKIT') {
                    $('#jenis_cuti').val(data.jenis_cuti).trigger('change');
                    $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti);
                    $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti);
                } else {
                    $('#jenis_cuti').val(data.jenis_cuti);
                    $('#rencana_mulai_cuti').val(data.rencana_mulai_cuti);
                    $('#rencana_selesai_cuti').val(data.rencana_selesai_cuti).attr('min', data.rencana_mulai_cuti);
                }
                $('#form-pengajuan-cuti').attr('action', base_url + '/cutie/pengajuan-cuti/update/' + idCuti);
                openForm();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })
});