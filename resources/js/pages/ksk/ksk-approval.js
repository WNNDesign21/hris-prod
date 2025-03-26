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

    var modalApprovalOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalApproval = new bootstrap.Modal(
        document.getElementById("modal-approval"),
        modalApprovalOptions
    );

    function openApproval() {
        modalApproval.show();
    }

    function closeApproval() {
        modalApproval.hide();
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

    var columnsReleasedTable = [
        { data: "id_ksk" },
        { data: "nama_divisi" },
        { data: "nama_departemen" },
        { data: "parent_name" },
        { data: "release_date" },
        { data: "released_by" },
        { data: "checked_by" },
        { data: "approved_by" },
        { data: "reviewed_div_by" },
        { data: "reviewed_ph_by" },
        { data: "reviewed_dir_by" },
        { data: "legalized_by" },
    ];

    var mustApprovedTable = $("#must-approved-table").DataTable({
        search: {
            return: true,
        },
        order: [[3, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/approval/datatable-must-approved",
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
                    }).then(function () {
                        refreshTable();
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
                    }).then(function () {
                        refreshTable();
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

    // //REFRESH TABLE
    function refreshTable() {
        var searchValueMustApproved = mustApprovedTable.search();
        if (searchValueMustApproved) {
            mustApprovedTable.search(searchValueMustApproved).draw();
        } else {
            mustApprovedTable.search("").draw();
        }

        // var searchValueReleased = mustApprovedTable.search();
        // if (searchValueReleased) {
        //     mustApprovedTable.search(searchValueReleased).draw();
        // } else {
        //     mustApprovedTable.search("").draw();
        // }
    }

    $('#must-approved-table').on('click', '.btnApproved', function(){
        let idKsk = $(this).data('id-ksk');
        let url = base_url + '/ksk/ajax/approval/get-ksk/' + idKsk;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response){
                let html = response.html;
                $('#modal-approval-body').empty().html(html);
                $('.select2').select2({
                    dropdownParent: $('#modal-approval')
                });
                openApproval();
            },
            error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    });
});
