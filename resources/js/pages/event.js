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

    //DATATABLE EVENT
    var columnsTable = [
        { data: "jenis_event" },
        { data: "keterangan" },
        { data: "durasi" },
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "aksi" },
    ];

    var eventTable = $("#event-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/event/datatable",
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
                targets: [-1],
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        eventTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH EVENT
    $('.btnAdd').on("click", function (){
        openEvent();
    })

    //CLOSE MODAL TAMBAH EVENT
    $('.btnClose').on("click", function (){
        closeEvent();
    })

    // MODAL TAMBAH EVENT
    var modalInputEventOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputEvent = new bootstrap.Modal(
        document.getElementById("modal-input-event"),
        modalInputEventOptions
    );

    function openEvent() {
        modalInputEvent.show();
    }

    function closeEvent() {
        $('#jenis_event').val('');
        $('#keterangan').val('');
        $('#tanggal_mulai').val('');
        $('#tanggal_selesai').val('');
        modalInputEvent.hide();
    }

    //MODAL TAMBAH EVENT DATE CONFIG
    let currentYear = new Date().getFullYear();
    let minDate = new Date(currentYear, 0, 1);
    let maxDate = new Date(currentYear, 11, 31);

    $('#jenis_event').select2({
        dropdownParent: $('#modal-input-event'),
    });
    $('#tanggal_mulai').attr('min', minDate.toISOString().split('T')[0]);
    $('#tanggal_mulai').attr('max', maxDate.toISOString().split('T')[0]);

    $('#tanggal_mulai').on('change', function() {
        let startDate = new Date($(this).val());

        $('#tanggal_selesai').val('');
        $('#tanggal_selesai').attr('min', startDate.toISOString().split('T')[0]);
    });


    //SUBMIT TAMBAH EVENT
    $('#form-tambah-event').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-event').attr('action');

        var formData = new FormData($('#form-tambah-event')[0]);
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
                refreshTable();
                closeEvent();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //DELETE EVENT
    $('#event-table').on('click', '.btnDelete', function (){
        var idEvent = $(this).data('id');
        Swal.fire({
            title: "Delete Event",
            text: "Apakah kamu yakin untuk menghapus event ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/event/delete/' + idEvent;
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