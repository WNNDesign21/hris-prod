$(function () {

    'use strict';

    let loadingSwal;
    let turnoverRate;
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

    function getDataKaryawan(){
      let url = base_url + '/master-data/dashboard/get-data-karyawan-dashboard';
      $.ajax({
          url: url,
          method: 'GET',
          success: function(response) {
              let dataKaryawan = response.data;
              $('#aktif_karyawan').text(dataKaryawan.aktif);
              $('#habis_kontrak_karyawan').text(dataKaryawan.habis_kontrak);
              $('#mengundurkan_diri_karyawan').text(dataKaryawan.mengundurkan_diri);
              $('#pensiun_karyawan').text(dataKaryawan.pensiun);
              $('#terminasi_karyawan').text(dataKaryawan.terminasi);
          },
          error: function(error) {
              console.error(error);
          }
      });
    }

    function turnoverChart(){
      let url = base_url + '/master-data/dashboard/get-data-turnover-monthly-dashboard';
      $.ajax({
          url: url,
          method: 'GET',
          success: function(response) {
              let dataRate = response.data;
              var options = {
                series: [
                  {
                    name: 'Turnover Rate (%)',
                    data: dataRate
                  },
                ],
                chart: {
                foreColor:"#bac0c7",
                type: 'bar',
                height: '100%',
                stacked: true,
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
              colors:['#6993ff'],
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
              legend: {
                show: true,
              },
              fill: {
                opacity: 1
              }
              };
      
              var chart = new ApexCharts(document.querySelector("#turnover-chart"), options);
              chart.render();
              
          },
          error: function(error) {
              console.error(error);
          }
      });
    }

    function turnoverDetailChart(){
      let url = base_url + '/master-data/dashboard/get-data-turnover-detail-monthly-dashboard';
      $.ajax({
          url: url,
          method: 'GET',
          success: function(response) {
              let dataResponse = response.data;
              let masuk = dataResponse.masuk;
              let habis_kontrak = dataResponse.habis_kontrak;
              let mengundurkan_diri = dataResponse.mengundurkan_diri;
              let pensiun = dataResponse.pensiun;
              let terminasi = dataResponse.terminasi;

              var options = {
                series: [
                  {
                    name: 'Masuk',
                    data: masuk,
                    color: '#007bff'
                  },
                  {
                    name: 'Habis Kontrak',
                    data: habis_kontrak,
                    color: '#dc3545'

                  },
                  {
                    name: 'Mengundurkan Diri',
                    data: mengundurkan_diri,
                    color: '#6c757d'
                  },
                  {
                    name: 'Pensiun',
                    data: pensiun,
                    color: '#28a745'
                  },
                  {
                    name: 'Terminasi',
                    data: terminasi,
                    color: '#9467bd'
                  },
                ],
                chart: {
                  height: 260,
                  type: 'line',
                  stacked: false,
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
              legend: {
                show: true,
              },
              fill: {
                opacity: 1
              }
              };
      
              var chart = new ApexCharts(document.querySelector("#turnover-detail-chart"), options);
              chart.render();
              
          },
          error: function(error) {
              console.error(error);
          }
      });
    }

    function kontrakProgressChart()
    {
          let url = base_url + '/master-data/dashboard/get-data-kontrak-progress-dashboard';
          $.ajax({
              url: url,
              method: 'GET',
              success: function(response) {
                  let dataKontrakProgress = response.data;
                  var options = {
                    chart: {
                      height: 180,
                      type: "radialBar",
                      toolbar: {
                        show: true
                      },
                    },
                    series: [dataKontrakProgress],
                      colors: ['#0052cc'],
                    plotOptions: {
                      radialBar: {
                        hollow: {
                          margin: 15,
                          size: "70%"
                        },
                        track: {
                          background: '#ff9920',
                        },
          
                        dataLabels: {
                          showOn: "always",
                          name: {
                            offsetY: -10,
                            show: false,
                            color: "#888",
                            fontSize: "13px"
                          },
                          value: {
                            color: "#111",
                            fontSize: "30px",
                            show: true
                          }
                        }
                      }
                    },
          
                    stroke: {
                      lineCap: "round",
                    },
                    labels: ["On Progress"]
                  };
          
                  var chart = new ApexCharts(document.querySelector("#kontrak-progress-chart"), options);
                  chart.render();
                  
              },
              error: function(error) {
                  console.error(error);
              }
          });
    }

    function totalDataKaryawanByStatus(){
      let url = base_url + '/master-data/dashboard/get-total-data-karyawan-by-status-karyawan-dashboard';
      $.ajax({
          url: url,
          method: 'GET',
          success: function(response) {
              let dataTotalKaryawanByStatus = response.data;
              var options = {
                series: dataTotalKaryawanByStatus,
                labels: ['Re-Active', 'Habis Kontrak', 'Mengundurkan Diri', 'Pensiun', 'Terminasi'],
                chart: {
                height:230,
                type: 'donut',
                toolbar: {
                  show: true
                },
              },
              dataLabels: {
                enabled: false
              },
              responsive: [{
                breakpoint: 480,
                options: {
                  chart: {
                    width: 200
                  },
                  legend: {
                    show: true
                  }
                }
              }],
              colors:['#04a08b', '#6993ff', '#ff9920', '#bac0c7', '#9467bd'],
              legend: {
                position: 'bottom',
                  horizontalAlign: 'center',
              }
              };
      
              var chart = new ApexCharts(document.querySelector("#total-data-by-status-chart"), options);
              chart.render();
              
          },
          error: function(error) {
              console.error(error);
          }
      });
    }

    function renderRekapKontrakChart(seriesData) {
        var options = {
            series: seriesData,
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['PKWTT', 'PKWT', 'Pengkaryaan'],
            colors:['#6993ff', '#ff9920', '#04a08b'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
            }
        };

        // Hancurkan chart lama jika ada untuk mencegah duplikasi
        if (window.rekapKontrakChart) {
            window.rekapKontrakChart.destroy();
        }

        window.rekapKontrakChart = new ApexCharts(document.querySelector("#rekap-kontrak-chart"), options);
        window.rekapKontrakChart.render();
    }

    function renderRekapWilayahChart(seriesData) {
        var options = {
            series: seriesData,
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['Dalam Karawang', 'Luar Karawang', 'Dalam Warung Bambu', 'Luar Warung Bambu'],
            colors:['#007bff', '#6c757d'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
            }
        };

        if (window.rekapWilayahChart) {
            window.rekapWilayahChart.destroy();
        }
        window.rekapWilayahChart = new ApexCharts(document.querySelector("#rekap-wilayah-chart"), options);
        window.rekapWilayahChart.render();
    }

    // tangguh buka
    function totalDataKaryawanRekap() {
    let filter = $('#filter_tingkat').val() || 'all';
    let url = base_url + '/master-data/dashboard/get-total-karyawan-rekap?filter=' + filter;

    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            let data = response.data;

            // Data untuk Chart
            let kontrakData = [data.pkwtt, data.pkwt, data.pk];
            let wilayahData = [data.dalam_karawang, data.luar_karawang, data.dalam_warung_bambu, data.luar_warung_bambu];
            renderRekapKontrakChart(kontrakData);
            renderRekapWilayahChart(wilayahData);

            $('#total_direct_indirect').text(data.direct_indirect);
            $('#total_sinas').text(data.sinas);
            $('#total_desa').text(data.desa);
            $('#total_kec').text(data.kecamatan);
            $('#total_kab').text(data.kabupaten);
            $('#total_prov').text(data.provinsi);
            $('#total_pkwtt').text(data.pkwtt);
            $('#total_pkwt').text(data.pkwt);
            $('#total_pk').text(data.pk);
            $('#total_dalam_krw').text(data.dalam_karawang);
            $('#total_luar_krw').text(data.luar_karawang);
            $('#total_dalam_wb').text(data.dalam_warung_bambu);
            $('#total_luar_wb').text(data.luar_warung_bambu);
        },
        error: function(error) {
            console.error(error);
        }
    });
}

// jalankan saat halaman selesai load
$('#filter_tingkat').on('change', function() {
    totalDataKaryawanRekap();
});

totalDataKaryawanRekap();


// tangguh tutup


    getDataKaryawan();
    turnoverChart();
    kontrakProgressChart();
    totalDataKaryawanByStatus();
    turnoverDetailChart();
  }); // End of use strict
  