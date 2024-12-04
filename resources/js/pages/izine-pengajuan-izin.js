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
            url: base_url + '/get-pengajuan-izin-notification',
            method: 'GET',
            success: function(response){
                $('.notification-pengajuan-izin').html(response.data);
            }
        })
    }
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "id_izin" },
        { data: "rencana_mulai_or_masuk" },
        { data: "rencana_selesai_or_keluar" },
        { data: "aktual_mulai_or_masuk" },
        { data: "aktual_selesai_or_keluar" },
        { data: "jenis_izin" },
        { data: "durasi" },
        { data: "keterangan" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "legalized_by" },
        { data: "aksi" },
    ];

    var izineTable = $("#pengajuan-izin-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/izine/pengajuan-izin-datatable",
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
        document.getElementById("modal-pengajuan-izin"),
        modalInputIzinPribadiOptions
    );

    function openInputForm() {
        modalInputIzinPribadi.show();
    }

    function closeInputForm() {
        modalInputIzinPribadi.hide();
        resetForm();
    }
    
    function resetForm(){
        $('#jenis_izin').val('TM').trigger('change');
        $('#keterangan').val('');
        $('#conditional_field').empty().append(`
            <div class="form-group">
                <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                    Mulai</label>
                <input type="date" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                    class="form-control" required>
            </div>
            <div class="form-group">
                <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana
                    Selesai</label>
                <input type="date" name="rencana_selesai_or_keluar"
                    id="rencana_selesai_or_keluar" class="form-control" required>
            </div>    
            <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                    Selesai di tanggal yang sama!</small>     
        `);

        let minDate = moment().format('YYYY-MM-DD');
        $('#rencana_mulai_or_masuk').attr('min', minDate);
        $('#rencana_selesai_or_keluar').attr('min', minDate);

        $('#rencana_mulai_or_masuk').on('change', function(){
            let rencana_mulai = $(this).val();
            $('#rencana_selesai_or_keluar').attr('min', rencana_mulai); 

            if(rencana_mulai > $('#rencana_selesai_or_keluar').val()){
                $('#rencana_selesai_or_keluar').val(rencana_mulai);
            }
        });

        $('#rencana_selesai_or_keluar').on('change', function(){
            let rencana_selesai = $(this).val();
            if(rencana_selesai < $('#rencana_mulai_or_masuk').val()){
                $(this).val($('#rencana_mulai_or_masuk').val());
            }
        })
    }

    $('#jenis_izin').select2({
        dropdownParent: $('#modal-pengajuan-izin'),
    });

    let minDate = moment().format('YYYY-MM-DD');
    $('#rencana_mulai_or_masuk').attr('min', minDate);
    $('#rencana_selesai_or_keluar').attr('min', minDate);

    $('#rencana_mulai_or_masuk').on('change', function(){
        let rencana_mulai = $(this).val();
        $('#rencana_selesai_or_keluar').attr('min', rencana_mulai); 

        if(rencana_mulai > $('#rencana_selesai_or_keluar').val()){
            $('#rencana_selesai_or_keluar').val(rencana_mulai);
        }
    });

    $('#rencana_selesai_or_keluar').on('change', function(){
        let rencana_selesai = $(this).val();
        if(rencana_selesai < $('#rencana_mulai_or_masuk').val()){
            $(this).val($('#rencana_mulai_or_masuk').val());
        }
    })

    $('#jenis_izin').on('change', function(){
        var jenis_izin = $(this).val();
        if(jenis_izin == 'TM'){
            $('#conditional_field').empty().append(`
                <div class="form-group">
                    <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                        Mulai</label>
                    <input type="date" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                        class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana
                        Selesai</label>
                    <input type="date" name="rencana_selesai_or_keluar"
                        id="rencana_selesai_or_keluar" class="form-control" required>
                </div>    
                <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                        Selesai di tanggal yang sama!</small>     
            `);

            let minDate = moment().format('YYYY-MM-DD');
            $('#rencana_mulai_or_masuk').attr('min', minDate);
            $('#rencana_selesai_or_keluar').attr('min', minDate);

            $('#rencana_mulai_or_masuk').on('change', function(){
                let rencana_mulai = $(this).val();
                $('#rencana_selesai_or_keluar').attr('min', rencana_mulai); 

                if(rencana_mulai > $('#rencana_selesai_or_keluar').val()){
                    $('#rencana_selesai_or_keluar').val(rencana_mulai);
                }
            });

            $('#rencana_selesai_or_keluar').on('change', function(){
                let rencana_selesai = $(this).val();
                if(rencana_selesai < $('#rencana_mulai_or_masuk').val()){
                    $(this).val($('#rencana_mulai_or_masuk').val());
                }
            })
        }else{
            $('#conditional_field').empty().append(`
                <div class="form-group">
                    <label for="masuk_or_keluar" id="label_masuk_or_keluar">Masuk / Keluar</label>
                    <select name="masuk_or_keluar" id="masuk_or_keluar" class="form-control" required>
                        <option value="M">Masuk</option>
                        <option value="K">Keluar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rencana_masuk_or_keluar" id="label_rencana_masuk_or_keluar">Jam Masuk / Keluar</label>
                    <input type="datetime-local" name="rencana_masuk_or_keluar" id="rencana_masuk_or_keluar"
                        class="form-control" required>
                </div>
            `);

            let minDate = moment().format('YYYY-MM-DDT00:00');
            $('#rencana_masuk_or_keluar').attr('min', minDate);
            $('#masuk_or_keluar').select2({
                dropdownParent: $('#modal-pengajuan-izin'),
            });
        }
    });

    $('#form-pengajuan-izin').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-pengajuan-izin').attr('action');
        var formData = new FormData($('#form-pengajuan-izin')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                updateNotification();
                showToast({ title: data.message });
                refreshTable();
                closeInputForm();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#form-pengajuan-izin-edit').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let idIzin = $('#id_izinEdit').val();
        let url = base_url + '/izine/pengajuan-izin/update/' + idIzin;
        var formData = new FormData($('#form-pengajuan-izin-edit')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                updateNotification();
                showToast({ title: data.message });
                refreshTable();
                closeFormEdit();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('#pengajuan-izin-table').on('click', '.btnDelete', function (){
        var idIzin = $(this).data('id-izin');
        console.log(idIzin);
        Swal.fire({
            title: "Delete Izin",
            text: "Apakah kamu yakin untuk menghapus Pengajuan Izin ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/izine/pengajuan-izin/delete/' + idIzin;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        loadingSwalClose();
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

    $('#pengajuan-izin-table').on('click', '.btnEdit', function (){
        loadingSwalShow();
        var idIzin = $(this).data('id-izin');
        var url = base_url + '/izine/pengajuan-izin/get-data-izin/' + idIzin;
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                var data = response.data;
                $('#id_izinEdit').val(data.id_izin);
                $('#keteranganEdit').val(data.keterangan);

                if(data.jenis_izin == 'TM'){
                    $('#conditional_fieldEdit').empty().append(`
                        <div class="form-group">
                            <label for="rencana_mulai_or_masukEdit" id="label_rencana_mulai_or_masukEdit">Rencana
                                Mulai</label>
                            <input type="date" name="rencana_mulai_or_masukEdit" id="rencana_mulai_or_masukEdit"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="rencana_selesai_or_keluarEdit" id="label_rencana_selesai_or_keluarEdit">Rencana
                                Selesai</label>
                            <input type="date" name="rencana_selesai_or_keluarEdit"
                                id="rencana_selesai_or_keluarEdit" class="form-control" required>
                        </div>    
                        <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                                Selesai di tanggal yang sama!</small>     
                    `);
        
                    let minDate = moment(data.rencana_mulai_or_masuk).format('YYYY-MM-DD');
                    $('#rencana_mulai_or_masukEdit').attr('min', moment().format('YYYY-MM-DD'));
                    $('#rencana_selesai_or_keluarEdit').attr('min', minDate);
        
                    $('#rencana_mulai_or_masukEdit').on('change', function(){
                        let rencana_mulai = $(this).val();
                        $('#rencana_selesai_or_keluarEdit').attr('min', rencana_mulai); 
        
                        if(rencana_mulai > $('#rencana_selesai_or_keluarEdit').val()){
                            $('#rencana_selesai_or_keluarEdit').val(rencana_mulai);
                        }
                    });
        
                    $('#rencana_selesai_or_keluar').on('change', function(){
                        let rencana_selesai = $(this).val();
                        if(rencana_selesai < $('#rencana_mulai_or_masuk').val()){
                            $(this).val($('#rencana_mulai_or_masuk').val());
                        }
                    })

                    $('#rencana_mulai_or_masukEdit').val(moment(data.rencana_mulai_or_masuk).format('YYYY-MM-DD'));
                    $('#rencana_selesai_or_keluarEdit').val(moment(data.rencana_selesai_or_keluar).format('YYYY-MM-DD'));
                }else{
                    $('#conditional_fieldEdit').empty().append(`
                        <div class="form-group">
                            <label for="masuk_or_keluarEdit" id="label_masuk_or_keluarEdit">Masuk / Keluar</label>
                            <select name="masuk_or_keluarEdit" id="masuk_or_keluarEdit" class="form-control" style="width:100%;" required>
                                <option value="M">Masuk</option>
                                <option value="K">Keluar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rencana_masuk_or_keluarEdit" id="label_rencana_masuk_or_keluar">Jam Masuk / Keluar</label>
                            <input type="datetime-local" name="rencana_masuk_or_keluarEdit" id="rencana_masuk_or_keluarEdit"
                                class="form-control" required>
                        </div>
                    `);
        
                    let minDate = moment(data.rencana_mulai_or_masuk ? data.rencana_mulai_or_masuk : data.rencana_selesai_or_keluar).format('YYYY-MM-DDTHH:mm');
                    $('#rencana_masuk_or_keluarEdit').attr('min', minDate);
                    $('#masuk_or_keluarEdit').select2({
                        dropdownParent: $('#modal-pengajuan-izin-edit'),
                    });
                    $('#masuk_or_keluarEdit').val(data.masuk_or_keluar).trigger('change');
                    $('#rencana_masuk_or_keluarEdit').val(moment(data.rencana_mulai_or_masuk ? data.rencana_mulai_or_masuk : data.rencana_selesai_or_keluar).format('YYYY-MM-DDTHH:mm'));
                }

                loadingSwalClose();
                openFormEdit();
            
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })

    $('.btnEdit').on("click", function (){
        openFormEdit();
    })

    $('.btnCloseEdit').on("click", function (){
        closeFormEdit();
    })

    // MODAL EDIT KARYAWAN
    var modalEditIzinPribadiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditIzinPribadi = new bootstrap.Modal(
        document.getElementById("modal-pengajuan-izin-edit"),
        modalEditIzinPribadiOptions
    );

    function openFormEdit() {
        modalEditIzinPribadi.show();
    }

    function closeFormEdit() {
        modalEditIzinPribadi.hide();
        resetFormEdit();
    }

    function resetFormEdit(){
        $('#conditional_fieldEdit').empty();
        $('#keteranganEdit').val('');
    }
});