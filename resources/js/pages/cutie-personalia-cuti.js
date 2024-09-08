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
    // getJenisCutiKhusus();
    // let jenisCutiKhusus;
    // function getJenisCutiKhusus() {
    //     loadingSwalShow();
    //     $.ajax({
    //         url: base_url + '/cutie/pengajuan-cuti/get-data-jenis-cuti-khusus',
    //         type: "get",
    //         success: function (response) {
    //             var data = response.data;
    //             jenisCutiKhusus = data;
    //             loadingSwalClose();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             showToast({ icon: "error", title: jqXHR.responseJSON.message });
    //         },
    //     });
    // };
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "nama" },
        { data: "rencana_mulai_cuti" },
        { data: "rencana_selesai_cuti" },
        { data: "aktual_mulai_cuti" },
        { data: "aktual_selesai_cuti" },
        { data: "durasi" },
        { data: "jenis" },
        { data: "alasan" },
        { data: "karyawan_pengganti" },
        { data: "checked_1" },
        { data: "checked_2" },
        { data: "approved" },
        { data: "legalized" },
        { data: "status_dokumen" },
        { data: "status" },
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