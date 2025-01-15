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

    //DATATABLE
    var columnsTable = [
        { data: "ni_karyawan" },
        { data: "karyawan" },
        { data: "departemen" },
        { data: "pin" },
        { data: "in_1" },
        { data: "out_1" },
        { data: "in_2" },
        { data: "out_2" },
        { data: "in_3" },
        { data: "out_3" },
        { data: "in_4" },
        { data: "out_4" },
        { data: "in_5" },
        { data: "out_5" },
        { data: "in_6" },
        { data: "out_6" },
        { data: "in_7" },
        { data: "out_7" },
        { data: "in_8" },
        { data: "out_8" },
        { data: "in_9" },
        { data: "out_9" },
        { data: "in_10" },
        { data: "out_10" },
        { data: "in_11" },
        { data: "out_11" },
        { data: "in_12" },
        { data: "out_12" },
        { data: "in_13" },
        { data: "out_13" },
        { data: "in_14" },
        { data: "out_14" },
        { data: "in_15" },
        { data: "out_15" },
        { data: "in_16" },
        { data: "out_16" },
        { data: "in_17" },
        { data: "out_17" },
        { data: "in_18" },
        { data: "out_18" },
        { data: "in_19" },
        { data: "out_19" },
        { data: "in_20" },
        { data: "out_20" },
        { data: "in_21" },
        { data: "out_21" },
        { data: "in_22" },
        { data: "out_22" },
        { data: "in_23" },
        { data: "out_23" },
        { data: "in_24" },
        { data: "out_24" },
        { data: "in_25" },
        { data: "out_25" },
        { data: "in_26" },
        { data: "out_26" },
        { data: "in_27" },
        { data: "out_27" },
        { data: "in_28" },
        { data: "out_28" },
        { data: "in_29" },
        { data: "out_29" },
        { data: "in_30" },
        { data: "out_30" },
        { data: "in_31" },
        { data: "out_31" },
        { data: "total_in_selisih" }
    ];

    var presensiTable = $("#presensi-table").DataTable({
        search: {
            return: true,
        },
        fixedColumns: {
            leftColumns: 4,
        },
        paging: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/attendance/presensi/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var departemen = $('#filterDepartemen').val();
                var periode = $('#filterPeriode').val();

                dataFilter.departemen = departemen;
                dataFilter.periode = periode;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
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
        scrollX: true,
        scrollY: 600,
        fixedHeader: true,
        columns: columnsTable,
        createdRow: function( row, data, dataIndex ) {
            $('td', row).each(function(index) {
                if ($(this).text() === '') {
                    $(this).addClass('bg-danger');
                }

                // if (data.in_status_6 === 'LATE' && index === 14) {
                //     $(this).addClass('bg-warning');
                // }
                for (let i = 1; i <= 31; i++) {
                    if (data[`in_status_${i}`] === 'LATE' && index === (i * 2 + 2)) {
                        $(this).addClass('bg-warning');
                    }

                    if (data[`out_status_${i}`] === 'EARLY' && index === (i * 2 + 3)) {
                        $(this).addClass('bg-dark');
                    }

                    if (data[`out_status_${i}`] === 'OVERTIME' && index === (i * 2 + 3)) {
                        $(this).addClass('bg-info');
                    }
                }
            });
        }
    })

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = presensiTable.search();
        if (searchValue) {
            presensiTable.search(searchValue).draw();
        } else {
            presensiTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    // MONTHLY
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

    $('.btnFilter').on('click', function() {
        openFilter();
    });

    $('.closeFilter').on('click', function() {
        closeFilter();
    });

    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter')
    });

    $('.btnResetFilter').on('click', function() {
        $('#filterDepartemen').val('').trigger('change');
        $('#filterPeriode').val('');
    });
    
    $(".btnSubmitFilter").on("click", function () {
        presensiTable.draw();
        closeFilter();
    });

});