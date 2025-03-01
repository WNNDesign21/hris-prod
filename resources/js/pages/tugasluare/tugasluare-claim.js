$(function () {
    // GLOBAL VARIABLES
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    let loadingSwal;
    let detailCount = 0;
    // END GLOBAL VARIABLES

    // LOADING & ALERT
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
    var columnsTable = [
        { data: "id_claim" },
        { data: "karyawan" },
        { data: "no_polisi" },
        { data: "total_claim" },
        { data: "status" },
        { data: "aksi" },
    ];

    var claimTable = $("#claim-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/tugasluare/claim/datatable",
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
        var searchValue = claimTable.search();
        if (searchValue) {
            claimTable.search(searchValue).draw();
        } else {
            claimTable.search("").draw();
        }
    }

    function open() {
        modalInput.show();
    }

    function close() {
        reset();
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
        $('#list-detailEdit').empty();
        $('#id_millageEdit').val('');
        detailCount = 0;
    }

    function reset(){
        $('#list-detail').empty();
        count = 0;
    }

    // function selectedPengikut(index, pengikutId){
    //     $.ajax({
    //         url: base_url + "/ajax/tugasluare/claim/select-get-data-all-karyawan",
    //         method: 'GET',
    //         dataType: 'JSON',
    //         success: function (response) {
    //             let pengikutIds = response;
    //             let select = $('#id_pengikutEdit_'+index);
    //             $.each(pengikutIds, function (i, val){
    //                 select.append('<option value="'+val.id+'">'+val.text+'</option>');
    //             });
    //             console.log(index, pengikutId)
    //             $("#id_pengikutEdit_" + index).val(pengikutId).trigger('change');
    //             $("#id_pengikutEdit_" + index).select2({
    //                 dropdownParent: $('#modal-edit'),
    //                 ajax: {
    //                 url: base_url + "/ajax/tugasluare/claim/select-get-data-karyawan",
    //                 type: "post",
    //                 dataType: "json",
    //                 delay: 250,
    //                 data: function (params) {
    //                     let selectedIds = [];
    //                     $("select[name='id_pengikutEdit[]']").each(function () {
    //                         let val = $(this).val();
    //                         if (val) {
    //                             selectedIds.push(val);
    //                         }
    //                     });
    //                     return {
    //                         search: params.term || "",
    //                         page: params.page || 1,
    //                         selectedIds: selectedIds
    //                     };
    //                 },
    //                 processResults: function (data, params) {
    //                     return {
    //                         results: data.results,
    //                         pagination: {
    //                             more: data.pagination.more
    //                         }
    //                     };
    //                 },
    //                 cache: true,
    //                 },
    //             });
    //         }
    //     })
    // }
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

    $('#claim-table').on("click", '.btnEdit', function (){
        loadingSwalShow();
        let millageId = $(this).data('id-millage');
        let url = base_url + '/ajax/tugasluare/claim/get-data-detail-millage/' + millageId;

        $.ajax({
            url: url,
            method: "GET",
            dataType: "JSON",
            success: function (response) {
                let pengikut = response.data;
                detailCount += pengikut.length;
                $('#pengemudiEdit').val(pengemudi).trigger('change');
                let list = $('#list-detailEdit').empty();
                
                $.each(pengikut, function (i, val){
                    list.append(`
                        <div class="form-group d-flex align-items-center">
                            <select class="form-control mr-2" name="id_pengikutEdit[]" id="id_pengikutEdit_${i + 1}" required>
                                <option value="">Pilih Karyawan</option>
                            </select>
                            <button type="button" class="btn btn-danger m-2 btnRemovePengikutEdit"><i class="fas fa-times"></i></button>
                        </div>
                    `)

                    $('.btnRemovePengikutEdit').last().on('click', function () {
                        $(this).closest('.form-group').remove();
                    });

                    selectedPengikut(i + 1, val.karyawan_id);
                });
                loadingSwalClose();
                openEdit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        }); 
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
        let idMillage = $('#id_millageEdit').val();
        let url = base_url + '/tugasluare/claim/update/' + idMillage;
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

    $('#claim-table').on('click', '.btnDelete', function (){
        var idMillage = $(this).data('id-millage');
        Swal.fire({
            title: "Delete ",
            text: "Apakah kamu yakin untuk menghapus claim ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                let url = base_url + '/tugasluare/claim/delete/' + idMillage;
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