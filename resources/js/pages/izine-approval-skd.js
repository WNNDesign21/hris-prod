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

    function updateNotification(){
        $.ajax({
            url: base_url + '/get-notification',
            method: 'GET',
            success: function(response){
                $('.notifications-menu').html(response.data);
            }
        })
    }

    // function updatePengajuanCutiNotification(){
    //     $.ajax({
    //         url: base_url + '/get-lapor-skd-notification',
    //         method: 'GET',
    //         success: function(response){
    //             $('.notification-lapor-skd').html(response.data);
    //         }
    //     })
    // }
    
    // var columnsTable = [
    //     { data: "tanggal_mulai" },
    //     { data: "tanggal_selesai" },
    //     { data: "durasi" },
    //     { data: "keterangan" },
    //     { data: "lampiran" },
    //     { data: "approved_by" },
    //     { data: "legalized_by" },
    //     { data: "aksi" },
    // ];

    // var laporSkdTable = $("#lapor-skd-table").DataTable({
    //     search: {
    //         return: true,
    //     },
    //     order: [[0, "DESC"]],
    //     processing: true,
    //     serverSide: true,
    //     ajax: {
    //         url: base_url + "/izine/lapor-skd-datatable",
    //         dataType: "json",
    //         type: "POST",
    //         data: function (dataFilter) {
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             if (jqXHR.responseJSON.data) {
    //                 var error = jqXHR.responseJSON.data.error;
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: " <br>Application error!",
    //                     html:
    //                         '<div class="alert alert-danger text-left" role="alert">' +
    //                         "<p>Error Message: <strong>" +
    //                         error +
    //                         "</strong></p>" +
    //                         "</div>",
    //                     allowOutsideClick: false,
    //                     showConfirmButton: true,
    //                 }).then(function () {
    //                     refreshTable();
    //                 });
    //             } else {
    //                 var message = jqXHR.responseJSON.message;
    //                 var errorLine = jqXHR.responseJSON.line;
    //                 var file = jqXHR.responseJSON.file;
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: " <br>Application error!",
    //                     html:
    //                         '<div class="alert alert-danger text-left" role="alert">' +
    //                         "<p>Error Message: <strong>" +
    //                         message +
    //                         "</strong></p>" +
    //                         "<p>File: " +
    //                         file +
    //                         "</p>" +
    //                         "<p>Line: " +
    //                         errorLine +
    //                         "</p>" +
    //                         "</div>",
    //                     allowOutsideClick: false,
    //                     showConfirmButton: true,
    //                 }).then(function () {
    //                     refreshTable();
    //                 });
    //             }
    //         },
    //     },
    //     initComplete: function () {
    //         $('.image-popup-vertical-fit').magnificPopup({
    //             type: 'image',
    //             closeOnContentClick: true,
    //             mainClass: 'mfp-img-mobile',
    //             image: {
    //                 verticalFit: true
    //             }
    //         });
    //     },
    //     drawCallback: function () { 
    //         $('.image-popup-vertical-fit').magnificPopup({
    //             type: 'image',
    //             closeOnContentClick: true,
    //             mainClass: 'mfp-img-mobile',
    //             image: {
    //                 verticalFit: true
    //             }
    //         });
    //     },
    //     // responsive: true,
    //     scrollX: true,
    //     columns: columnsTable,
    //     columnDefs: [
    //         {
    //             orderable: false,
    //             targets: [-1],
    //         },
    //         {
    //             targets: [-1],
    //             createdCell: function (td, cellData, rowData, row, col) {
    //                 // $(td).addClass("text-center");
    //             },
    //         },
    //     ],
    // })

    // //REFRESH TABLE
    // function refreshTable() {
    //     laporSkdTable.search("").draw();
    // }

    // //RELOAD TABLE
    // $('.btnReload').on("click", function (){
    //     refreshTable();
    // })

    // $('.btnAdd').on("click", function (){
    //     openInputForm();
    // })

    // $('.btnClose').on("click", function (){
    //     closeInputForm();
    // })
});