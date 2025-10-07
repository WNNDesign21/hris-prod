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

    // DATATABLE
    var columnsTable = [
        { data: "lembur_id" },
        { data: "nama" },
        { data: "posisi" },
        { data: "departemen" },
        { data: "mulai" },
        { data: "selesai" },
        { data: "durasi" },
        { data: "nominal" },
    ];

    var detailLemburTable = $("#detail-lembur-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "ASC"]],
        processing: true,
        serverSide: true,
        lengthChange: false,
        pageLength: 18,
        ajax: {
            url: base_url + "/lembure/detail-lembur-datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                let departemen = $('#filterDepartemen').val();
                let periode = $('#filterPeriode').val();

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
            // {
            //     orderable: false,
            //     targets: [3,4],
            // },
            {
                targets: [-1],
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).addClass("text-center");
                },
            },
        ],
    })

    function refreshTable() {
        detailLemburTable.search("").draw();
    }

    $('.btnReload').on("click", function (){
        resetFilter();
        getLeaderboardUserMonthly();
        refreshTable();
    })

    $('.btnFilter').on('click', function(){
        openFilter();
    })

    var options = {
        series: [{
          data: []
        }],
        chart: {
            type: 'bar',
            height: '100%',
            events: {
                dataPointSelection: (event, chartContext, opts) => {
                    if(categories.length > 0){
                        detailLemburTable.search(categories[opts.dataPointIndex]).draw();
                    } else {
                        detailLemburTable.search("").draw();
                    }
                }
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
            barHeight: '100%',
            distributed: true,
            horizontal: true,
            dataLabels: {
                position: 'bottom'
            },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
            colors: ['#fff']
            },
            formatter: function (val, opt) {
            //   return opt.w.globals.labels[opt.dataPointIndex] + ":  " + 'Rp ' + val.toLocaleString('id-ID')
                //  return 'Rp ' + val.toLocaleString('id-ID')
            },
            offsetX: 0,
            dropShadow: {
            enabled: true
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            categories: [],
            labels: {
                formatter: function (val) {
                    return 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'K';
                }
            }
        },
        yaxis: {
            labels: {
            show: true
            }
        },
        // title: {
        //     text: 'Leaderboard Lembur '+ ' (' + new Date().toLocaleString('default', { month: 'long' }) + ' ' + new Date().getFullYear() + ')',
        //     align: 'center',
        //     floating: true
        // },
        tooltip: {
            theme: 'light',
            x: {
            show: false
            },
            y: {
            title: {
                formatter: function (val, opt) {
                    return 'Total Nominal Lembur';
                },
            },
                formatter: function (val, opt) {
                    return 'Rp ' + val.toLocaleString('id-ID')
                }
            },
        }
      };

    var chartLeaderboardUserMonthly = new ApexCharts(document.querySelector("#chartLeaderboardUserMonthly"), options);
    chartLeaderboardUserMonthly.render();

    let categories = [];

    loadingSwalShow();
    getLeaderboardUserMonthly();
    function getLeaderboardUserMonthly() {
        let url = base_url + '/lembure/detail-lembur/get-leaderboard-user-monthly';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                limit: $('#filterData').val(),
                departemen: $('#filterDepartemen').val(),
                periode: $('#filterPeriode').val()
            },
            success: function(response) {
                let data = response.data;
                var series = [];
                var hours = [];
                categories = [];

                data.forEach(function(val, index) {
                    const karyawan = val.nama;
                    categories.push(karyawan);
                    series.push(val.total_nominal_lembur);
                    hours.push(val.total_jam_lembur / 60);
                });

                let limit = $('#filterData').val();
                let departemen = $('#filterDepartemen').val();
                let periode = $('#filterPeriode').val();

                const currentDate = new Date();
                const currentMonth = currentDate.toLocaleString('default', { month: 'long' });
                const currentYear = currentDate.getFullYear();
                let textPeriode = `${currentMonth.toUpperCase()} ${currentYear}`;
                let textDepartemen = 'SEMUA DEPARTEMEN';
                let textLimit = 'TOP 50';

                if (limit) {
                    textLimit = 'TOP '+limit;
                }

                if (periode) {
                    const date = new Date(periode + '-01');
                    const month = date.toLocaleString('default', { month: 'long' });
                    const year = date.getFullYear();
                    textPeriode = `${month.toUpperCase()} ${year}`;
                }


                if (departemen) {
                    textDepartemen = $('#filterDepartemen').select2('data')[0].text
                }

                $('#filterStatus').text(textLimit+' - '+textDepartemen+' - '+textPeriode);

                chartLeaderboardUserMonthly.updateOptions({
                    series: [{
                      data: series
                    }],
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: function (val) {
                                return 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'K';
                            }
                        }
                    },
                })
                loadingSwalClose();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    $('.btnCloseFilter').on("click", function (){
        closeFilter();
    });

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

    function resetFilter() {
        $('#filterData').val(50).trigger('change');
        $('#filterDepartemen').val('').trigger('change');
        $('#filterPeriode').val('');
    }

    $('.btnResetFilter').on('click', function(){
        resetFilter();
    })

    $('#filterData').select2({
        dropdownParent: $('#modal-filter')
    });

    $('#filterDepartemen').select2({
        dropdownParent: $('#modal-filter')
    });

    $(".btnSubmitFilter").on("click", function () {
        loadingSwalShow();
        detailLemburTable.search('').draw();
        getLeaderboardUserMonthly();
        closeFilter();
    });
});