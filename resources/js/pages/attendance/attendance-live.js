$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    //DATATABLE
    var columnsTable = [
        { data: "karyawan" },
        { data: "departemen" },
        { data: "scan_date" },
    ];

    var liveTable = $("#data-table").DataTable({
        search: {
            return: true,
        },
        order: [[2, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/live-attendance/datatable",
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
                        window.location.reload();
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
                        window.location.reload();
                    });
                }
            },
        },
        scrollX: true,
        pageResize: true,
        autoWidth: true,
        columns: columnsTable,
    })

    function refreshTable() {
        var searchValue = liveTable.search();
        if (searchValue) {
            liveTable.search(searchValue).draw();
        } else {
            liveTable.search("").draw();
        }
    }

    // GRAFIK
    var liveAttendanceChart;
    getLiveAttendanceChart();
    function getLiveAttendanceChart() {
        let url = base_url + '/get-live-attendance-chart';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                let data = response.data;

                const totalKaryawan = data.map((item) => item.total_karyawan);
                const karyawanHadir = data.map((item) => item.karyawan_hadir);   
                const departemen = data.map((item) => item.departemen);

                var options = {
                    series: [{
                    name: 'Total Karyawan',
                    data: totalKaryawan
                  }, {
                    name: 'Total Hadir',
                    data: karyawanHadir
                  }],
                    chart: {
                    type: 'bar',
                    height: 'auto',
                  },
                  plotOptions: {
                    bar: {
                      horizontal: false,
                      columnWidth: '55%',
                      borderRadius: 5,
                      borderRadiusApplication: 'end'
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  title: {
                    text: 'Department Attendance Chart',
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                      fontSize:  '20px',
                      fontWeight:  'bold',
                      fontFamily:  undefined,
                      color:  '#263238'
                    },
                 },
                  xaxis: {
                    categories: departemen,
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function (val) {
                        return val + " Karyawan";
                      }
                    }
                  }
                  };
            
                  liveAttendanceChart = new ApexCharts(document.querySelector("#chart"), options);
                  liveAttendanceChart.render();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: "error",
                    title: " <br>Application error!",
                    message: jqXHR.responseJSON.message,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                }).then(function () {
                    refreshTable();
                    window.location.reload();
                });
            }
        });
    }

    function updateLiveAttendanceChart() {
        let url = base_url + '/get-live-attendance-chart';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                let data = response.data;

                const totalKaryawan = data.map((item) => item.total_karyawan);
                const karyawanHadir = data.map((item) => item.karyawan_hadir);   
                const departemen = data.map((item) => item.departemen);

                liveAttendanceChart.updateOptions({
                    series: [{
                        name: 'Total Karyawan',
                        data: totalKaryawan
                      }, {
                        name: 'Total Hadir',
                        data: karyawanHadir
                      }],
                    xaxis: {
                        categories: departemen,
                    }
                });
            },
            error: function(error) {
                Swal.fire({
                    icon: "error",
                    title: " <br>Application error!",
                    message: jqXHR.responseJSON.message,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                }).then(function () {
                    refreshTable();
                    window.location.reload();
                });
            }
        });
    }

    const organisasiId = authUser.organisasi_id;
    window.Echo.private(`live-attendance.${organisasiId}`)
    .listen('LiveAttendanceEvent', (e) => {
        updateLiveAttendanceChart();
        refreshTable();
    });

    setInterval(function() {
        window.location.reload();
    }, 3600000);
});