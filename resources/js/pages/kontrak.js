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
        { data: "id_kontrak" },
        { data: "nama" },
        { data: "nama_posisi" },
        { data: "no_surat" },
        { data: "issued_date" },
        { data: "jenis" },
        { data: "status" },
        { data: "durasi" },
        { data: "salary" },
        { data: "status_change_by" },
        // { data: "status_change_date" },
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "attachment" },
        { data: "aksi" },
    ];

    var kontrakTable = $("#kontrak-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/kontrak/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                console.log(dataFilter);
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
        rowReorder: {
            selector: 'td:nth-child(2)'
        },

        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1,-2],
            },
            {
                targets: [-2, -1],
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center");
                },
            },
        ],
        // dom: 'Bfrtip',
        // buttons: [
        //     {
        //         extend: 'excelHtml5', 
        //         text: 'Export to Excel',
        //         exportOptions: {
        //             columns: ':not(:nth-child(14), :nth-child(13))',
        //             search: 'applied', 
        //             order: 'applied' 
        //         }
        //     },
        // ],
    })

    //REFRESH TABLE
    function refreshTable() {
        kontrakTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openKontrak();
    })

    $('.btnClose').on("click", function (){
        closeKontrak();
    })

    $('#kontrak-table').on('click', '.btnDelete', function (){
        var idKontrak = $(this).data('id');
        Swal.fire({
            title: "Delete Kontrak",
            text: "Apakah kamu yakin untuk menghapus Kontrak ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/kontrak/delete/' + idKontrak;
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

    //Upload Kontrak Scan
    $('#kontrak-table').on('click', '.btn-file', function (){
        var idKontrak = $(this).data('id')
        let input = document.getElementById("attachment_" + idKontrak);
        let url = base_url + '/master-data/kontrak/upload-kontrak/' + idKontrak;

        if (input && input.type === 'file') {
            input.click();
    
            $(input).on('change', function(event) {
                var file = event.target.files[0];
                loadingSwalShow(); 
    
                var formData = new FormData();
                formData.append('attachment', file);
    
                $.ajax({
                    url: url, 
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        loadingSwalClose()
                        showToast({ title: data.message });
                        refreshTable();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    }
                });
            });
        } else {
            console.error('Element with ID "attachment_' + idKontrak + '" is not a file input.');
        }
    })

    // MODAL TAMBAH KARYAWAN
    var modalInputKontrakOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputKontrak = new bootstrap.Modal(
        document.getElementById("modal-input-kontrak"),
        modalInputKontrakOptions
    );

    function openKontrak() {
        modalInputKontrak.show();
        initializeSelect2();
    }

    function closeKontrak() {
        modalInputKontrak.hide();
        resetKontrak();
    }

    function resetKontrak() {
        $('#form-input-kontrak').trigger("reset");
    }

    function initializeSelect2() {
        $('#karyawan_id').select2({
            dropdownParent: $('#modal-input-kontrak'),
            ajax: {
                url: base_url + "/master-data/karyawan/get-data-karyawan",
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || "",
                        page: params.page || 1,
                    };
                },
                cache: true,
            },
        });

        $('#posisi').select2({
            dropdownParent: $('#modal-input-kontrak'),
            ajax: {
                url: base_url + "/master-data/posisi/get-data-posisi",
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || "",
                        page: params.page || 1,
                    };
                },
                cache: true,
            },
        });

        $('#jenis').select2({
            dropdownParent: $('#modal-input-kontrak'),
        })

        $('#tempat_administrasi').select2({
            dropdownParent: $('#modal-input-kontrak'),
        })
    }

    $('#form-input-kontrak').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-input-kontrak').attr('action');

        var formData = new FormData($('#form-input-kontrak')[0]);
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
                closeKontrak();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

     // MODAL TAMBAH KARYAWAN

     function initializeSelect2Edit(posisiId) {
        selectPosisiEdit(posisiId);

        $('#jenis_kontrakEdit').select2({
            dropdownParent: $('#modal-edit-kontrak'),
        })

        $('#status_kontrakEdit').select2({
            dropdownParent: $('#modal-edit-kontrak'),
        })

        $('#tempat_administrasi_kontrakEdit').select2({
            dropdownParent: $('#modal-edit-kontrak'),
        })
    }

    function selectPosisiEdit(posisiId =  '') {
        $.ajax({
            url: base_url + '/master-data/posisi/get-data-all-posisi',
            type: "get",
            success: function (data) {
                var selectPosisi = $("#posisi_kontrakEdit");
                selectPosisi.empty();
                $.each(data, function (i, val){
                    selectPosisi.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $('#posisi_kontrakEdit').val(posisiId).trigger('change');
                $('#posisi_kontrakEdit').select2({
                    dropdownParent: $('#modal-edit-kontrak')
                });
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    };

     var modalEditKontrakOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditKontrak = new bootstrap.Modal(
        document.getElementById("modal-edit-kontrak"),
        modalEditKontrakOptions
    );

    function openKontrakEdit() {
        modalEditKontrak.show();
    }

    function closeKontrakEdit() {
        modalEditKontrak.hide();
        resetKontrak();
    }

    function resetKontrak() {
        $('#form-edit-kontrak').trigger("reset");
    }

    $('#jenis_kontrakEdit').on('change', function (){
        let jenisKontrak = $(this).val();
        if(jenisKontrak == 'PKWTT'){
            $('#durasi_kontrakEdit').val('').prop('readonly', true);
            $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', true);
        } else {
            $('#durasi_kontrakEdit').val('').prop('readonly', false);
            $('#tanggal_selesai_kontrakEdit').val('').prop('readonly', false);
        }
    })

    $('#kontrak-table').on('click', '.btnEdit', function (){
        loadingSwalShow();
        var idKontrak = $(this).data('id');
        var url = base_url + '/master-data/kontrak/get-data-detail-kontrak/' + idKontrak;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (data) {
                let dataKontrak = data.data;
                initializeSelect2Edit(dataKontrak.posisi_id);
                $('#id_kontrakEdit').val(dataKontrak.id_kontrak);
                $('#nama_karyawan_kontrakEdit').val(dataKontrak.nama_karyawan);
                $('#no_surat_kontrakEdit').val(dataKontrak.no_surat);
                $('#issued_date_kontrakEdit').val(dataKontrak.issued_date);
                $('#jenis_kontrakEdit').val(dataKontrak.jenis).trigger('change');
                $('#status_kontrakEdit').val(dataKontrak.status).trigger('change');
                $('#durasi_kontrakEdit').val(dataKontrak.durasi);
                $('#salary_kontrakEdit').val(dataKontrak.salary);
                $('#deskripsi_kontrakEdit').val(dataKontrak.deskripsi);
                $('#tanggal_mulai_kontrakEdit').val(dataKontrak.tanggal_mulai);
                $('#tanggal_selesai_kontrakEdit').val(dataKontrak.tanggal_selesai);
                openKontrakEdit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    })

    $('#form-edit-kontrak').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idKontrak = $('#id_kontrakEdit').val();
        let url = base_url + '/master-data/kontrak/update/' + idKontrak;

        var formData = new FormData($('#form-edit-kontrak')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
                closeKontrakEdit();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });
});