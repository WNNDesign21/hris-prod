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
        { data: "status_change_date" },
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
                targets: [-1],
            },
            {
                targets: [-2, -1],
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center");
                },
            },
        ],
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
});