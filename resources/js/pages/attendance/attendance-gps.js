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

    //DATATABLE
    var columnsTable = [
        { data: "karyawan" },
        { data: "attachment" },
        { data: "location" },
        { data: "status" },
        { data: "type" },
        { data: "is_legalized" },
    ];

    var attGpsTable = $("#att-gps-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/attendance/device/datatable",
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

        columns: columnsTable,
        columnDefs: [
            {
                orderable: false,
                targets: [0, -1],
            },
        ],
    })

    //REFRESH TABLE
    function refreshTable() {
        var searchValue = attGpsTable.search();
        if (searchValue) {
            attGpsTable.search(searchValue).draw();
        } else {
            attGpsTable.search("").draw();
        }
    }

    $('.btnReload').on("click", function (){
        refreshTable();
    })

    $('.btnAdd').on("click", function (){
        open();
    })

    $('.btnClose').on("click", function (){
        close();
    })

    // MODAL
    var modalInputOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalInput = new bootstrap.Modal(
        document.getElementById("modal-input"),
        modalInputOptions
    );

    function closeCamera(){
        const video = $('#camera-preview')[0];
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(function(track) {
            track.stop();
        });
    }

    function open() {
        openCamera();
        modalInput.show();
    }

    function close() {
        closeCamera();
        $('#status').val('');
        modalInput.hide();
    }

    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
      
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
      
        const timeString = hours + ':' + minutes + ':' + seconds +' WIB';
        document.getElementById('realtimeClock').textContent = timeString;
    }

    function getLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            getPermissionLocation();
        }
    }

    function getPermissionLocation() {
        if (navigator.permissions) {
            navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
                if (result.state === 'granted') {
                    getLocation();
                } else if (result.state === 'prompt') {
                    getLocation();
                } else if (result.state === 'denied') {
                    Swal.fire({
                        icon: "error",
                        title: "Location access has been denied.",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    });
                }
            });
        } else {
            getLocation();
        }
    }

    async function openCamera() {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
          $('#camera-preview').prop('srcObject', stream);
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'Could not access the camera.',
            allowOutsideClick: false,
            showConfirmButton: true,
         });
        }
    }
    
    function captureImage() {
        const video = $('#camera-preview')[0];
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageDataUrl = canvas.toDataURL('image/png', 0.6);
        $('#image').val(imageDataUrl);

        // Pause the video stream to simulate capturing a photo
        video.pause();
    }

    function showPosition(position) {
        $('#latitude').val(position.coords.latitude);
        $('#longitude').val(position.coords.longitude);
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                Swal.fire({
                    icon: "error",
                    title: 'User denied the request for Geolocation.',
                    allowOutsideClick: false,
                    showConfirmButton: true,
                });
                break;
            case error.POSITION_UNAVAILABLE:
                Swal.fire({
                    icon: 'error',
                    title: 'Location information is unavailable.',
                    allowOutsideClick: false,
                    showConfirmButton: true,
                });
                break;
            case error.TIMEOUT:
                Swal.fire({
                    icon: 'error',
                    title: 'The request to get user location timed out.',
                    allowOutsideClick: false,
                    showConfirmButton: true,
                });
                break;
            case error.UNKNOWN_ERROR:
                Swal.fire({
                    icon: 'error',
                    title: 'An unknown error occurred.',
                    allowOutsideClick: false,
                    showConfirmButton: true,
                });
                break;
        }
    }

    $('.btnIn').on('click', function () {
        $('#camera-previewText').text('IN');
        $('#status').val('IN');
        open();
    })

    $('.btnOut').on('click', function () {
        $('#camera-previewText').text('OUT');
        $('#status').val('OUT');
        open();
    })

    let isFrontCamera = true;
    $('.btnSwitch').on('click', function () {
        isFrontCamera = !isFrontCamera;
        switchCamera();
    });

    $('.btnCapture').on('click', function () {
        captureImage();
        Swal.fire({
            title: 'Confirm Presence',
            text: "Do you want to submit this presence?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                let type = $('#type').val();
                let url = $('#form-input').attr('action');
                let formData = new FormData($('#form-input')[0]);
                formData.append('type', type);
                loadingSwalShow();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        loadingSwalClose();
                        showToast({ icon: "success", title: response.message });
                        close();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                        const video = $('#camera-preview')[0];
                        video.play();
                        $('#image').val('');
                    },
                });
                close();
            } else {
                const video = $('#camera-preview')[0];
                video.play();
                $('#image').val('');
            }
        });
    });

    async function switchCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: isFrontCamera ? "user" : "environment" } });
            $('#camera-preview').prop('srcObject', stream);
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Could not switch the camera.',
                allowOutsideClick: false,
                showConfirmButton: true,
            });
        }
    }

    $('#type').select2();
    setInterval(updateClock, 1000);
    updateClock();
    getLocation();
});