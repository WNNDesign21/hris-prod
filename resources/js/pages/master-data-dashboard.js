$(function () {
  'use strict';

  // ======================= GLOBAL VAR =======================
  let loadingSwal;
  let currentFilterType = null; // Untuk deteksi konteks tombol filter (Desa/Kecamatan/Kabupaten/Provinsi)

  // ======================= SWEETALERT LOADING =======================
  function loadingSwalShow() {
    loadingSwal = Swal.fire({
      title: '<i class="fas fa-sync-alt fa-spin fs-80"></i>',
      showConfirmButton: false,
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
        chart: { type: 'bar', height: '100%', stacked: true },
        colors: ['#6993ff'],
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
        plotOptions: { bar: { columnWidth: '30%', borderRadius: 4 } },
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

  // ======================= RENDER DONUT CHART =======================
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
          donut: {
            size: '70%',
            labels: { show: true, total: { show: true, label: 'Total', formatter: () => total } },
          },
        },
      },
      legend: { position: 'bottom', horizontalAlign: 'center' },
    };

    if (window[chartKey]) window[chartKey].destroy();
    window[chartKey] = new ApexCharts(document.querySelector(chartSelector), options);
    window[chartKey].render();
  }

  // ======================= MODAL FILTER =======================
  var modalFilterCurrent = new bootstrap.Modal(document.getElementById('modal-filter-current'));
  var modalTitle = $('#modalFilterTitle');

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
    const type = title.toLowerCase().includes('desa') ? 'desa'
                : title.toLowerCase().includes('kecamatan') ? 'kecamatan'
                : title.toLowerCase().includes('kabupaten') ? 'kabupaten'
                : title.toLowerCase().includes('provinsi') ? 'provinsi'
                : title.toLowerCase().includes('karawang') ? 'karawang'
                : title.toLowerCase().includes('warungbambu') ? 'warungbambu'
                : null;
    openFilterCurrent(title, type);
  });
$('.btnSubmitFilterCurrent').on('click', function () {
  console.log('Tombol Filter ditekan, type:', currentFilterType);
  if (currentFilterType) filterWilayah(currentFilterType);
  else Swal.fire({ icon: 'info', title: 'Pilih kategori filter dulu.' });
});

  $('.closeFilterCurrent').on('click', function () {
    closeFilterCurrent();
  });

  // ======================= FUNGSI FILTER WILAYAH =======================
  function filterWilayah(type) {
  const sumber = $('#filterSumber').val();
  if (!sumber) {
    Swal.fire({
      icon: 'warning',
      title: 'Pilih Sumber Data!',
      text: 'Silakan pilih "Domisili" atau "Alamat" terlebih dahulu.',
    });
    return;
  }

  let endpoint = `${base_url}/master-data/dashboard/rekap-${type}`;
  if (sumber === 'Alamat') endpoint += '-alamat';

  // === KASUS KHUSUS: KARAWANG ===
  // === KASUS KHUSUS: KARAWANG ===
if (type === 'karawang') {
  Swal.fire({
    title: `Memuat Data KARAWANG (${sumber})...`,
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  $.ajax({
    url: endpoint,
    method: 'GET',
    success: function (res) {
      Swal.close();
      console.log(`Respon dari server Karawang [${sumber}]`, res);

      const dalam = res.dalam || [];
      const luar = res.luar || [];

      const data = [
        { keterangan: `Dalam Karawang (${sumber})`, total: dalam.length },
        { keterangan: `Luar Karawang (${sumber})`, total: luar.length },
      ];

      // === Reset tabel & chart ===
      const tableId = '#table-karawang';
      const tbodyId = '#tabel-rekap-karawang';
      const chartId = '#chart-karawang';
      const chartKey = 'chartKarawang';

      // Hapus chart lama
      if (window[chartKey]) {
        try { window[chartKey].destroy(); } catch (e) {}
        window[chartKey] = null;
      }

      // Hapus tabel lama
      if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().clear().destroy();
      }

      // === Render tabel baru ===
      let html = '';
      let no = 1;
      data.forEach(item => {
        html += `<tr><td>${no++}</td><td>${item.keterangan}</td><td>${item.total}</td></tr>`;
      });
      $(tbodyId).html(html);

      // === Inisialisasi ulang DataTable ===
      const table = $(tableId).DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
      });

      // === Render chart baru ===
      const rows = table.rows({ page: 'current' }).data().toArray();
      renderWilayahChart(chartId, chartKey, rows);

      closeFilterCurrent();
    },
    error: function (xhr) {
      Swal.close();
      console.error('Error response:', xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memuat Data Karawang',
        text: `Terjadi kesalahan saat memuat data Karawang (${sumber}).`,
      });
    },
  });

  return; // stop ke blok umum
}

// === KASUS KHUSUS: WARUNGBAMBU ===
// === KASUS KHUSUS: WARUNGBAMBU ===
if (type === 'warungbambu') {
  const sumber = $('#filterSumber').val();
  if (!sumber) {
    Swal.fire({
      icon: 'warning',
      title: 'Pilih Sumber Data!',
      text: 'Silakan pilih "Domisili" atau "Alamat" terlebih dahulu.',
    });
    return;
  }

  Swal.fire({
    title: `Memuat Data WARUNGBAMBU (${sumber})...`,
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  // Tentukan endpoint sesuai sumber
  let endpoint = `${base_url}/master-data/dashboard/rekap-warungbambu`;
  if (sumber === 'Alamat') endpoint += '-alamat';

  $.ajax({
    url: endpoint,
    method: 'GET',
    success: function (res) {
      Swal.close();
      console.log('Respon Warungbambu:', res);

      const dalam = res.dalam || [];
      const luar = res.luar || [];

      const data = [
        { keterangan: `Dalam Desa Warungbambu (${sumber})`, total: dalam.length },
        { keterangan: `Luar Desa Warungbambu (${sumber})`, total: luar.length },
      ];

      // === Hapus chart lama dan tabel lama ===
      const chartKey = 'chartWarungbambu';
      const tableId = '#table-warungbambu';
      const tbodyId = '#tabel-rekap-warungbambu';

      if (window[chartKey]) {
        window[chartKey].destroy();
        window[chartKey] = null;
      }

      if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
      }

      // Render tabel baru
      let html = '', no = 1;
      data.forEach(item => {
        html += `<tr><td>${no++}</td><td>${item.keterangan}</td><td>${item.total}</td></tr>`;
      });
      $(tbodyId).html(html);

      // Render DataTable baru
      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      // Render chart baru
      const renderChart = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-warungbambu', chartKey, rows);
      };

      renderChart();
      $(tableId).on('draw.dt', renderChart);

      closeFilterCurrent();
    },
    error: function () {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memuat Data Warungbambu',
        text: 'Terjadi kesalahan saat memuat data filter Warungbambu.',
      });
    },
  });

  return; // stop agar tidak lanjut ke blok umum
}


  // === BLOK UMUM UNTUK DESA / KECAMATAN / KABUPATEN / PROVINSI ===
  Swal.fire({
    title: `Memuat Data ${type.toUpperCase()}...`,
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  const tableId = `#table-${type}`;
  const tbodyId = `#tabel-rekap-${type}`;
  const chartId = `#chart-${type}`;
  const chartKey = `chart${type.charAt(0).toUpperCase() + type.slice(1)}`;

  $(tbodyId).html('<tr><td colspan="3" class="text-center">Memuat data...</td></tr>');
  if (window[chartKey]) { window[chartKey].destroy(); window[chartKey] = null; }
  if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

  $.ajax({
    url: endpoint,
    method: 'GET',
    success: function (res) {
      Swal.close();
      const data = res.data || [];
      let html = '', no = 1;
      data.forEach(i => {
        const key = i[type] || '-';
        html += `<tr><td>${no++}</td><td>${key}</td><td>${i.total || 0}</td></tr>`;
      });
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
    },
    error: function () {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Gagal Memuat Data',
        text: `Terjadi kesalahan saat memuat data ${type}.`,
      });
    },
  });
}

  // ======================= FUNGSI LAINNYA (KARAWANG, KONTRAK, SINAS, DLL) =======================
  function rekapGabunganKarawang() {
    $.get(`${base_url}/master-data/dashboard/rekap-karawang`, function (res) {
      const dalam = res.dalam || [];
      const luar = res.luar || [];

      const data = [
        { keterangan: 'Dalam Karawang', total: dalam.length },
        { keterangan: 'Luar Karawang', total: luar.length },
      ];

      let html = '';
      let no = 1;
      data.forEach(item => {
        html += `<tr><td>${no++}</td><td>${item.keterangan}</td><td>${item.total}</td></tr>`;
      });
      $('#tabel-rekap-karawang').html(html);

      const tableId = '#table-karawang';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-karawang', 'chartKarawang', rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  function rekapKontrakTable() {
    $.get(`${base_url}/master-data/dashboard/rekap-kontrak`, function (res) {
      const data = res.data || [];
      let html = '', no = 1;
      data.forEach(i => html += `<tr><td>${no++}</td><td>${i.keterangan}</td><td>${i.total}</td></tr>`);
      $('#tabel-rekap-kontrak').html(html);

      const tableId = '#table-kontrak';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-kontrak', 'chartKontrak', rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  function rekapDirectIndirectTable() {
    $.get(`${base_url}/master-data/dashboard/rekap-direct-indirect`, function (res) {
      const data = res.data || [];
      let html = '', no = 1;
      data.forEach(i => html += `<tr><td>${no++}</td><td>${i.kategori}</td><td>${i.total}</td></tr>`);
      $('#tabel-rekap-direct-indirect').html(html);

      const tableId = '#table-direct-indirect';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-direct-indirect', 'chartDirectIndirect', rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  function rekapSinas() {
    $.get(`${base_url}/master-data/dashboard/rekap-sinas`, function (res) {
      const data = res.data || [];
      let html = '', no = 1;
      data.forEach(i => html += `<tr><td>${no++}</td><td>${i.sinas}</td><td>${i.total}</td></tr>`);
      $('#tabel-rekap-sinas').html(html);

      const tableId = '#table-sinas';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-sinas', 'chartSinas', rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  function rekapWarungbambuTable() {
    $.get(`${base_url}/master-data/dashboard/rekap-warungbambu`, function (res) {
      const data = [
        { keterangan: 'Dalam Desa Warung Bambu', total: res.dalam },
        { keterangan: 'Luar Desa Warung Bambu', total: res.luar },
      ];

      let html = '', no = 1;
      data.forEach(i => html += `<tr><td>${no++}</td><td>${i.keterangan}</td><td>${i.total}</td></tr>`);
      $('#tabel-rekap-warungbambu').html(html);

      const tableId = '#table-warungbambu';
      if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

      const table = $(tableId).DataTable({
        pageLength: 5, lengthChange: false, searching: false, ordering: true, info: true,
      });

      const render = () => {
        const rows = table.rows({ page: 'current' }).data().toArray();
        renderWilayahChart('#chart-warungbambu', 'chartWarungbambu', rows);
      };
      render();
      $(tableId).on('draw.dt', render);
    });
  }

  // ======================= AUTO LOAD DOMISILI =======================
  $(document).ready(function () {
    $('#filterSumber').val('Domisili');

    ['karawang','warungbambu', 'desa', 'kecamatan', 'kabupaten', 'provinsi'].forEach(t => filterWilayah(t));

    // rekapGabunganKarawang();
    rekapKontrakTable();
    rekapDirectIndirectTable();
    rekapSinas();
    rekapWarungbambuTable();
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
