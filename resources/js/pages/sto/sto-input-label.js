$(function(){

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

  // DATATABLE
  var columnsTable = [
      { data: 'no_label' },
      { data: 'issued_name' },
      { data: 'wh_name' },
      { data: 'created_at' },
  ];

  var labelTable =
  $("#table-register-label").DataTable({
      search: {
          return: true,
      },
      order: [[0, "ASC"]],
      processing: true,
      serverSide: true,
      ajax: {
          url: base_url + "/sto/input_label/datatable",
          dataType: "json",
          type: "POST",
          data: function (dataFilter) {
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
      columns: columnsTable,
      dom: 'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ]
  });

  function refreshTable() {
      var searchValue = labelTable.search();
      if (searchValue) {
          labelTable.search(searchValue).draw();
      } else {
          labelTable.search("").draw();
      }
  }
  
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

  $('#form-label-sto').on('submit', function (e) {
    e.preventDefault(); 
    loadingSwalShow();
    let formData = $(this).serialize();

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        success: function (data) {
            loadingSwalClose();
            showToast({ title: data.message });
            $('#form-label-sto')[0].reset();
            $('#wh_id').val('').trigger('change'); 
            refreshTable();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          loadingSwalClose();
            showToast({ icon: "error", title: jqXHR.responseJSON.message });
        },
    });
  });

})