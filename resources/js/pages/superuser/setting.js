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

    $("#btnReset").on("click", function () {
        $.ajax({
            url: base_url + "/superuser/setting/reset-logo",
            type: "GET",
            beforeSend: function () {
                loadingSwalShow();
            },
            success: function (data) {
                const input = document.getElementById("app_logo");
                input.value = "";
                loadingSwalClose();
                showToast({ title: data.message });
                $("#linkFoto").attr(
                    "href",
                    base_url + "/img/tcf/exist-logo-compress.jpg"
                );
                $("#imageReview").attr(
                    "src",
                    base_url + "/img/tcf/exist-logo-compress.jpg"
                );
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    $("#btnUpload").on("click", function () {
        let input = document.getElementById("app_logo");
        input.click();

        $("#app_logo").on("change", function () {
            var linkFoto = "linkFoto";
            var review = "imageReview";

            compressAndDisplayImageSave(this, review, linkFoto);
        });
    });

    function readURL(input, review, linkFoto) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#" + linkFoto).attr("href", e.target.result);
                $("#" + review).attr("src", e.target.result);
                $(input).next(".custom-file-label").html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function compressAndDisplayImageSave(input, review, linkFoto ) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            var image = new Image();
            image.onload = function () {
              var canvas = document.createElement("canvas");
              var ctx = canvas.getContext("2d");

              const maxWidth = 1280;
              const maxHeight = 720;
              const ratio = Math.min(maxWidth / image.width, maxHeight / image.height);
              canvas.width = Math.round(image.width * ratio);
              canvas.height = Math.round(image.height * ratio);
              ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

                const quality = 0.8;
                const compressedImageData = canvas.toDataURL("image/jpeg", quality);
                const blob = dataURItoBlob(compressedImageData);

                const fileName = input.files[0].name.split('.')[0] + '-compressed.jpg';
                const fileType = input.files[0].type;
                const compressedFile = new File([blob], fileName, {type: fileType});

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedFile);
                input.files = dataTransfer.files;

                let formData = new FormData();
                formData.append('app_logo', compressedFile);

                $.ajax({
                    url: base_url + "/superuser/setting/upload-logo",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        loadingSwalShow();
                    },
                    success: function (data) {
                        loadingSwalClose();
                        showToast({ title: data.message });
                        $("#" + linkFoto).attr("href", compressedImageData);
                        $("#" + review).attr("src", compressedImageData);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const input = document.getElementById("app_logo");
                        input.value = "";
                        $("#linkFoto").attr(
                            "href",
                            base_url + "/img/tcf/exist-logo-compress.jpg"
                        );
                        $("#imageReview").attr(
                            "src",
                            base_url + "/img/tcf/exist-logo-compress.jpg"
                        );

                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                });
            };

            image.src = e.target.result;
          };

          reader.readAsDataURL(input.files[0]);
        }
    }

    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeType = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const intArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
          intArray[i] = byteString.charCodeAt(i);
        }
        const blob = new Blob([arrayBuffer], { type: mimeType });
        return blob;
    }

    $('#app_icon').on('change', function () {
        let formData = new FormData();
        formData.append('app_icon', this.files[0]);
        $.ajax({
            url: base_url + "/superuser/setting/upload-icon",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                loadingSwalShow();
            },
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
                const input = document.getElementById("app_icon");
                input.value = "";
                window.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                const input = document.getElementById("app_icon");
                input.value = "";
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        });
    });
});
