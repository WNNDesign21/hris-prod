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

    // DATATABLE
    var columnsTable = [
        { data: 'no_label' },
        { data: 'customer_name' },
        { data: 'wh_name' },
        { data: 'part_code' },
        { data: 'part_name' },
        { data: 'part_number' },
        { data: 'quantity' },
        { data: 'identitas_lot' },
        { data: 'updated_at' },
        { data: 'action' },
    ];

    var hasilTable =
    $("#table-hasil-sto").DataTable({
        search: {
            return: true,
        },
        order: [[0, "ASC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/sto/input_hasil/datatable",
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
        var searchValue = hasilTable.search();
        if (searchValue) {
            hasilTable.search(searchValue).draw();
        } else {
            hasilTable.search("").draw();
        }
    }


    $('#product_id').on('change', function() {
        let partCode = $(this).val();
        
        if (partCode) {
            $.ajax({
                url: base_url + '/sto/input_hasil/get_part/' + partCode,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#part_name').val(data.name);
                    $('#part_code').val(data.value);
                    $('#part_desc').val(data.description);
                    $('#quantity_uom').text(data.uom ?? '');
                    if (data.classification) {
                        $('#model').val(data.classification).trigger('change');
                    } else {
                        $('#model').val('').trigger('change');
                    }

                    if (data.partner_id){
                        $('#customer').append('<option value="'+data.partner_id+'" selected>'+data.partner_name+'</option>');
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
                        }).val(data.partner_id).trigger('change');
                    } else {
                        $('#customer').val(null).trigger('change');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                },
            });
        }
    });

    $('#quantity').on('input', function() {
        let quantity = $(this).val();
        console.log(quantity);
        quantity = quantity.replace(/^0+/, '');
        if (quantity === '' || parseInt(quantity) < 1) {
            quantity = 0;
        }
        $(this).val(quantity);
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
                error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
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
                refreshTable();
                $('#form-hasil-sto')[0].reset();
                $('#customer').val('').trigger('change'); 
                $('#no_label').val('').trigger('change'); 
                $('#product_id').val('').trigger('change');
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

    $('#table-hasil-sto').on('click', '.btnEdit', function() {
        let idStoLine = $(this).data('id');
        $.ajax({
            url: base_url + '/sto/input_hasil/get_sto_line/' + idStoLine,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    })
});

