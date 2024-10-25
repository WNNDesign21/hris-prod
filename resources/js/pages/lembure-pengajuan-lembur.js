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
        { data: "id_lembur" },
        { data: "issued_date" },
        { data: "issued_by" },
        { data: "total_durasi"},
        { data: "status"},
        { data: "plan_checked_by"},
        { data: "plan_approved_by"},
        { data: "plan_legalized_by"},
        { data: "actual_checked_by"},
        { data: "actual_approved_by"},
        { data: "actual_legalized_by"},
        { data: "aksi"},
    ];

    var lembureTable = $("#lembur-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/pengajuan-lembur-datatable",
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
                targets: [0,-1],
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
        lembureTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        openForm();
    })

    $('.btnClose').on("click", function (){
        closeForm();
    })

    // MODAL TAMBAH KARYAWAN
    var modalPengajuanLemburOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalPengajuanLembur = new bootstrap.Modal(
        document.getElementById("modal-pengajuan-lembur"),
        modalPengajuanLemburOptions
    );

    function openForm() {
        modalPengajuanLembur.show();
    }

    function closeForm() {
        modalPengajuanLembur.hide();
    }

    //SURAT PERINTAH LEMBUR
    let count = 0;
    let jumlah_detail_lembur = 0;
    $('#table-detail-lembur').on("click",'.btnDeleteDetailLembur', function (){
        jumlah_detail_lembur--;
        let urutan = $(this).data('urutan');
        $(`#btn_delete_detail_lembur_${urutan}`).closest('tr').remove();

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }
    });

    $('.btnAddDetailLembur').on("click", function (){
        count++;
        jumlah_detail_lembur++;
        let tbody = $('#list-detail-lembur');
        tbody.append(`
             <tr>
                <td>
                    <select name="karyawan_id[]" id="karyawan_id_${count}" class="form-control" style="width: 100%;">
                    </select>
                </td>
                <td>
                    <input type="text" name="job_description[]"
                        id="job_description_${count}" class="form-control"
                        style="width: 100%;">
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_mulai_lembur[]"
                        id="rencana_mulai_lembur_${count}" class="form-control"
                        style="width: 100%;">
                    </input>
                </td>
                <td>
                    <input type="datetime-local" name="rencana_selesai_lembur[]"
                        id="rencana_selesai_lembur_${count}" class="form-control"
                        style="width: 100%;">
                    </input>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button"
                            class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${count}" id="btn_delete_detail_lembur_${count}"><i
                                class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `)

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }

        $("#karyawan_id_" + count).select2({
            dropdownParent: $('#modal-pengajuan-lembur'),
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
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='karyawan_id[]']").each(function () {
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
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })
});