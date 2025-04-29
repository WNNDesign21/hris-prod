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

    //DATATABLE USERANISASI
    var columnsTable = [
        { data: "log_name" },
        { data: "description" },
        { data: "causer" },
        { data: "created_at" }
    ];

    var activitylogTable = $("#activity-log-table").DataTable({
        search: {
            return: true,
        },
        order: [[3, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/superuser/activity-log/datatable",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
                var createdAt = $('#created_at').val();
                var causer = $('#causer').val();
                dataFilter.createdAt = createdAt;
                dataFilter.causer = causer;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON.data) {
                    var error = jqXHR.responseJSON.data.error;
                    Swal.fire({
                        icon: "error",
                        title: " <br>Application error!",
                        html:
                            '<div class="alert alert-danger text-left" role="alert">' +
                            "<p>Error Message: <strong>" +
                            error +
                            "</strong></p>" +
                            "</div>",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    });
                } else {
                    var message = jqXHR.responseJSON.message;
                    var errorLine = jqXHR.responseJSON.line;
                    var file = jqXHR.responseJSON.file;
                    Swal.fire({
                        icon: "error",
                        title: " <br>Application error!",
                        html:
                            '<div class="alert alert-danger text-left" role="alert">' +
                            "<p>Error Message: <strong>" +
                            message +
                            "</strong></p>" +
                            "<p>File: " +
                            file +
                            "</p>" +
                            "<p>Line: " +
                            errorLine +
                            "</p>" +
                            "</div>",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    });
                }
            },
        },
        columns: columnsTable
    });

    //REFRESH TABLE
    function refreshTable() {
        activitylogTable.search("").draw();
    }

    //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('#created_at').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#created_at').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + '|' + picker.endDate.format('YYYY-MM-DD'));
        refreshTable();
    });

    $('#created_at').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        activitylogTable.ajax.reload();
    });

    $('#causer').select2({
        clearable: true,
        placeholder: "Pilih User",
        allowClear: true,
        ajax: {
            url: base_url + "/superuser/activity-log/causer",
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
    }).on("select2:select", function (e) {
        var data = e.params.data;
        if (data.id) {
            refreshTable();
        }
    }).on("select2:unselect", function (e) {
        $('#causer').val('').trigger('change');
        refreshTable();
    });
});
