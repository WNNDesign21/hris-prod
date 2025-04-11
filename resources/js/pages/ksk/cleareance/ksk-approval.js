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

    var modalDetailOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalDetail = new bootstrap.Modal(
        document.getElementById("modal-detail"),
        modalDetailOptions
    );

    function openDetail() {
        modalDetail.show();
    }

    function closeDetail() {
        modalDetail.hide();
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
});
