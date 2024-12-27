$(function(){

  $.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });

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
    e.preventDefault(); // Prevent form submission

    // Ambil data form
    let formData = $(this).serialize();

    // Kirim data dengan AJAX
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        success: function (data) {
            showToast({ title: data.message });
            $('#form-label-sto')[0].reset();
            $('#wh_id').select2('destroy'); 
        },
        error: function (jqXHR, textStatus, errorThrown) {
            showToast({ icon: "error", title: jqXHR.responseJSON.message });
        },
    });
  });

})