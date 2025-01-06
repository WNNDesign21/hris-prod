$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    // ALERT & LOADING
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
            timer: 3000, 
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

    var columnsTable = [
        { data: "departemen" },
        { data: "created_at" },
        { data: "periode" },
        { data: "status" },
        { data: "message" },
        { data: "attachment" },
    ];

    var exportSlipLemburTable = $("#export-slip-lembur-table")
    .DataTable({
        search: {
            return: true,
        },
        order: [[1, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/lembure/export-report-lembur/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let departemen = $('#filterDepartemen').val();
                let status = $('#filterStatus').val();
                let periode = $('#filterPeriode').val();

                dataFilter.status = status;
                dataFilter.departemen = departemen;
                dataFilter.periode = periode;
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
                targets: [-1],
            },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    $("export-slip-lembur-table input").off();
    $("export-slip-lembur-table input").on("keyup", function (e) {
        if (e.code == "Enter") {
            exportSlipLemburTable.search(this.value).draw();
            intervalOff()
        }
    });

    var mySetInterval;
    function intervalOn() {
        mySetInterval = setInterval(function () {
            exportSlipLemburTable.ajax.reload(null, false);
        }, 30000);
    }

    function intervalOff() {
        clearInterval(mySetInterval);
    }

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = exportSlipLemburTable.search();
        if (searchValue) {
            exportSlipLemburTable.search(searchValue).draw();
        } else {
            exportSlipLemburTable.search("").draw();
        }
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('#form-export-slip-lembur').on('submit', function (e) {
        e.preventDefault();
        loadingSwalShow();
        let formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
                refreshTable();
                intervalOn();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });

    $('.btnFilter').on("click", function (){
        openFilter();
    })

    $('.btnClose').on("click", function (){
        closeFilter();
    })

    $('.btnResetFilter').on('click', function(){
        $('#filterPeriode').val('');
        $('#filterDepartemen').val('');
        $('#filterStatus').val('');
        exportSlipLemburTable.draw();
        intervalOn();
    })

    $(".btnSubmitFilter").on("click", function () {
        exportSlipLemburTable.draw();
        closeFilter();
        intervalOff();
    });

     // MODAL REJECT
     var modalFilterOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilter = new bootstrap.Modal(
        document.getElementById("modal-filter"),
        modalFilterOptions
    );

    function openFilter() {
        modalFilter.show();
    }

    function closeFilter() {
        modalFilter.hide();
    }

    $('#departemen_slip').select2();
    $('#filterStatus').select2({
        dropdownParent: $('#modal-filter')
    });
    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter')
    });
});