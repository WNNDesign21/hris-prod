$(function () {
  'use strict';

  // ======================= GLOBAL VAR =======================
  let loadingSwal;

  // ======================= SWEETALERT LOADING =======================
  function loadingSwalShow() {
    loadingSwal = Swal.fire({
      imageHeight: 300,
      showConfirmButton: false,
      title: '<i class="fas fa-sync-alt fa-spin fs-80"></i>',
      allowOutsideClick: false,
      background: 'rgba(0, 0, 0, 0)',
    });
  }

  function loadingSwalClose() {
    if (loadingSwal) loadingSwal.close();
  }

  // ======================= DATA KARYAWAN =======================
  function getDataKaryawan() {
    $.get(base_url + '/master-data/dashboard/get-data-karyawan-dashboard', function (response) {
      const d = response.data;
      $('#aktif_karyawan').text(d.aktif);
      $('#habis_kontrak_karyawan').text(d.habis_kontrak);
      $('#mengundurkan_diri_karyawan').text(d.mengundurkan_diri);
      $('#pensiun_karyawan').text(d.pensiun);
      $('#terminasi_karyawan').text(d.terminasi);
    });
  }

  // ======================= TURNOVER CHART =======================
  function turnoverChart() {
    $.get(base_url + '/master-data/dashboard/get-data-turnover-monthly-dashboard', function (response) {
      const dataRate = response.data;
      const options = {
        series: [{ name: 'Turnover Rate (%)', data: dataRate }],
        chart: { type: 'bar', height: '100%', stacked: true, toolbar: { show: true } },
        colors: ['#6993ff'],
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
        plotOptions: { bar: { columnWidth: '30%', borderRadius: 4 } },
        grid: { borderColor: '#f7f7f7' },
      };
      new ApexCharts(document.querySelector('#turnover-chart'), options).render();
    });
  }

  // ======================= TURNOVER DETAIL CHART =======================
  function turnoverDetailChart() {
    $.get(base_url + '/master-data/dashboard/get-data-turnover-detail-monthly-dashboard', function (res) {
      const d = res.data;
      const options = {
        series: [
          { name: 'Masuk', data: d.masuk, color: '#007bff' },
          { name: 'Habis Kontrak', data: d.habis_kontrak, color: '#dc3545' },
          { name: 'Mengundurkan Diri', data: d.mengundurkan_diri, color: '#6c757d' },
          { name: 'Pensiun', data: d.pensiun, color: '#28a745' },
          { name: 'Terminasi', data: d.terminasi, color: '#9467bd' },
        ],
        chart: { type: 'line', height: 260, stacked: false, toolbar: { show: false } },
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
      };
      new ApexCharts(document.querySelector('#turnover-detail-chart'), options).render();
    });
  }

  // ======================= KONTRAK PROGRESS =======================
  function kontrakProgressChart() {
    $.get(base_url + '/master-data/dashboard/get-data-kontrak-progress-dashboard', function (res) {
      const d = res.data;
      const options = {
        chart: { height: 180, type: 'radialBar' },
        series: [d],
        colors: ['#0052cc'],
        plotOptions: {
          radialBar: {
            hollow: { size: '70%' },
            track: { background: '#ff9920' },
            dataLabels: {
              value: { fontSize: '30px', show: true },
            },
          },
        },
        labels: ['On Progress'],
      };
      new ApexCharts(document.querySelector('#kontrak-progress-chart'), options).render();
    });
  }

  // ======================= TOTAL DATA STATUS =======================
  function totalDataKaryawanByStatus() {
    $.get(base_url + '/master-data/dashboard/get-total-data-karyawan-by-status-karyawan-dashboard', function (res) {
      const d = res.data;
      const options = {
        series: d,
        labels: ['Re-Active', 'Habis Kontrak', 'Mengundurkan Diri', 'Pensiun', 'Terminasi'],
        chart: { height: 230, type: 'donut' },
        colors: ['#04a08b', '#6993ff', '#ff9920', '#bac0c7', '#9467bd'],
        legend: { position: 'bottom', horizontalAlign: 'center' },
      };
      new ApexCharts(document.querySelector('#total-data-by-status-chart'), options).render();
    });
  }

  // ======================= REKAP KARAWANG =======================
  function rekapKarawangTable() {
    $.get(base_url + '/master-data/dashboard/rekap-karawang', function (res) {
      const dalam = res.dalam, luar = res.luar;
      const isiTabel = (data, id) => {
        let html = '', no = 1;
        if (data.length)
          data.forEach(i => html += `<tr><td>${no++}</td><td>${i.alamat}</td></tr>`);
        else html = `<tr><td colspan="2" class="text-center">Tidak ada data</td></tr>`;
        $(`#${id} tbody`).html(html);
      };
      isiTabel(dalam, 'tabel-dalam-karawang');
      isiTabel(luar, 'tabel-luar-karawang');
    });
  }

  // ======================= FUNGSI UTAMA REKAP WILAYAH =======================
  function rekapWilayah(jenis, kolom) {
    let url = `${base_url}/master-data/dashboard/rekap-${jenis}`;
    let tableId = `#table-${jenis}`;
    let tbodyId = `#tabel-rekap-${jenis}`;
    let chartId = `#chart-${jenis}`;
    let chartKey = `chart${jenis.charAt(0).toUpperCase() + jenis.slice(1)}`;

    $.get(url, function (res) {
      const data = res.data;
      let html = '', no = 1;
      data.forEach(i => {
        html += `<tr><td>${no++}</td><td>${i[kolom]}</td><td>${i.total}</td></tr>`;
      });
      $(tbodyId).html(html);

      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart(chartId, chartKey, rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  function renderWilayahChart(chartId, chartKey, pageData) {
    if (!pageData.length) return;
    const labels = pageData.map(i => i[1]);
    const values = pageData.map(i => parseInt(i[2]));
    const total = values.reduce((a, b) => a + b, 0);

    const opt = {
      series: values,
      labels,
      chart: { type: 'donut', height: 280, animations: { enabled: true, easing: 'easeinout', speed: 500 } },
      colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A'],
      plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => total } } } } },
      legend: { position: 'bottom', horizontalAlign: 'center' },
    };

    if (window[chartKey]) window[chartKey].destroy();
    window[chartKey] = new ApexCharts(document.querySelector(chartId), opt);
    window[chartKey].render();
  }

  // ======================= REKAP KONTRAK =======================
  function rekapKontrakTable() {
    const url = base_url + '/master-data/dashboard/rekap-kontrak';
    $.get(url, function (res) {
      const data = res.data;
      let html = '', no = 1, chartData = [];
      data.forEach(i => {
        html += `<tr><td>${no++}</td><td>${i.keterangan}</td><td>${i.total}</td></tr>`;
        chartData.push(i.total);
      });
      $('#tabel-rekap-kontrak').html(html);

      const opt = {
        series: [{ name: 'Jumlah Karyawan', data: chartData }],
        chart: { type: 'bar', height: 350 },
        plotOptions: { bar: { columnWidth: '45%', borderRadius: 8 } },
        xaxis: { categories: ['PKWTT', 'PKWT', 'PK'] },
        colors: ['#008FFB', '#00E396', '#FEB019'],
      };
      if (window.rekapKontrakChart) window.rekapKontrakChart.destroy();
      window.rekapKontrakChart = new ApexCharts(document.querySelector('#rekap-kontrak-chart'), opt);
      window.rekapKontrakChart.render();
    });
  }

  // ======================= JALANKAN SEMUA =======================
  getDataKaryawan();
  turnoverChart();
  turnoverDetailChart();
  kontrakProgressChart();
  totalDataKaryawanByStatus();
  rekapWilayah('desa', 'desa');
  rekapWilayah('kecamatan', 'kecamatan');
  rekapWilayah('kabupaten', 'kabupaten');
  rekapWilayah('provinsi', 'provinsi');
  rekapKarawangTable();
  rekapKontrakTable();
});
