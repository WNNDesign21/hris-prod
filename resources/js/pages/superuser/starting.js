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

    //SUBMIT TAMBAH ORGANISASI
    $('#form-input').on('submit', function (e){
        e.preventDefault();
        loadingSwalShow();
        let url = $('#form-input').attr('action');

        var formData = new FormData($('#form-input')[0]);
        $.ajax({
            url: url,
            data: formData,
            method:"POST",
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {
                loadingSwalClose();
                showToast({ title: data.message });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    // WIZZARD
    function initializedWizzard() {
        $(".tab-wizard").steps({
            headerTag: "h6"
            , bodyTag: "section"
            , transitionEffect: "none"
            , titleTemplate: '<span class="step">#index#</span> #title#'
            , labels: {
                finish: "Submit"
            }
            , onFinished: function (event, currentIndex) {
               swal("Your Order Submitted!", "Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula vel nunc egestas porttitor.");
            }
        });

        $.validator.addMethod("notEqual", function(value, element, param) {
            return this.optional(element) || value !== $(param).val();
        }, "Please specify a different value.");

        var form = $(".validation-wizard").show();
        $(".validation-wizard").steps({
            headerTag: "h6"
            , bodyTag: "section"
            , transitionEffect: "none"
            , titleTemplate: '<span class="step">#index#</span> #title#'
            , labels: {
                finish: "Submit"
            }
            , onStepChanging: function (event, currentIndex, newIndex) {
                return currentIndex > newIndex || !(3 === newIndex && Number($("#age-2").val()) < 18) && (currentIndex < newIndex && (form.find(".body:eq(" + newIndex + ") label.error").remove(), form.find(".body:eq(" + newIndex + ") .error").removeClass("error")), form.validate().settings.ignore = ":disabled,:hidden", form.valid())
            }
            , onFinishing: function (event, currentIndex) {
                return form.validate().settings.ignore = ":disabled", form.valid()
            }
            , onFinished: function (event, currentIndex) {
                event.preventDefault();
                let formData = new FormData($('#form-input')[0]);
                $.ajax({
                    url: base_url + '/superuser/organisasi/store',
                    data: formData,
                    method:"POST",
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    beforeSend: function () {
                        loadingSwalShow();
                    },
                    success: function (data) {
                        loadingSwalClose();
                        showToast({ title: data.message });
                        setTimeout(function() {
                            window.location.href = base_url + '/superuser/organisasi';
                        }, 2000);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                    },
                })
            }
        }), $(".validation-wizard").validate({
            ignore: "input[type=hidden]"
            , errorClass: "text-danger"
            , successClass: "text-success"
            , highlight: function (element, errorClass) {
                $(element).removeClass(errorClass)
            }
            , unhighlight: function (element, errorClass) {
                $(element).removeClass(errorClass)
            }
            , errorPlacement: function (error, element) {
                error.insertAfter(element)
            }
            , rules: {
                nama: {
                    required: true,
                    remote: {
                        url: base_url + "/superuser/organisasi/validate-input/organisasis/nama",
                        type: "POST",
                        data: {
                            value: function() {
                                return $("#nama").val();
                            },
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                    }
                },
                personalia_email: {
                    email: true,
                    notEqual: "#security_email",
                    remote: {
                        url: base_url + "/superuser/organisasi/validate-input/users/email",
                        type: "POST",
                        data: {
                            value: function() {
                                return $("#personalia_email").val();
                            },
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json"
                    },
                },
                personalia_username: {
                    required: true,
                    notEqual: "#security_username",
                    remote: {
                        url: base_url + "/superuser/organisasi/validate-input/users/username",
                        type: "POST",
                        data: {
                            value: function() {
                                return $("#personalia_username").val();
                            },
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json"
                    },
                },
                personalia_password: {
                    required: true,
                    minlength: 8
                },
                personalia_password_confirmation: {
                    equalTo: "#personalia_password"
                },
                security_email: {
                    email: true,
                    notEqual: "#personalia_email",
                    remote: {
                        url: base_url + "/superuser/organisasi/validate-input/users/email",
                        type: "POST",
                        data: {
                            value: function() {
                                return $("#security_email").val();
                            },
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                    },
                },
                security_username: {
                    required: true,
                    notEqual: "#personalia_username",
                    remote: {
                        url: base_url + "/superuser/organisasi/validate-input/users/username",
                        type: "POST",
                        data: {
                            value: function() {
                                return $("#security_username").val();
                            },
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                    },
                },
                security_password: {
                    required: true,
                    minlength: 8
                },
                security_password_confirmation: {
                    equalTo: "#security_password"
                },
            },
            messages: {
                nama: {
                    remote: "Nama organisasi sudah digunakan"
                },
                personalia_email: {
                    remote: "Email sudah digunakan",
                    notEqual: "Email Personalia tidak boleh sama dengan Email Security."
                },
                personalia_username: {
                    remote: "Username sudah digunakan",
                    notEqual: "Username Personalia tidak boleh sama dengan Username Security."
                },
                security_email: {
                    remote: "Email sudah digunakan",
                    notEqual: "Email Security tidak boleh sama dengan Email Personalia."
                },
                security_username: {
                    remote: "Username sudah digunakan",
                    notEqual: "Username Security tidak boleh sama dengan Username Personalia."
                },
            }
        })
    }
    initializedWizzard();
});
