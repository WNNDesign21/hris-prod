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
    getJenisCutiKhusus();
    let jenisCutiKhusus;
    function getJenisCutiKhusus() {
        loadingSwalShow();
        $.ajax({
            url: base_url + '/cutie/get-data-jenis-cuti-khusus',
            type: "get",
            success: function (response) {
                var data = response.data;
                jenisCutiKhusus = data;
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    };
    
    //DATATABLE KARYAWAN
    var columnsTable = [
        { data: "no" },
        { data: "rencana_mulai_cuti" },
        { data: "rencana_selesai_cuti" },
        { data: "aktual_mulai_cuti" },
        { data: "aktual_selesai_cuti" },
        { data: "durasi" },
        { data: "jenis" },
        { data: "alasan" },
        { data: "karyawan_pengganti" },
        { data: "checked" },
        { data: "approved" },
        { data: "legalized" },
        { data: "status_dokumen" },
        { data: "status" },
        { data: "attachment" },
        { data: "aksi" },
    ];

    var cutieTable = $("#cutie-table").DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/cutie/pengajuan-cuti-datatable",
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
        responsive: false,
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

    //SELECT2 & CONDITIONAL FIELD
    $('#jenis_cuti').select2();
    $('#jenis_cuti').on('change', function() {
        let jenisCuti = $(this).val();
        if (jenisCuti == 'KHUSUS') {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            conditionalField.append('<label for="jenis_cuti_khusus">Jenis Cuti Khusus</label>');
            var selectField = $('<select style="width:100%;"></select>').attr('id', 'jenis_cuti_khusus').attr('name', 'jenis_cuti_khusus');
            $.each(jenisCutiKhusus, function (i, val){
                selectField.append('<option value="'+val.id+'">'+val.text+'</option>');
            });
            conditionalField.append(selectField);
            $('#jenis_cuti_khusus').select2();
        } else if (jenisCuti == 'SAKIT') {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
            conditionalField.append('<label for="alasan_sakit">Bukti Surat Dokter</label>');
            var inputField = $('<input type="file" class="form-control">').attr('id', 'attachment').attr('name', 'attachment');
            conditionalField.append(inputField);
        } else {
            var conditionalField = $('#conditional_field');
            conditionalField.empty();
        }
    });
});