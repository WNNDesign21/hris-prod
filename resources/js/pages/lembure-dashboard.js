import { load } from "@eonasdan/tempus-dominus/dist/plugins/fa-five";

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

     // MONTHLY
     var modalFilterMonthlyOptions = {
      backdrop: true,
      keyboard: false,
    };

    var modalFilterMonthly = new bootstrap.Modal(
        document.getElementById("modal-filter-monthly"),
        modalFilterMonthlyOptions
    );

    function openFilterMonthly() {
        modalFilterMonthly.show();
    }

    function closeFilterMonthly() {
        modalFilterMonthly.hide();
    }

    $('.btnFilterMonthly').on('click', function() {
      openFilterMonthly();
    });

    $('.closeFilterMonthly').on('click', function() {
      closeFilterMonthly();
    });

    $('#filterDepartemenMonthly').select2({
       dropdownParent: $('#modal-filter-monthly')
    });

    $('#filterOrganisasiMonthly').select2({
       dropdownParent: $('#modal-filter-monthly')
    });

    $('#filterTahunMonthly').select2({
       dropdownParent: $('#modal-filter-monthly')
    });

    $('.btnResetFilterMonthly').on('click', function() {
        $('#filterOrganisasiMonthly').val('').trigger('change');
        $('#filterDepartemenMonthly').val('').trigger('change');
        $('#filterTahunMonthly').val(new Date().getFullYear()).trigger('change');
    });

    $(".btnSubmitFilterMonthly").on("click", function () {
        loadingSwalShow();
        let url = base_url + '/lembure/dashboard-lembur/get-monthly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen: $('#filterDepartemenMonthly').val(),
                organisasi: $('#filterOrganisasiMonthly').val(),
                tahun: $('#filterTahunMonthly').val()
            },
            success: function(response) {
              loadingSwalClose();
                let dataActual = response.dataActual;
                let dataPlanning = response.dataPlanning;
                let batas = response.batas;
                var series = [];

                const monthlyDataActual = [dataActual.januari, dataActual.februari, dataActual.maret, dataActual.april, dataActual.mei, dataActual.juni, dataActual.juli, dataActual.agustus, dataActual.september, dataActual.oktober, dataActual.november, dataActual.desember];   
                const departmentSeriesActual = {
                    name: 'Nominal Lembur Aktual',
                    data: monthlyDataActual,
                    color: '#007bff', 
                };
                series.push(departmentSeriesActual);

                const monthlyDataPlanning = [dataPlanning.januari, dataPlanning.februari, dataPlanning.maret, dataPlanning.april, dataPlanning.mei, dataPlanning.juni, dataPlanning.juli, dataPlanning.agustus, dataPlanning.september, dataPlanning.oktober, dataPlanning.november, dataPlanning.desember];   
                const departmentSeriesPlanning = {
                    name: 'Nominal Lembur Planning',
                    data: monthlyDataPlanning,
                    color: '#ffc107', 
                };
                series.push(departmentSeriesPlanning);

                console.log(series)

                let target = [];
                if(batas){
                  $.each(batas, function(key, value) {
                    if (value == null || value == 0) return;
                    target.push({
                          y: value,
                          borderColor: '#FF0000', 
                          label: {
                              borderColor: '#FF0000',
                              style: {
                                  color: '#fff',
                                  background: '#FF0000'
                              },
                              text: key.toUpperCase() + ' : Rp ' + value.toLocaleString('id-ID') ?? 0
                          }
                      })
                  })
                } 

                chartMonthlyLemburPerDepartemen.updateOptions({
                    annotations: {
                        yaxis: target
                    },
                    series: series,
                    title: {
                        text: 'Monitoring Lembur '+ $('#filterTahunMonthly').val(),
                    }
                }); 
            },
            error: function(error) {
                console.error(error);
            }
        });
          closeFilterMonthly();
    });

    var chartMonthlyLemburPerDepartemen;
    getMonthlyLemburPerDepartemen();
    function getMonthlyLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-monthly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                organisasi: $('#filterOrganisasiMonthly').val(),
                departemen: $('#filterDepartemenMonthly').val(),
                tahun: $('#filterTahunMonthly').val()
            },
            success: function(response) {
                let dataActual = response.dataActual;
                let dataPlanning = response.dataPlanning;
                let batas = response.batas;
                var series = [];

                const monthlyDataActual = [dataActual.januari, dataActual.februari, dataActual.maret, dataActual.april, dataActual.mei, dataActual.juni, dataActual.juli, dataActual.agustus, dataActual.september, dataActual.oktober, dataActual.november, dataActual.desember];   
                const departmentSeriesActual = {
                  name: 'Nominal Lembur Actual',
                  data: monthlyDataActual,
                  color: '#007bff', 
                };
                series.push(departmentSeriesActual);

                const monthlyDataPlanning = [dataPlanning.januari, dataPlanning.februari, dataPlanning.maret, dataPlanning.april, dataPlanning.mei, dataPlanning.juni, dataPlanning.juli, dataPlanning.agustus, dataPlanning.september, dataPlanning.oktober, dataPlanning.november, dataPlanning.desember];   
                const departmentSeriesPlanning = {
                  name: 'Nominal Lembur Planning',
                  data: monthlyDataPlanning,
                  color: '#ffc107', 
                };
                series.push(departmentSeriesPlanning);

                let target = [];
                if(batas){
                  $.each(batas, function(key, value) {
                    if (value == null || value == 0) return;
                    target.push({
                          y: value,
                          borderColor: '#FF0000', 
                          label: {
                              borderColor: '#FF0000',
                              style: {
                                  color: '#fff',
                                  background: '#FF0000'
                              },
                              text: key.toUpperCase() + ' : Rp ' + value.toLocaleString('id-ID') ?? 0
                          }
                      })
                  })
                } 

                var options = {
                    annotations: {
                        yaxis: target
                    },
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
                                return 'Rp ' + (val ? val.toLocaleString('id-ID') : 0);
                            }
                        }
                    }
                };

                chartMonthlyLemburPerDepartemen = new ApexCharts(document.querySelector("#chartMonthlyLemburPerDepartemen"), options);
                chartMonthlyLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    // CURRENT MONTH
    var modalFilterCurrentOptions = {
      backdrop: true,
      keyboard: false,
    };

    var modalFilterCurrent = new bootstrap.Modal(
        document.getElementById("modal-filter-current"),
        modalFilterCurrentOptions
    );

    function openFilterCurrent() {
        modalFilterCurrent.show();
    }

    function closeFilterCurrent() {
        modalFilterCurrent.hide();
    }

    $('.btnFilterCurrent').on('click', function() {
      openFilterCurrent();
    });

    $('.closeFilterCurrent').on('click', function() {
      closeFilterCurrent();
    });

    $('#filterDepartemenCurrent').select2({
      dropdownParent: $('#modal-filter-current')
    });

    $('.btnResetFilterCurrent').on('click', function() {
      $('#filterDepartemenCurrent').val([]).trigger('change');
      $('#filterPeriodeCurrent').val('');
    });

    $(".btnSubmitFilterCurrent").on("click", function () {
        loadingSwalShow();
        let url = base_url + '/lembure/dashboard-lembur/get-current-month-lembur-per-departemen';
        let url_weekly = 
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen: $('#filterDepartemenCurrent').val(),
                periode: $('#filterPeriodeCurrent').val()
            },
            success: function(response) {
              loadingSwalClose();
              let data = response.data;
              let batas = response.batas;
              var series = [];
              var categories = [];
              var hours = [];

              data.forEach(function(val, index) {
                  const departmentName = val.departemen;
                  const departmentSeries = {
                    x: departmentName,
                    y: val.total_nominal,
                    goals: [{
                      value: batas.length > 0 && (batas[index]['id_departemen'] == val.id_departemen) ? batas[index]['nominal_batas_lembur'] : 0,
                      name: 'Batas Budget Lembur',
                      strokeWidth: 5,
                      strokeColor: '#FF0000',
                      border: '#FF0000',
                      dashArray: 5
                    }],
                  };
                  categories.push(departmentName);
                  series.push(departmentSeries);
                  hours.push(val.total_durasi / 60);
              });

              chartCurrentMonthLemburPerDepartemen.updateOptions({
                  series: [{
                      data: series
                    }],
                  title: {
                      text: 'Grafik Lembur (' + ($('#filterPeriodeCurrent').val() ? new Date($('#filterPeriodeCurrent').val()).toLocaleString('default', { month: 'long' }) + ' ' + new Date($('#filterPeriodeCurrent').val()).getFullYear() : new Date().toLocaleString('default', { month: 'long' }) + ' ' + new Date().getFullYear()) + ')',
                  }
                }); 
            },
            error: function(error) {
                console.error(error);
            }
        });
          closeFilterMonthly();
    });

    var chartCurrentMonthLemburPerDepartemen;
    getCurrentMonthLemburPerDepartemen();
    function getCurrentMonthLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-current-month-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen: $('#filterDepartemenCurrent').val(),
                periode: $('#filterPeriodeCurrent').val()
            },
            success: function(response) {
                let data = response.data;
                let batas = response.batas;
                var series = [];
                var categories = [];
                var hours = [];

                data.forEach(function(val, index) {
                    const departmentName = val.departemen;
                    const departmentSeries = {
                      x: departmentName,
                      y: val.total_nominal,
                      goals: [{
                        value: batas.length > 0 && (batas[index]['id_departemen'] == val.id_departemen) ? batas[index]['nominal_batas_lembur'] : 0,
                        name: 'Batas Budget Lembur',
                        strokeWidth: 5,
                        strokeColor: '#FF0000',
                        border: '#FF0000',
                        dashArray: 5
                      }],
                    };
                    categories.push(departmentName);
                    series.push(departmentSeries);
                    hours.push(val.total_durasi / 60);
                });

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
                  dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                      colors: ['#fff']
                    },
                    formatter: function (val, opt) {
                        const goalValue = opt.w.config.series[0].data[opt.dataPointIndex].goals[0].value;
                        if (goalValue === 0) {
                          return 'Rp ' + (val.toLocaleString('id-ID') ?? 0) + ' (Unknown)';
                        }
                        const percentage = (val / goalValue) * 100;
                        return 'Rp ' + (val.toLocaleString('id-ID') ?? 0) + ' (' + percentage.toFixed(2) + '%)';
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
                            return 'Total Nominal Lembur';
                          },
                      },
                        formatter: function (val, opt) {
                            return 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                  }
                  };

                chartCurrentMonthLemburPerDepartemen = new ApexCharts(document.querySelector("#chartCurrentMonthLemburPerDepartemen"), options);
                chartCurrentMonthLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    // WEEKLY
    var modalFilterWeeklyOptions = {
      backdrop: true,
      keyboard: false,
    };

    var modalFilterWeekly = new bootstrap.Modal(
        document.getElementById("modal-filter-weekly"),
        modalFilterWeeklyOptions
    );

    function openFilterWeekly() {
        modalFilterWeekly.show();
    }

    function closeFilterWeekly() {
        modalFilterWeekly.hide();
    }

    $('.btnFilterWeekly').on('click', function() {
      openFilterWeekly();
    });

    $('.closeFilterWeekly').on('click', function() {
      closeFilterWeekly();
    });

    $('#filterDepartemenWeekly').select2({
      dropdownParent: $('#modal-filter-weekly')
    });

    $('.btnResetFilterWeekly').on('click', function() {
        $('#filterDepartemenWeekly').val([]).trigger('change');
        $('#filterPeriodeWeekly').val('');
    });

    $(".btnSubmitFilterWeekly").on("click", function () {
        loadingSwalShow();
        let url = base_url + '/lembure/dashboard-lembur/get-weekly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen: $('#filterDepartemenWeekly').val(),
                periode: $('#filterPeriodeWeekly').val()
            },
            success: function(response) {
              loadingSwalClose();
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

                chartWeeklyLemburPerDepartemen.updateOptions({
                  series: series,
                  xaxis: {
                    categories: categories,
                  },
                  title: {
                      text: 'Grafik Weekly Lembur' + ' (' + ($('#filterPeriodeWeekly').val() ? new Date($('#filterPeriodeWeekly').val()).toLocaleString('default', { month: 'long' }) + ' ' + new Date($('#filterPeriodeWeekly').val()).getFullYear() : new Date().toLocaleString('default', { month: 'long' }) + ' ' + new Date().getFullYear()) + ')',
                  }
                }); 
            },
            error: function(error) {
                console.error(error);
            }
        });
          closeFilterWeekly();
    });

    var chartWeeklyLemburPerDepartemen;
    getWeeklyLemburPerDepartemen();
    function getWeeklyLemburPerDepartemen() {
        let url = base_url + '/lembure/dashboard-lembur/get-weekly-lembur-per-departemen';
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                departemen: $('#filterDepartemenWeekly').val(),
                periode: $('#filterPeriodeWeekly').val()
            },
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

                chartWeeklyLemburPerDepartemen = new ApexCharts(document.querySelector("#chartWeeklyLemburPerDepartemen"), options);
                chartWeeklyLemburPerDepartemen.render();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
});