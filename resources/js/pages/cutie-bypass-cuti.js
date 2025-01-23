$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

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

    function calculateDuration(start, end) {
        var startDate = new Date(start);
        var endDate = new Date(end);
        var timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
        var duration = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
        return duration;
    }

    function formReset() {
        $('#id_karyawan').val('').trigger('change');
        $('#penggunaan_sisa_cuti').val('TB').trigger('change');
        $('#rencana_mulai_cuti').val('');
        $('#rencana_selesai_cuti').val('').attr('min', '');
        $('#alasan_cuti').val('');
    }

    //SELECT2 
    $('#penggunaan_sisa_cuti').select2()

    $('#id_karyawan').select2({
        ajax: {
            url: base_url + "/master-data/karyawan/get-data-karyawan",
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

    //RENCANA MULAI CUTI
    $('#rencana_mulai_cuti').on('change', function() {
        var rencanaMulaiCuti = $(this).val();
        $('#rencana_selesai_cuti').val('').attr('min', rencanaMulaiCuti);
    });

    $('#form-bypass-cuti').on('submit', function (e){
        loadingSwalShow();
        e.preventDefault();
        let url = $('#form-bypass-cuti').attr('action');
        let start = $('#rencana_mulai_cuti').val();
        let end = $('#rencana_selesai_cuti').val();
        let duration = calculateDuration(start, end);

        var formData = new FormData($('#form-bypass-cuti')[0]);
        formData.append('durasi_cuti', duration);
        
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                loadingSwalClose();
                formReset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });
});