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
        { data: "total_in_selisih" },
        { data: "total_kehadiran" }
    ];

    var presensiTable = $("#presensi-table").DataTable({
        search: {
            return: true,
        },
        fixedColumns: {
            leftColumns: 2,
        },
        // paging: false,
        processing: true,
        language: {
            processing: '<i class="fas fa-sync-alt fa-spin fs-80"></i>'
        },
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
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel'
        ],
        createdRow: function( row, data, dataIndex ) {
            $('td', row).each(function(index) {
                if ($(this).text() === 'Check') {
                    $(this).addClass('bg-danger');
                }

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

    // PRESENSI
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

    // SUMMARY
    var modalFilterSummaryOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalFilterSummary = new bootstrap.Modal(
        document.getElementById("modal-filter-summary"),
        modalFilterSummaryOptions
    );

    function openFilterSummary() {
        modalFilterSummary.show();
    }

    function closeFilterSummary() {
        modalFilterSummary.hide();
    }

    $('.btnFilterSummary').on('click', function() {
        openFilterSummary();
    });

    $('.closeFilterSummary').on('click', function() {
        closeFilterSummary();
    });

    $('#filterDepartemenSummary').select2({
        dropdownParent: $('#modal-filter-summary')
    });

    $('.btnResetFilterSummary').on('click', function() {
        $('#filterDepartemenSummary').val('').trigger('change');
        $('#filterTanggalSummary').val('');
    });
    
    $(".btnSubmitFilterSummary").on("click", function () {
        loadingSwalShow();
        var departemen = $('#filterDepartemenSummary').val();
        var tanggal = $('#filterTanggalSummary').val();

        $.ajax({
            url: base_url + "/attendance/presensi/get-summary-presensi",
            type: "POST",
            data: {
                departemen: departemen,
                tanggal: tanggal
            },
            success: function (response) {
                $('#summaryContent').empty().html(response.data);
                if (tanggal) {
                    $('.summaryText').text(tanggal);
                } else {
                    $('.summaryText').text(new Date().toLocaleDateString());
                }

                $('.btnDetailSummary').on('click', function() {
                    loadingSwalShow();
                    let type = $(this).data('type');
            
                    if(type == '2') {
                        $('.detailText').text('Detail Sakit');
                    } else if(type == '3') {
                        $('.detailText').text('Detail Izin');
                    } else if(type == '4') {
                        $('.detailText').text('Detail Cuti');
                    } else {
                        $('.detailText').text('Detail Hadir');
                    }
            
                    $.ajax({
                        url: base_url + "/attendance/presensi/get-detail-presensi",
                        type: "POST",
                        data: {
                            departemen: $('#filterDepartemenSummary').val(),
                            tanggal: $('#filterTanggalSummary').val(),
                            type: type
                        },
                        success: function (response) {
                            let data = response.data;
                            $('#detailContent').empty();
                            if (data.length > 0) {
                                data.forEach(item => {
                                    $('#detailContent').append(`
                                        <tr>
                                         <td>${item.nama || item.nama_karyawan}</td>
                                         <td>${item.departemen}</td>
                                        </tr>
                                    `);
                                });
                            }
                            $('#detail-table').DataTable({
                                order: [[1, 'asc']]
                            });
                            loadingSwalClose();
                            openDetailSummary();
                        },
                        error: function (jqXHR) {
                            showToast({ icon: "error", title: jqXHR.responseJSON.message });
                        }
                    })
                });
            
                $('.btnCloseDetailSummary').on('click', function() {
                    closeDetailSummary();
                    $('#detail-table').DataTable().destroy();
                });

                closeFilterSummary();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                closeFilterSummary();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    });


    //DETAIL
    var modalDetailSummaryOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDetailSummary = new bootstrap.Modal(
        document.getElementById("modal-detail-summary"),
        modalDetailSummaryOptions
    );

    function openDetailSummary() {
        modalDetailSummary.show();
    }

    function closeDetailSummary() {
        modalDetailSummary.hide();
    }

    $('.btnDetailSummary').on('click', function() {
        loadingSwalShow();
        let type = $(this).data('type');

        if(type == '2') {
            $('.detailText').text('Detail Sakit');
        } else if(type == '3') {
            $('.detailText').text('Detail Izin');
        } else if(type == '4') {
            $('.detailText').text('Detail Cuti');
        } else {
            $('.detailText').text('Detail Hadir');
        }

        $.ajax({
            url: base_url + "/attendance/presensi/get-detail-presensi",
            type: "POST",
            data: {
                departemen: $('#filterDepartemenSummary').val(),
                tanggal: $('#filterTanggalSummary').val(),
                type: type
            },
            success: function (response) {
                let data = response.data;
                $('#detailContent').empty();
                if (data.length > 0) {
                    data.forEach(item => {
                        $('#detailContent').append(`
                            <tr>
                             <td>${item.nama || item.nama_karyawan}</td>
                             <td>${item.departemen}</td>
                            </tr>
                        `);
                    });
                }
                $('#detail-table').DataTable({
                    order: [[1, 'asc']]
                });
                loadingSwalClose();
                openDetailSummary();
            },
            error: function (jqXHR) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    });

    $('.btnCloseDetailSummary').on('click', function() {
        closeDetailSummary();
        $('#detail-table').DataTable().destroy();
    });

    var modalCheckOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalCheck = new bootstrap.Modal(
        document.getElementById("modal-check"),
        modalCheckOptions
    );

    function openCheck() {
        modalCheck.show();
    }

    function closeCheck() {
        modalCheck.hide();
    }

    $('.btnCloseCheck').on('click', function() {
        closeCheck();
        $('#check-table').DataTable().destroy();
    });

    $('#presensi-table').on('click', '.btnCheck', function() {
        let karyawanId = $(this).data('karyawan-id');
        let tanggal = $(this).data('date');
        let pin = $(this).data('pin');

        loadingSwalShow();
        $.ajax({
            url: base_url + "/attendance/presensi/check-presensi",
            type: "POST",
            data: {
                karyawan_id: karyawanId,
                date: tanggal,
                pin: pin
            },
            success: function (response) {
                let data = response.data.data;
                let jenis = response.data.jenis;

                if(jenis = 'scanlog'){
                    $('#checkContent').empty();
                    if (data.length > 0) {
                        data.forEach(item => {
                            $('#checkContent').append(`
                                <tr>
                                    <td>${item.scan_date}</td>
                                    <td><span class="badge badge-success">Scanlog</span></td>
                                </tr>
                            `);
                        });
                    }
                    $('#check-table').DataTable({
                        order: [[0, 'asc']]
                    });
                } else if (jenis = 'cuti') {
                    $('#checkContent').empty();
                    if (data.length > 0) {
                        data.forEach(item => {
                            $('#checkContent').append(`
                                <tr>
                                    <td>${item.rencana_mulai_cuti} - ${item.rencana_selesai_cuti}</td>
                                    <td><span class="badge badge-success">Cuti</span></td>
                                </tr>
                            `);
                        });
                    }
                    $('#check-table').DataTable({
                        order: [[0, 'asc']]
                    });
                } else if (jenis = 'izin') {
                    $('#checkContent').empty();
                    if (data.length > 0) {
                        data.forEach(item => {
                            $('#checkContent').append(`
                                <tr>
                                    <td>${item.rencana_mulai_or_masuk} - ${item.rencana_selesai_or_keluar}</td>
                                    <td><span class="badge badge-info">Izin</span></td>
                                </tr>
                            `);
                        });
                    }
                    $('#check-table').DataTable({
                        order: [[0, 'asc']]
                    });
                } else if (jenis = 'sakit') {
                    $('#checkContent').empty();
                    if (data.length > 0) {
                        data.forEach(item => {
                            $('#checkContent').append(`
                                <tr>
                                    <td>${item.tanggal_mulai} - ${item.tanggal_selesai}</td>
                                    <td><span class="badge badge-danger">Sakit</span></td>
                                </tr>
                            `);
                        });
                    }
                    $('#check-table').DataTable({
                        order: [[0, 'asc']]
                    });
                } else {
                    $('#check-table').DataTable();
                }
                openCheck();
                loadingSwalClose();
            },
            error: function (jqXHR) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    });

});