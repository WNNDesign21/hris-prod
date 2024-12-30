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

    //DATATABLE ORGANISASI
    var columnsTable = [
        { data: "id_device" },
        { data: "cloud_id" },
        { data: "device_sn" },
        { data: "device_name" },
        { data: "server_ip" },
        { data: "server_port" },
        { data: "aksi" },
    ];

    var deviceTable = $("#device-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/attendance/device/datatable",
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

        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [0, -1],
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = deviceTable.search();
        if (searchValue) {
            deviceTable.search(searchValue).draw();
        } else {
            deviceTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openDevice();
    })

    $('.btnClose').on("click", function (){
        closeDevice();
    })

    // MODAL
    var modalInputDeviceOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputDevice = new bootstrap.Modal(
        document.getElementById("modal-input-device"),
        modalInputDeviceOptions
    );

    function openDevice() {
        modalInputDevice.show();
    }

    function closeDevice() {
        modalInputDevice.hide();
    }

    var modalEditDeviceOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditDevice = new bootstrap.Modal(
        document.getElementById("modal-edit-device"),
        modalEditDeviceOptions
    );

    function openDeviceEdit() {
        modalEditDevice.show();
    }

    function closeDeviceEdit() {
        modalEditDevice.hide();
        resetDeviceEdit();
    }

    $('#device-table').on("click", '.btnEdit', function (){
        let idDevice = $(this).data('id');
        let deviceName = $(this).data('device-name');
        let deviceSN = $(this).data('device-sn');
        let serverIP = $(this).data('server-ip');
        let serverPort = $(this).data('server-port');
        let cloudID = $(this).data('cloud-id');

        let url = base_url + '/attendance/device/update/' + idDevice;
        $('#form-edit-device').attr('action', url);
        $('#device_nameEdit').val(deviceName);
        $('#device_snEdit').val(deviceSN);
        $('#server_ipEdit').val(serverIP);
        $('#server_portEdit').val(serverPort);
        $('#cloud_idEdit').val(cloudID);
        openDeviceEdit();
    })

    $('.btnCloseEdit').on("click", function (){
        closeDeviceEdit();
    })

    function resetDeviceEdit() {
        $('#device_nameEdit').val('');
        $('#device_snEdit').val('');
        $('#server_ipEdit').val('');
        $('#server_portEdit').val('');
        $('#cloud_idEdit').val('');
        $('#form-edit-device').attr('action', '#');
    }

    $('#form-tambah-device').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-tambah-device')[0]);
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
                closeDevice();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#form-edit-device').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        var formData = new FormData($('#form-edit-device')[0]);
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
                closeDeviceEdit();
                refreshTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#device-table').on('click', '.btnDelete', function (){
        var idDevice = $(this).data('id');
        Swal.fire({
            title: "Delete Device",
            text: "Apakah kamu yakin untuk menghapus device ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                let url = base_url + '/attendance/device/delete/' + idDevice;
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


});