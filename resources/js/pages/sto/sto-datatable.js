
$(function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

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

  //SHOW TOAST
  function showToast(options) {
      const toast = Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 2000, 
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

    var columnsTable = [
        { data: 'customer_name' },
        { data: 'wh_name'},
        { data: 'locator_name'},
        { data: 'product_code' },
        { data: 'product_name' },
        { data: 'product_desc' },
        { data: 'model' },
        { data: 'qty_book' },
        { data: 'qty_count' },
        { data: 'balance' },
        { data: 'organization_id' },
        { data: 'processed' },
    ];

    var stoTable = $("#table-hasil-sto").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/sto/compare/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                dataFilter.wh_id = $('#wh_id').val();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON.data) {
                    var error = jqXHR.responseJSON.data.error;
                } else {
                    var message = jqXHR.responseJSON.message;
                    var errorLine = jqXHR.responseJSON.line;
                    var file = jqXHR.responseJSON.file;
                }
            },
        },
        responsive: true,
        lengthChange: false,
        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
        ],
    });

    $('#submit-filter').on('click', function() {
        stoTable.search('').draw();
    });


    $('#wh_id').select2({
        ajax: {
            url: '/sto/input_hasil/get_wh_label',
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                };
            },
            cache: true,
        },
    });

    $('#export-excel-button').on('click', function () {
        loadingSwalShow();
        let warehouseId = $('#wh_id').val(); // Ambil nilai filter
        let url = base_url + "/sto/compare/export-excel"; // URL endpoint Laravel
        let params = {
            wh_id: warehouseId, // Kirim filter jika ada
        };
    
        // Kirim request ke server untuk mengunduh file
        $.ajax({
            url: url,
            type: 'POST',
            data: params,
            xhrFields: {
                responseType: 'blob', // Mengunduh file sebagai blob
            },
            success: function (response) {
                // Membuat tautan download untuk file Excel
                let today = new Date();
                let day = ("0" + today.getDate()).slice(-2); // Menambahkan leading zero jika perlu
                let month = ("0" + (today.getMonth() + 1)).slice(-2); // Bulan dimulai dari 0
                let year = today.getFullYear();
                let dateString = `${year}-${month}-${day}`; // Format YYYY-MM-DD

                // Menentukan nama file berdasarkan tanggal hari ini
                let fileName = `Data_STO_${dateString}.xlsx`; 
                let link = document.createElement('a');
                let blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                let url = window.URL.createObjectURL(blob);
                link.href = url;
                link.download = fileName // Nama file
                link.click();
                window.URL.revokeObjectURL(url);
                loadingSwalClose();
            },
            error: function (error) {
                loadingSwalClose();
                showToast({ icon: "error", title: "Gagal mengunduh file Excel" });
            },
        });
    });
    




});