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

    getMonthlyLemburPerDepartemen();
    function getMonthlyLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-monthly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                let data = response.data;
                var series = [];
                const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#fdc500', '#6c757d'];
                let colorIndex = 0;

                for (const val of data) {
                    const departmentName = val.departemen;
                    const monthlyData = [val.januari, val.februari, val.maret, val.april, val.mei, val.juni, val.juli, val.agustus, val.september, val.oktober, val.november, val.desember];   
                    const departmentSeries = {
                        name: departmentName,
                        data: monthlyData,
                        color: colors[colorIndex % colors.length], 
                    };
                    series.push(departmentSeries);
                    colorIndex++;
                }

                var options = {
                    series: series,
                    chart: {
                        height: '100%',
                        type: 'area',
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                        export: {
                            svg: {
                                filename: undefined,
                            },
                            png: {
                                filename: undefined,
                            }
                        },
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            legend: {
                                position: 'bottom',
                                offsetX: -10,
                                offsetY: 0
                            }
                        }
                    }],
                    grid: {
                        show: true,
                        borderColor: '#f7f7f7',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '30%',
                            endingShape: 'rounded',
                            colors: {
                                backgroundBarColors: ['#f0f0f0'],
                                backgroundBarOpacity: 0,
                            },
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'K';
                            }
                        }
                    },
                    legend: {
                        show: true,
                    },
                    markers: {
                        size: 0,
                    },
                    title: {
                        text: 'Monitoring Lembur '+ new Date().getFullYear(),
                        align: 'left'
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return 'Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                var chartMonthlyLemburPerDepartemen = new ApexCharts(document.querySelector("#chartMonthlyLemburPerDepartemen"), options);
                chartMonthlyLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    getCurrentMonthLemburPerDepartemen();
    function getCurrentMonthLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-current-month-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                let data = response.data;
                var series = [];
                var categories = [];
                var hours = [];
                const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#fdc500', '#6c757d'];

                for (const val of data) {
                    const departmentName = val.departemen;
                    categories.push(departmentName);
                    series.push(val.total_nominal);
                    hours.push(val.total_durasi / 60);
                }

                var options = {
                    series: [{
                    data: series
                  }],
                    chart: {
                    type: 'bar',
                    height: '100%'
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
                //   colors: colors,
                  dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                      colors: ['#fff']
                    },
                    formatter: function (val, opt) {
                    //   return opt.w.globals.labels[opt.dataPointIndex] + ":  " + 'Rp ' + val.toLocaleString('id-ID')
                         return 'Rp ' + val.toLocaleString('id-ID')
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
                    categories: categories,
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
                  title: {
                      text: 'Grafik Lembur '+ ' (' + new Date().toLocaleString('default', { month: 'long' }) + ' ' + new Date().getFullYear() + ')',
                      align: 'center',
                      floating: true
                  },
                  tooltip: {
                    theme: 'light',
                    x: {
                      show: false
                    },
                    y: {
                      title: {
                        formatter: function (val, opt) {
                            return 'Total Durasi Lembur';
                          },
                      },
                        formatter: function (val, opt) {
                            return hours[opt.dataPointIndex] + ' Jam';
                        }
                    }
                  }
                  };

                var chartCurrentMonthLemburPerDepartemen = new ApexCharts(document.querySelector("#chartCurrentMonthLemburPerDepartemen"), options);
                chartCurrentMonthLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    getWeeklyLemburPerDepartemen();
    function getWeeklyLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-weekly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                let data = response.data;
                var series = [];
                var dataMinggu1 = [];
                var dataMinggu2 = [];
                var dataMinggu3 = [];
                var dataMinggu4 = [];
                var categories = [];

                for (const val of data) {
                    const departmentName = val.departemen;
                    categories.push(departmentName);
                    dataMinggu1.push(val.minggu_1);
                    dataMinggu2.push(val.minggu_2);
                    dataMinggu3.push(val.minggu_3);
                    dataMinggu4.push(val.minggu_4);
                }

                series.push({
                    name: 'Minggu Ke-1',
                    data: dataMinggu1,
                },{
                    name: 'Minggu Ke-2',
                    data: dataMinggu2,
                },{
                    name: 'Minggu Ke-3',
                    data: dataMinggu3,
                },{
                    name: 'Minggu Ke-4',
                    data: dataMinggu4,
                })

                var options = {
                    series: series,
                    chart: {
                    type: 'bar',
                    height: '100%'
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
                         return 'Rp ' + val.toLocaleString('id-ID')
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
                    categories: categories,
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
                  title: {
                      text: 'Grafik Weekly Lembur' + ' (' + new Date().toLocaleString('default', { month: 'long' }) + ' ' + new Date().getFullYear() + ')',
                      align: 'center',
                      floating: true
                  },
                  legend: {
                    show: false
                  },
                  tooltip: {
                    theme: 'light',
                    x: {
                      show: true
                    },
                    y: {
                      title: {
                        formatter: function (val, opt) {
                            const seriesIndex = opt.seriesIndex;
                            return 'Minggu Ke-' + (seriesIndex + 1);
                        },
                      },
                      formatter: function (val) {
                        return 'Rp ' + val.toLocaleString('id-ID');
                    }
                    }
                  }
                  };

                var chartWeeklyLemburPerDepartemen = new ApexCharts(document.querySelector("#chartWeeklyLemburPerDepartemen"), options);
                chartWeeklyLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
});