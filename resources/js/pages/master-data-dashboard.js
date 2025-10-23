$(function () {
  'use strict';

  // ======================= GLOBAL VAR =======================
  let currentFilterType = null;

  // ======================= DATA KARYAWAN =======================
  function getDataKaryawan() {
    $.get(base_url + '/master-data/dashboard/get-data-karyawan-dashboard', function (res) {
      const d = res.data;
      $('#aktif_karyawan').text(d.aktif);
      $('#habis_kontrak_karyawan').text(d.habis_kontrak);
      $('#mengundurkan_diri_karyawan').text(d.mengundurkan_diri);
      $('#pensiun_karyawan').text(d.pensiun);
      $('#terminasi_karyawan').text(d.terminasi);
    });
  }

  // ======================= TURNOVER CHART =======================
  function turnoverChart() {
    $.get(base_url + '/master-data/dashboard/get-data-turnover-monthly-dashboard', function (res) {
      const dataRate = res.data;
      const options = {
        series: [{ name: 'Turnover Rate (%)', data: dataRate }],
        chart: { type: 'bar', height: '100%', stacked: true },
        colors: ['#6993ff'],
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
        plotOptions: { bar: { columnWidth: '30%', borderRadius: 4 } },
      };
      new ApexCharts(document.querySelector('#turnover-chart'), options).render();
    });
  }

  // ======================= TURNOVER DETAIL =======================
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
        chart: { type: 'line', height: 260 },
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
            dataLabels: { value: { fontSize: '30px', show: true } },
          },
        },
        labels: ['On Progress'],
      };
      new ApexCharts(document.querySelector('#kontrak-progress-chart'), options).render();
    });
  }

  // ======================= TOTAL STATUS =======================
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

  // ======================= RENDER CHART =======================
  function renderWilayahChart(chartSelector, chartKey, pageData) {
    if (!pageData || !pageData.length) return;

    const labels = pageData.map(r => Array.isArray(r) ? r[1] : r.keterangan);
    const values = pageData.map(r => parseInt(Array.isArray(r) ? r[2] : r.total));
    const total = values.reduce((a, b) => a + b, 0);

    const options = {
      series: values,
      labels: labels,
      chart: { type: 'donut', height: 280 },
      colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A'],
      plotOptions: {
        pie: {
          donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => total } } },
        },
      },
      legend: { position: 'bottom', horizontalAlign: 'center' },
    };

    if (window[chartKey]) window[chartKey].destroy();
    window[chartKey] = new ApexCharts(document.querySelector(chartSelector), options);
    window[chartKey].render();
  }

  // ======================= MODAL FILTER =======================
  const modalFilterCurrent = new bootstrap.Modal(document.getElementById('modal-filter-current'));
  const modalTitle = $('#modalFilterTitle');

  function openFilterCurrent(title = 'Filter Dashboard', type = '') {
    modalTitle.text(title);
    currentFilterType = type;
    modalFilterCurrent.show();
  }

  function closeFilterCurrent() {
    modalFilterCurrent.hide();
  }

  $('.btnFilterCurrent').on('click', function () {
    const title = $(this).data('title') || 'Filter Dashboard';
    const map = {
      desa: 'desa', kecamatan: 'kecamatan', kabupaten: 'kabupaten', provinsi: 'provinsi',
      karawang: 'karawang', warungbambu: 'warungbambu', kontrak: 'kontrak',
      direct: 'direct-indirect', sinas: 'sinas'
    };
    const found = Object.keys(map).find(k => title.toLowerCase().includes(k));
    openFilterCurrent(title, found ? map[found] : null);
  });

  $('.btnSubmitFilterCurrent').on('click', function () {
    if (currentFilterType) filterWilayah(currentFilterType);
    else Swal.fire({ icon: 'info', title: 'Pilih kategori filter dulu.' });
  });

  $('.closeFilterCurrent').on('click', closeFilterCurrent);

  // ======================= FILTER WILAYAH =======================
  function filterWilayah(type) {
    const sumber = $('#filterSumber').val();
     const departemen = $('#filterDepartemen').val(); // <-- ambil nilai departemen
    if (!sumber) {
      Swal.fire({
        icon: 'warning',
        title: 'Pilih Sumber Data!',
        text: 'Silakan pilih "Domisili" atau "Alamat" terlebih dahulu.',
      });
      return Promise.resolve();
    }

    let endpoint = `${base_url}/master-data/dashboard/rekap-${type}`;
    if (sumber === 'Alamat') endpoint += '-alamat';

    return new Promise((resolve) => {
      $.ajax({
        url: endpoint,
        method: 'GET',
        success: function (res) {
          const tableId = `#table-${type}`;
          const tbodyId = `#tabel-rekap-${type}`;
          const chartId = `#chart-${type}`;
          const chartKey = `chart${type.charAt(0).toUpperCase() + type.slice(1)}`;

          if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();
          if (window[chartKey]) { window[chartKey].destroy(); window[chartKey] = null; }

          let html = '', no = 1;

          if (['karawang', 'warungbambu'].includes(type)) {
            const dalam = res.dalam || [];
            const luar = res.luar || [];
            const label = type === 'karawang' ? 'Karawang' : 'Desa Warungbambu';
            const data = [
              { keterangan: `Dalam ${label} (${sumber})`, total: dalam.length },
              { keterangan: `Luar ${label} (${sumber})`, total: luar.length },
            ];
            data.forEach(i => html += `<tr><td>${no++}</td><td>${i.keterangan}</td><td>${i.total}</td></tr>`);
          } else if (['kontrak', 'direct-indirect', 'sinas'].includes(type)) {
            const data = res.data || [];
            data.forEach(i => {
              const label = i.keterangan || i.kategori || i.sinas || '-';
              html += `<tr><td>${no++}</td><td>${label}</td><td>${i.total || 0}</td></tr>`;
            });
          } else {
            const data = res.data || [];
            data.forEach(i => {
              const key = i[type] || '-';
              html += `<tr><td>${no++}</td><td>${key}</td><td>${i.total || 0}</td></tr>`;
            });
          }

          $(tbodyId).html(html);

          const table = $(tableId).DataTable({
            pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
          });

          const renderChart = () => {
            const rows = table.rows({ page: 'current' }).data().toArray();
            renderWilayahChart(chartId, chartKey, rows);
          };

          renderChart();
          $(tableId).on('draw.dt', renderChart);
          closeFilterCurrent();
          resolve();
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Gagal Memuat Data',
            text: `Terjadi kesalahan saat memuat data ${type}.`,
          });
          resolve();
        },
      });
    });
  }

  // ======================= AUTO LOAD =======================
$(document).ready(function () {
  $('#filterSumber').val('Domisili');

  const sections = [
    'desa',
    'kecamatan',
    'kabupaten',
    'provinsi',
    'karawang',
    'warungbambu',
    'kontrak',
    'direct-indirect',
    'sinas'
  ];

  // Jalankan semua filter sekaligus TANPA menunggu (instant parallel)
  sections.forEach(type => filterWilayah(type));
});


  // ======================= RESET FILTER =======================
  $('.btnResetFilterCurrent').on('click', function () {
    $('#filterSumber').val('');
    Swal.fire({
      icon: 'success',
      title: 'Filter direset',
      timer: 1000,
      showConfirmButton: false,
    });
  });

  // ======================= JALANKAN SEMUA =======================
  getDataKaryawan();
  turnoverChart();
  turnoverDetailChart();
  kontrakProgressChart();
  totalDataKaryawanByStatus();
});
