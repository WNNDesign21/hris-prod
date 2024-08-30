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
              $('#terminasi_karyawan').text(dataKaryawan.terminasi);
              $('#resign_karyawan').text(dataKaryawan.resign);
              $('#pensiun_karyawan').text(dataKaryawan.pensiun);
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
                    name: 'Turnover Rate',
                    data: dataRate
                  },
                ],
                chart: {
                foreColor:"#bac0c7",
                type: 'bar',
                height: 200,
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
                      type: "radialBar"
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

    getDataKaryawan();
    turnoverChart();
    kontrakProgressChart();
      
      
          var options = {
            series: [9, 5, 13],
              labels: ['Prog..', 'Comp..', 'Ye to'],
            chart: {
            height:230,
            type: 'donut',
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
                show: false
              }
            }
          }],
          colors:['#04a08b', '#6993ff', '#ff9920'],
          legend: {
            position: 'bottom',
              horizontalAlign: 'center',
          }
          };
  
          var chart = new ApexCharts(document.querySelector("#charts_widget_2_chart"), options);
          chart.render();
      
      
        
  
      
  }); // End of use strict
  