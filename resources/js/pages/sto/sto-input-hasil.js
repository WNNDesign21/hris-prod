$(function () {
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


    $('#product_id').on('change', function() {
        let partCode = $(this).val();
        
        if (partCode) {
            $.ajax({
                url: '/sto/input_hasil/get_part/' + partCode,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#part_name').val(data.name);
                    $('#part_code').val(data.value);
                    $('#part_desc').val(data.description);
                    if (data.classification) {
                        $('#model').val(data.classification).trigger('change');
                    }
                    // if (data.partner_name) {
                    //     $('#partner_name').val(data.partner_name).trigger('change');
                    // }
                },
                error: function() {
                    alert('Error retrieving data.');
                }
            });
        }
    });

    $('#no_label').on('change', function() {
        let noLabel = $(this).val();
        
        if (noLabel) {
            $.ajax({
                url: '/sto/input_hasil/get_wh/' + noLabel,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#wh_name').val(data.wh_name);
                },
                error: function() {
                    alert('Error retrieving data.');
                }
            });
        }
    });

    $('#no_label').select2({
        ajax: {
            url: '/sto/input_hasil/get_warehouse',
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


    $('#product_id' ).select2({
        ajax: {
            url: '/sto/input_hasil/get_part',
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



    $('#form-hasil-sto').on('submit', function (e) {
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
                $('#form-hasil-sto')[0].reset();
                $('#customer').select2('destroy'); 
                $('#no_label').select2('destroy'); 
                $('#product_id').select2('destroy'); 
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });

    $('#customer').select2({
        ajax: {
            url: '/sto/input_hasil/get_customer',
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
    $('#no_label').select2({
        ajax: {
            url: '/sto/input_hasil/get_no_label',
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


});

