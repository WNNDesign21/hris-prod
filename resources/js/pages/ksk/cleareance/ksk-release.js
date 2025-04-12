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

    var modalInputOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInput = new bootstrap.Modal(
        document.getElementById("modal-input"),
        modalInputOptions
    );

    function openInput() {
        modalInput.show();
    }

    function closeInput() {
        modalInput.hide();
    }

    function resetInput() {
        $('#atasan_langsung').val('');
        $('#dept_it').val('');
        $('#dept_fat').val('');
        $('#dept_ga').val('');
        $('#dept_hr').val('');
        $('#form-input').attr('action', )
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

    function updateKskNotification(){
        $.ajax({
            url: base_url + '/ajax/ksk/get-ksk-notification',
            method: 'GET',
            success: function(response){
                $('.notification-release').html(response.html_release);
                $('.notification-approval').html(response.html_approval);
                $('.notification-cleareance').html(response.html_cleareance);
            }, error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    var columnsUnreleasedTable = [
        { data: "karyawan" },
        { data: "departemen" },
        { data: "jabatan" },
        { data: "posisi" },
        { data: "action" }
    ];

    var unreleasedTable = $("#unreleased-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        stateSave: !0,
        ajax: {
            url: base_url + "/ksk/cleareance/release/datatable-unreleased",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
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
                    })
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
        // responsive: true,
        scrollX: true,
        columns: columnsUnreleasedTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-1],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    var columnsReleasedTable = [
        { data: "id_cleareance" },
        { data: "karyawan" },
        { data: "departemen" },
        { data: "jabatan" },
        { data: "posisi" },
        { data: "tanggal_akhir_bekerja" },
        { data: "approval" },
        { data: "status" },
    ];

    var releasedTable = $("#released-table").DataTable({
        search: {
            return: true,
        },
        order: [[2, "DESC"]],
        processing: true,
        serverSide: true,
        stateSave: !0,
        ajax: {
            url: base_url + "/ksk/cleareance/release/datatable-released",
            dataType: "json",
            type: "POST",
            data: function (dataFilter) {
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
        // responsive: true,
        scrollX: true,
        columns: columnsReleasedTable,
        columnDefs: [
            {
                orderable: false,
                targets: [-2],
            },
            // {
            //     targets: [-1],
            //     createdCell: function (td, cellData, rowData, row, col) {
            //         // $(td).addClass("text-center");
            //     },
            // },
        ],
    })

    function refreshTable() {
        var searchValueUnreleased = unreleasedTable.search();
        if (searchValueUnreleased) {
            unreleasedTable.search(searchValueUnreleased).draw();
        } else {
            unreleasedTable.search("").draw();
        }

        var searchValueReleased = releasedTable.search();
        if (searchValueReleased) {
            releasedTable.search(searchValueReleased).draw();
        } else {
            releasedTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnClose').on("click", function (){
        resetInput();
        closeInput();
    })

    $('#unreleased-table').on('click', '.btnRelease', function (e) {
        let idKSKDetail = $(this).data('id-ksk-detail');
        let karyawanId = $(this).data('karyawan-id');
        let url = base_url + "/ksk/cleareance/release/update/" + idKSKDetail;
        $('#form-input').attr('action', url);
        openInput();

        $('#atasan_langsung').select2({
            dropdownParent: $('#modal-input'),
            ajax: {
            url: base_url + "/ksk/cleareance/ajax/release/get-atasan-langsung",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                search: params.term || "",
                page: params.page || 1,
                id_karyawan: karyawanId
                };
            },
            cache: true,
            },
        });
    });

    $('#dept_it').select2({
        dropdownParent: $('#modal-input'),
        ajax: {
            url: base_url + "/ksk/cleareance/ajax/release/get-karyawans",
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

    $('#dept_fat').select2({
        dropdownParent: $('#modal-input'),
        ajax: {
            url: base_url + "/ksk/cleareance/ajax/release/get-karyawans",
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

    $('#dept_ga').select2({
        dropdownParent: $('#modal-input'),
        ajax: {
            url: base_url + "/ksk/cleareance/ajax/release/get-karyawans",
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

    $('#dept_hr').select2({
        dropdownParent: $('#modal-input'),
        ajax: {
            url: base_url + "/ksk/cleareance/ajax/release/get-karyawans",
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

    $('#form-input').on('submit', function (e) {
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
        console.log(url);
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                showToast({ title: data.message });
                refreshTable();
                closeInput();
                loadingSwalClose();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        var target = $(e.target).attr("href");
        if ($(target).find("table").hasClass("dataTable")) {
            if (!$.fn.DataTable.isDataTable($(target).find("table"))) {
                $(target).find("table").DataTable();
            } else {
                $(target).find("table").DataTable().columns.adjust().draw();
            }
        }
    });
});
