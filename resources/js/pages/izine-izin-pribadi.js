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
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
        { data: "jenis_izin" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "legalized_by" },
        { data: "lampiran" },
        { data: "aksi" },
    ];

    var izineTable = $("#izin-pribadi-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/izin-pribadi-datatable",
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
        izineTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openInputForm();
    })

    $('.btnClose').on("click", function (){
        closeInputForm();
    })

    // MODAL TAMBAH KARYAWAN
    var modalInputIzinPribadiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputIzinPribadi = new bootstrap.Modal(
        document.getElementById("modal-input-izin-pribadi"),
        modalInputIzinPribadiOptions
    );

    function openInputForm() {
        modalInputIzinPribadi.show();
    }

    function closeInputForm() {
        modalInputIzinPribadi.hide();
        resetForm();
    }

    $('#jenis_izin').select2({
        dropdownParent: $('#modal-input-izin-pribadi'),
    });

    $('#jenis_izin').on('change', function(){
        if($(this).val() == 'SK'){
            $('#conditional_field').empty().append(`
                <div class="form-group">
                    <label for="lampiran">Lampiran</label>
                    <input type="file" class="form-control" id="lampiran" name="lampiran">
                </div>
                `);
            $('#lampiran').prop('required', true);
        }else{
            $('#conditional_field').empty();
            $('#lampiran').prop('required', false);
        }
    })
});