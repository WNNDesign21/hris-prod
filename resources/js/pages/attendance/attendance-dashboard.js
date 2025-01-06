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

    // getMonthlyAttendanceDepartment();
    // function getMonthlyAttendanceDepartment() {
    //     let url = base_url + '/lembure/dashboard-lembur/get-monthly-lembur-per-departemen';
    //     $.ajax({
    //         url: url,
    //         method: 'POST',
    //         dataType: 'json',
    //         data: {},
    //         success: function(response) {
    //             let data = response.data;
    //             let batas = response.batas;
    //             var series = [];
    //             const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#fdc500', '#6c757d'];
    //             let colorIndex = 0;

    //             // for (const val of data) {
    //                 // const departmentName = val.departemen;
    //                 const monthlyData = [data.januari, data.februari, data.maret, data.april, data.mei, data.juni, data.juli, data.agustus, data.september, data.oktober, data.november, data.desember];   
    //                 const departmentSeries = {
    //                     name: 'Nominal Lembur',
    //                     data: monthlyData,
    //                     color: '#007bff', 
    //                 };
    //                 series.push(departmentSeries);
    //                 // colorIndex++;
    //             // }

    //             let target = [];
    //             if(batas){
    //               $.each(batas, function(key, value) {
    //                 if (value == null || value == 0) return;
    //                 target.push({
    //                       y: value,
    //                       borderColor: '#FF0000', 
    //                       label: {
    //                           borderColor: '#FF0000',
    //                           style: {
    //                               color: '#fff',
    //                               background: '#FF0000'
    //                           },
    //                           text: key.toUpperCase() + ' : Rp ' + value.toLocaleString('id-ID')
    //                       }
    //                   })
    //               })
    //             } 

    //             var options = {
    //                 annotations: {
    //                     yaxis: target
    //                 },
    //                 series: series,
    //                 chart: {
    //                     height: '100%',
    //                     type: 'area',
    //                     toolbar: {
    //                         show: true
    //                     },
    //                     zoom: {
    //                         enabled: true
    //                     },
    //                     export: {
    //                         svg: {
    //                             filename: undefined,
    //                         },
    //                         png: {
    //                             filename: undefined,
    //                         }
    //                     },
    //                 },
    //                 responsive: [{
    //                     breakpoint: 480,
    //                     options: {
    //                         legend: {
    //                             position: 'bottom',
    //                             offsetX: -10,
    //                             offsetY: 0
    //                         }
    //                     }
    //                 }],
    //                 grid: {
    //                     show: true,
    //                     borderColor: '#f7f7f7',
    //                 },
    //                 plotOptions: {
    //                     bar: {
    //                         horizontal: false,
    //                         columnWidth: '30%',
    //                         endingShape: 'rounded',
    //                         colors: {
    //                             backgroundBarColors: ['#f0f0f0'],
    //                             backgroundBarOpacity: 0,
    //                         },
    //                     },
    //                 },
    //                 dataLabels: {
    //                     enabled: false
    //                 },
    //                 xaxis: {
    //                     categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //                 },
    //                 yaxis: {
    //                     labels: {
    //                         formatter: function (val) {
    //                             return 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'K';
    //                         }
    //                     }
    //                 },
    //                 legend: {
    //                     show: true,
    //                 },
    //                 markers: {
    //                     size: 0,
    //                 },
    //                 title: {
    //                     text: 'Monitoring Lembur '+ new Date().getFullYear(),
    //                     align: 'left'
    //                 },
    //                 stroke: {
    //                     curve: 'smooth'
    //                 },
    //                 tooltip: {
    //                     y: {
    //                         formatter: function (val) {
    //                             return 'Rp ' + val.toLocaleString('id-ID');
    //                         }
    //                     }
    //                 }
    //             };

    //             var chartMonthlyLemburPerDepartemen = new ApexCharts(document.querySelector("#chartMonthlyLemburPerDepartemen"), options);
    //             chartMonthlyLemburPerDepartemen.render();
    //         },
    //         error: function(error) {
    //             console.error(error);
    //         }
    //     });
    // }
});