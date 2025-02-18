$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    // LOADING & ALERT
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
    // END LOADING & ALERT

    // DATATABLE
    // var columnsTable = [
    //     { data: "id_tugasluar" },
    //     { data: "karyawan" },
    //     { data: "kendaraan" },
    //     { data: "pergi" },
    //     { data: "kembali" },
    //     { data: "jarak" },
    //     { data: "rute" },
    //     { data: "keterangan" },
    //     { data: "status" },
    //     { data: "aksi" },
    // ];

    // var pengajuanTable = $("#pengajuan-table").DataTable({
    //     search: {
    //         return: true,
    //     },
    //     order: [[0, "DESC"]],
    //     processing: true,
    //     serverSide: true,
    //     ajax: {
    //         url: base_url + "/tugasluare/pengajuan/datatable",
    //         dataType: "json",
    //         type: "POST",
    //         data: function (dataFilter) {
    //             let filterNopol = '';
    //             let filterStatus = '';
    //             let filterFrom = '';
    //             let filterTo = '';

    //             dataFilter.nopol = filterNopol;
    //             dataFilter.status = filterStatus;
    //             dataFilter.from = filterFrom;
    //             dataFilter.to = filterTo;
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             if (jqXHR.responseJSON.data) {
    //                 var error = jqXHR.responseJSON.data.error;
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: " <br>Application error!",
    //                     html:
    //                         '<div class="alert alert-danger text-left" role="alert">' +
    //                         "<p>Error Message: <strong>" +
    //                         error +
    //                         "</strong></p>" +
    //                         "</div>",
    //                     allowOutsideClick: false,
    //                     showConfirmButton: true,
    //                 }).then(function () {
    //                     refreshTable();
    //                 });
    //             } else {
    //                 var message = jqXHR.responseJSON.message;
    //                 var errorLine = jqXHR.responseJSON.line;
    //                 var file = jqXHR.responseJSON.file;
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: " <br>Application error!",
    //                     html:
    //                         '<div class="alert alert-danger text-left" role="alert">' +
    //                         "<p>Error Message: <strong>" +
    //                         message +
    //                         "</strong></p>" +
    //                         "<p>File: " +
    //                         file +
    //                         "</p>" +
    //                         "<p>Line: " +
    //                         errorLine +
    //                         "</p>" +
    //                         "</div>",
    //                     allowOutsideClick: false,
    //                     showConfirmButton: true,
    //                 }).then(function () {
    //                     refreshTable();
    //                 });
    //             }
    //         },
    //     },

    //     columns: columnsTable,
    //     columnDefs: [
    //         {
    //             orderable: false,
    //             targets: [0, -1],
    //         },
    //     ],
    // })
    // END DATATABLE
    
    // MODAL
    var modalInputOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInput = new bootstrap.Modal(
        document.getElementById("modal-input"),
        modalInputOptions
    );

    var modalEditOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEdit = new bootstrap.Modal(
        document.getElementById("modal-edit"),
        modalEditOptions
    );
    // END MODAL


    // FUNCTION
    function refreshTable() {
        var searchValue = pengajuanTable.search();
        if (searchValue) {
            pengajuanTable.search(searchValue).draw();
        } else {
            pengajuanTable.search("").draw();
        }
    }

    function open() {
        modalInput.show();
    }

    function close() {
        modalInput.hide();
    }

    function openEdit() {
        modalEdit.show();
    }

    function closeEdit() {
        modalEdit.hide();
        resetEdit();
    }

    function resetEdit() {
        
    }
    // END FUNCTION


    // EVENT
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        open();
    })

    $('.btnClose').on("click", function (){
        close();
    })

    $('#pengajuan-table').on("click", '.btnEdit', function (){
        let id = $(this).data('id');
        let pengajuanName = $(this).data('pengajuan-name');
        let pengajuanSN = $(this).data('pengajuan-sn');
        let serverIP = $(this).data('server-ip');
        let serverPort = $(this).data('server-port');
        let cloudID = $(this).data('cloud-id');

        let url = base_url + '/tugasluare/pengajuan/update/' + id;
        $('#form-edit-pengajuan').attr('action', url);
        $('#pengajuan_nameEdit').val(pengajuanName);
        $('#pengajuan_snEdit').val(pengajuanSN);
        $('#server_ipEdit').val(serverIP);
        $('#server_portEdit').val(serverPort);
        $('#cloud_idEdit').val(cloudID);
        openEdit();
    })

    $('.btnCloseEdit').on("click", function (){
        closeEdit();
    })
    
    $('#form-input').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-input')[0]);
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
                close();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#form-edit').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-edit')[0]);
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
                closeEdit();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#pengajuan-table').on('click', '.btnDelete', function (){
        var id = $(this).data('id');
        Swal.fire({
            title: "Delete ",
            text: "Apakah kamu yakin untuk menghapus pengajuan ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                let url = base_url + '/tugasluare/pengajuan/delete/' + id;
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
    // END EVENT
});