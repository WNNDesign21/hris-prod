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

    //DATATABLE GRUP
    var columnsTable = [
        { data: "no" },
        { data: "nama" },
        { data: "jam_masuk" },
        { data: "jam_keluar" },
        { data: "toleransi_waktu" },
        { data: "aksi" },
    ];

    var grupTable = $("#grup-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/grup/datatable",
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
            // {
            //     targets: [],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         $(td).addClass("text-center");
            //     },
            // },
            // {
            //     targets: [0],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        grupTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //OPEN MODAL TAMBAH GRUP
    $('.btnAdd').on("click", function (){
        openGrup();
    })

    //CLOSE MODAL TAMBAH GRUP
    $('.btnClose').on("click", function (){
        closeGrup();
    })


    // MODAL TAMBAH GRUP
    var modalInputGrupOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputGrup = new bootstrap.Modal(
        document.getElementById("modal-input-grup"),
        modalInputGrupOptions
    );

    function openGrup() {
        modalInputGrup.show();
    }

    function closeGrup() {
        $('#nama_grup').val('');
        $('#jam_masuk').val('');
        $('#jam_keluar').val('');
        $('#toleransi_waktu').val('');
        modalInputGrup.hide();
    }

    //SUBMIT TAMBAH GRUP
    $('#form-tambah-grup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-grup').attr('action');

        var formData = new FormData($('#form-tambah-grup')[0]);
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
                closeGrup();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    // MODAL EDIT GRUP
    var modalEditGrupOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditGrup = new bootstrap.Modal(
        document.getElementById("modal-edit-grup"),
        modalEditGrupOptions
    );

    function openEditGrup() {
        modalEditGrup.show();
    }

    function closeEditGrup() {
        $('#id_grup_edit').val('');
        $('#nama_grup_edit').val('');
        $('#jam_masuk_edit').val('');
        $('#jam_keluar_edit').val('');
        $('#toleransi_waktu_edit').val('');
        modalEditGrup.hide();
    }

    $('.btnCloseEdit').on("click", function (){
        closeEditGrup();
    })

    //EDIT GRUP
    $('#grup-table').on('click', '.btnEdit', function (){
        var idGrup = $(this).data('id');
        var nama = $(this).data('grup-nama');
        var jamMasuk = $(this).data('jam-masuk');
        var jamKeluar = $(this).data('jam-keluar');
        var toleransiWaktu = $(this).data('toleransi-waktu');

        $('#id_grup_edit').val(idGrup);
        $('#nama_grup_edit').val(nama);
        $('#jam_masuk_edit').val(jamMasuk);
        $('#jam_keluar_edit').val(jamKeluar);
        $('#toleransi_waktu_edit').val(toleransiWaktu);
        openEditGrup();
    });

    //SUBMIT EDIT GRUP
    $('#form-edit-grup').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idGrup = $('#id_grup_edit').val();
        let url = base_url + '/master-data/grup/update/' + idGrup;

        var formData = new FormData($('#form-edit-grup')[0]);
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
                closeEditGrup();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    //DELETE GRUP
    $('#grup-table').on('click', '.btnDelete', function (){
        var idGrup = $(this).data('id');
        Swal.fire({
            title: "Delete Grup",
            text: "Apakah kamu yakin untuk menghapus grup ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                var url = base_url + '/master-data/grup/delete/' + idGrup;
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

    // MODAL TAMBAH SHIFT PATTERN
    var modalInputShiftPatternOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInputShiftPattern = new bootstrap.Modal(
        document.getElementById("modal-input-shift-pattern"),
        modalInputShiftPatternOptions
    );

    function openShiftPattern() {
        modalInputShiftPattern.show();
    }

    function closeShiftPattern() {
        resetSp();
        modalInputShiftPattern.hide();
    }

    //SUBMIT TAMBAH GRUP
    $('#form-tambah-shift-pattern').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-tambah-shift-pattern').attr('action');

        var formData = new FormData($('#form-tambah-shift-pattern')[0]);
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
                refreshTableSp();
                closeShiftPattern();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

     //RELOAD TABLE
     $('.btnReloadSp').on("click", function (){
        refreshTableSp();
    })

    //DATATABLE GRUP
    var columnsSpTable = [
        { data: "no" },
        { data: "nama" },
        { data: "urutan" },
        { data: "aksi" },
    ];

    var shiftPatternTable = $("#shift-pattern-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/master-data/grup/shift-pattern-datatable",
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

        columns: columnsSpTable,
        columnDefs: [
            {
                orderable: false,
                targets: [0, -1],
            },
        ],
    })

    //REFRESH TABLE
    function refreshTableSp() {
        shiftPatternTable.search("").draw();
    }

    //OPEN MODAL TAMBAH GRUP
    $('.btnAddSp').on("click", function (){
        openShiftPattern();
    })

    //CLOSE MODAL TAMBAH GRUP
    $('.btnCloseSp').on("click", function (){
        closeShiftPattern();
    })

    function resetSp(){
        $('#list-urutan').empty();
        $('#nama_shift_pattern').val('');
        count = 0;
    }

    let count = 0;
    $('.btnAddUrutan').on("click", function (){
        count++;
        let tbody = $('#list-urutan');
        tbody.append(`
            <div class="form-group d-flex align-items-center">
            <select class="form-control mr-2" name="urutan[]" id="id_urutan_${count}" required>
                <option value="">Pilih Shift</option>
            </select>
            <button type="button" class="btn btn-danger m-2 btnRemoveUrutan"><i class="fas fa-times"></i></button>
            </div>
        `);

        // Add event listener to remove button
        $('.btnRemoveUrutan').last().on('click', function () {
            $(this).closest('.form-group').remove();
            count--;
        });

        $("#id_urutan_" + count).select2({
            dropdownParent: $('#modal-input-shift-pattern'),
            ajax: {
            url: base_url + "/master-data/grup/get-data-grup",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='urutan[]']").each(function () {
                if ($(this).val()) {
                    selectedIds.push($(this).val());
                }
                });

                let filteredData = data.results.filter(function (item) {
                return !selectedIds.includes(item.id);
                });

                return {
                    results: filteredData,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })
    
    //DELETE GRUP
    $('#shift-pattern-table').on('click', '.btnDeleteSp', function (){
        var idGrupPattern = $(this).data('id');
        Swal.fire({
            title: "Delete Shift Pattern",
            text: "Apakah kamu yakin untuk menghapus grup pattern ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                loadingSwalShow();
                var url = base_url + '/master-data/grup/delete-shift-pattern/' + idGrupPattern;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "delete",
                    },
                    dataType: "JSON",
                    success: function (data) {
                        loadingSwalClose();
                        refreshTableSp();
                        showToast({ title: data.message });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            }
        });
    })

    $('.btnCloseEditSp').on("click", function (){
        resetEditSp();
        closeEditShiftPattern();
    })

    function resetEditSp(){
        $('#list-urutanEdit').empty();
        $('#nama_shift_patternEdit').val('');
        $('#id_shift_patternEdit').val('');
        urutanCount = 0;
    }

     var modalEditShiftPatternOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEditShiftPattern = new bootstrap.Modal(
        document.getElementById("modal-edit-shift-pattern"),
        modalEditShiftPatternOptions
    );

    function openEditShiftPattern() {
        modalEditShiftPattern.show();
    }

    function closeEditShiftPattern() {
        resetSp();
        modalEditShiftPattern.hide();
    }

    let urutanCount = 0;
    $('#shift-pattern-table').on("click", '.btnEditSp' , function () {
        loadingSwalShow();
        let idGrupPattern = $(this).data('id');
        let url = base_url + '/master-data/grup/get-data-grup-pattern/' + idGrupPattern;
        $('#id_shift_patternEdit').val(idGrupPattern);
        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let urutan = response.data.urutan;
                let nama = response.data.nama;
                urutanCount += urutan.length;
                $('#nama_shift_patternEdit').val(nama);
                $('#id_shift_patternEdit').val(idGrupPattern);
                let list = $('#list-urutanEdit').empty();
                
                $.each(urutan, function (i, val){
                    list.append(`
                        <div class="form-group d-flex align-items-center">
                            <select class="form-control mr-2" name="urutanEdit[]" id="id_urutanEdit_${i + 1}" required>
                                <option value="">Pilih Shift</option>
                            </select>
                            <button type="button" class="btn btn-danger m-2 btnRemoveUrutanEdit"><i class="fas fa-times"></i></button>
                        </div>
                    `)

                    $('.btnRemoveUrutanEdit').last().on('click', function () {
                        $(this).closest('.form-group').remove();
                    });

                    selectedGrupPattern(i + 1, val);
                });
                loadingSwalClose();
                openEditShiftPattern();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
    })

    $('.btnAddUrutanEdit').on("click", function (){
        urutanCount++;
        let tbody = $('#list-urutanEdit');
        tbody.append(`
            <div class="form-group d-flex align-items-center">
            <select class="form-control mr-2" name="urutanEdit[]" id="id_urutanEdit_${urutanCount}" required>
                <option value="">Pilih Shift</option>
            </select>
            <button type="button" class="btn btn-danger m-2 btnRemoveUrutanEdit"><i class="fas fa-times"></i></button>
            </div>
        `);

        $('.btnRemoveUrutanEdit').last().on('click', function () {
            $(this).closest('.form-group').remove();
        });

        $("#id_urutanEdit_" + urutanCount).select2({
            dropdownParent: $('#modal-edit-shift-pattern'),
            ajax: {
            url: base_url + "/master-data/grup/get-data-grup",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='urutanEdit[]']").each(function () {
                if ($(this).val()) {
                    selectedIds.push($(this).val());
                }
                });

                let filteredData = data.results.filter(function (item) {
                return !selectedIds.includes(item.id);
                });

                return {
                    results: filteredData,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })

    function selectedGrupPattern(index, grupId){
        $.ajax({
            url: base_url + "/master-data/grup/get-data-all-grup",
            method: 'GET',
            dataType: 'JSON',
            success: function (response) {
                let grup_ids = response;
                let select = $('#id_urutanEdit_'+index);
                $.each(grup_ids, function (i, val){
                    select.append('<option value="'+val.id+'">'+val.text+'</option>');
                });
                $("#id_urutanEdit_" + index).val(grupId).trigger('change');
                $("#id_urutanEdit_" + index).select2({
                    dropdownParent: $('#modal-edit-shift-pattern'),
                    ajax: {
                    url: base_url + "/master-data/grup/get-data-grup",
                    type: "post",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || "",
                            page: params.page || 1,
                        };
                    },
                    processResults: function (data, params) {
                        let selectedIds = [];
                        $("select[name='urutanEdit[]']").each(function () {
                        if ($(this).val()) {
                            selectedIds.push($(this).val());
                        }
                        });
        
                        let filteredData = data.results.filter(function (item) {
                        return !selectedIds.includes(item.id);
                        });
        
                        return {
                            results: filteredData,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true,
                    },
                });
            }
        })
    }

    $('#form-edit-shift-pattern').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let idGrupPattern = $('#id_shift_patternEdit').val();
        let url = base_url + '/master-data/grup/update-shift-pattern/' + idGrupPattern;

        var formData = new FormData($('#form-edit-shift-pattern')[0]);
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
                refreshTableSp();
                closeEditShiftPattern();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });
});