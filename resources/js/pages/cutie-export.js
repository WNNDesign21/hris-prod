$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    //LOADING SWALL
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

    $('#departemen_id').select2();
    $('#to').attr('readonly', true);

    $('#from').on('change', function() {
        let from = $(this).val();
        $('#to').attr('readonly', false);
        $('#to').attr('min', from);
    });

    $('#btnExport').on('click', function() {
        let to = $('#to').val();

        if(to == '') {
            showToast({ icon: 'error', title: 'Masukkan batas tanggal data cuti!'});
            return;
        }
        $('#form-export-cuti').submit();
    });

});