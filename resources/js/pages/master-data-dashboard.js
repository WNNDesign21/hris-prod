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

   // ======================= REKAP DALAM & LUAR KARAWANG =======================
function rekapGabunganKarawang() {
  const url = base_url + '/master-data/dashboard/rekap-karawang';

  $.ajax({
    url: url,
    method: 'GET',
    success: function (response) {
      const dalam = response.dalam || [];
      const luar = response.luar || [];

      // Gabung data
      const data = [
        { keterangan: 'Dalam Karawang', total: dalam.length },
        { keterangan: 'Luar Karawang', total: luar.length },
      ];

      // === Render tabel ===
      let html = '';
      let no = 1;
      data.forEach(item => {
        html += `
          <tr>
            <td>${no++}</td>
            <td>${item.keterangan}</td>
            <td>${item.total}</td>
          </tr>`;
      });
      $('#tabel-rekap-karawang').html(html);

      // === Inisialisasi DataTables ===
      const tableId = '#table-karawang';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
      });

      // === Render donut chart (seperti rekap lain) ===
      const renderChart = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-karawang', 'chartKarawang', rows);
      };

      renderChart();
      $(tableId).on('draw.dt', renderChart);
    },
    error: function (xhr) {
      console.error('Gagal memuat data Karawang:', xhr);
    }
  });
}

// ======================= REKAP KONTRAK (TABEL + DONUT) =======================
function rekapKontrakTable() {
  const url = base_url + '/master-data/dashboard/rekap-kontrak';

  $.ajax({
    url: url,
    method: 'GET',
    success: function (response) {
      const data = response.data || [];

      // === Render tabel ===
      let html = '';
      let no = 1;
      data.forEach(item => {
        html += `
          <tr>
            <td>${no++}</td>
            <td>${item.keterangan}</td>
            <td>${item.total}</td>
          </tr>`;
      });
      $('#tabel-rekap-kontrak').html(html);

      // === Inisialisasi DataTables ===
      const tableId = '#table-kontrak';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
      });

      // === Render donut chart (seragam) ===
      const renderChart = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-kontrak', 'chartKontrak', rows);
      };

      renderChart();
      $(tableId).on('draw.dt', renderChart);
    },
    error: function (xhr) {
      console.error('Gagal memuat data Kontrak:', xhr);
    }
  });
}

// ======================= FUNGSI RENDER DONUT CHART (SAMA UNTUK SEMUA) =======================
function renderWilayahChart(chartSelector, chartKey, pageData) {
  if (!pageData || !pageData.length) return;

  // DataTables mengembalikan array [No, Nama, Total]
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
        donut: {
          size: '70%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Total',
              formatter: () => total,
            },
          },
        },
      },
    },
    legend: { position: 'bottom', horizontalAlign: 'center' },
  };

  if (window[chartKey]) window[chartKey].destroy();
  window[chartKey] = new ApexCharts(document.querySelector(chartSelector), options);
  window[chartKey].render();
}
// ======================= REKAP DIRECT / INDIRECT =======================
function rekapDirectIndirectTable() {
  let url = base_url + '/master-data/dashboard/rekap-direct-indirect';

  $.ajax({
    url: url,
    method: 'GET',
    success: function (response) {
      const data = response.data || [];
      let html = '', no = 1;

      // === Render tabel ===
      data.forEach(item => {
        html += `
          <tr>
            <td>${no++}</td>
            <td>${item.kategori}</td>
            <td>${item.total}</td>
          </tr>
        `;
      });

      $('#tabel-rekap-direct-indirect').html(html);

      // === DataTables pagination ===
      if ($.fn.DataTable.isDataTable('#table-direct-indirect')) {
        $('#table-direct-indirect').DataTable().destroy();
      }

      const table = $('#table-direct-indirect').DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
      });

      // === Render chart ===
      const total = data.reduce((a, b) => a + b.total, 0);
      const options = {
        series: data.map(i => i.total),
        labels: data.map(i => i.kategori),
        chart: { type: 'donut', height: 280 },
        colors: ['#008FFB', '#FEB019'],
        plotOptions: {
          pie: {
            donut: {
              size: '70%',
              labels: {
                show: true,
                total: {
                  show: true,
                  label: 'Total',
                  formatter: () => total
                }
              }
            }
          }
        },
        legend: {
          position: 'bottom',
          horizontalAlign: 'center'
        }
      };

      if (window.chartDirectIndirect) window.chartDirectIndirect.destroy();
      window.chartDirectIndirect = new ApexCharts(
        document.querySelector("#chart-direct-indirect"),
        options
      );
      window.chartDirectIndirect.render();
    },
    error: function (xhr) {
      console.error('Gagal memuat data Direct/Indirect:', xhr);
    }
  });
}


function rekapWarungbambuTable() {
  let url = base_url + '/master-data/dashboard/rekap-warungbambu';

  $.ajax({
    url: url,
    method: 'GET',
    success: function (response) {
      const data = [
        { keterangan: 'Dalam Desa Warung Bambu', total: response.dalam },
        { keterangan: 'Luar Desa Warung Bambu', total: response.luar },
      ];

      // ==== RENDER TABEL ====
      let html = '';
      let no = 1;
      data.forEach(item => {
        html += `
          <tr>
            <td>${no++}</td>
            <td>${item.keterangan}</td>
            <td>${item.total}</td>
          </tr>
        `;
      });
      $('#tabel-rekap-warungbambu').html(html);

      // ==== DATA TABLE PAGINATION ====
      if ($.fn.DataTable.isDataTable('#table-warungbambu')) {
        $('#table-warungbambu').DataTable().destroy();
      }

      const table = $('#table-warungbambu').DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
      });

      // Render chart pertama kali (halaman awal)
      renderWarungbambuChart(table.rows({ page: 'current' }).data().toArray());

      // Update chart ketika ganti halaman
      $('#table-warungbambu').on('draw.dt', function () {
        const currentPageData = table.rows({ page: 'current' }).data().toArray();
        renderWarungbambuChart(currentPageData);
      });
    },
    error: function (xhr) {
      console.error('Gagal memuat data Warung Bambu:', xhr);
    }
  });
}

// ==== FUNGSI RENDER DONUT ====
function renderWarungbambuChart(pageData) {
  if (!pageData.length) return;

  const labels = pageData.map(i => i[1]);
  const values = pageData.map(i => parseInt(i[2]));
  const total = values.reduce((a, b) => a + b, 0);

  const options = {
    series: values,
    labels: labels,
    chart: { type: 'donut', height: 280 },
    colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A'], // warna seragam
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Total',
              formatter: () => total,
            }
          }
        }
      }
    },
    legend: {
      position: 'bottom',
      horizontalAlign: 'center'
    },
  };

  if (window.chartWarungbambu) window.chartWarungbambu.destroy();
  window.chartWarungbambu = new ApexCharts(document.querySelector("#chart-warungbambu"), options);
  window.chartWarungbambu.render();
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
  rekapGabunganKarawang();
  rekapKontrakTable();
  rekapDirectIndirectTable();
  
  rekapWarungbambuTable();
});
