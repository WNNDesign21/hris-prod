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
        $('#image')[0].files = null;
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
        canvas.toBlob(function(blob) {
            const file = new File([blob], 'capture.png', { type: 'image/png' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            $('#image')[0].files = dataTransfer.files;

            video.pause();
        }, 'image/png', 0.6);
    }

    function showPosition(position) {
        let longitude = position.coords.longitude;
        let latitude = position.coords.latitude;
        $('#longitude').val(longitude);
        $('#latitude').val(latitude);

        var map = L.map('map').setView([latitude, longitude], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        }).addTo(map);

        L.marker([latitude, longitude]).addTo(map);
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
                        getAttGpsList();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        loadingSwalClose();
                        showToast({ icon: "error", title: jqXHR.responseJSON.message });
                        const video = $('#camera-preview')[0];
                        video.play();
                        $('#image')[0].files = null;
                    },
                });
                close();
            } else {
                const video = $('#camera-preview')[0];
                video.play();
                $('#image')[0].files = null;
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

    function loadingAttGpsList() {
        $('#list-att-gps').empty();
        $('#list-att-gps').append(`
            <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
    }

    function getAttGpsList() {
        loadingAttGpsList();
        try {
            let url = base_url + '/attendance/gps/get-att-gps-list';
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    let data = response.data;
                    let list = $('#list-att-gps');
                    list.empty();
                    if(data.length > 0) {
                        data.forEach(function (item) {
                            list.prepend(`
                                <div class="media media-single">
                                    <a id="linkFoto${item.id}" href="${item.attachment}"
                                        class="image-popup-vertical-fit" data-title="${item.att_date}">
                                        <img id="imageReview${item.id}" src="${item.attachment}" alt="${item.att_date}"
                                            class="img-fluid avatar avatar-lg rounded">
                                    </a>
                                    <div class="media-body">
                                        <h6><a href="#">${item.att_time}</a></h6>
                                        <small class="text-fader" class="att-date">${item.att_date}</small>
                                        <hr>
                                        <small class="text-fader">Type: ${item.type}</small>
                                    </div>

                                    <div class="media-right">
                                        <a class="btn btn-sm ${item.status == 'IN' ? 'btn-success' : 'btn-danger'}" href="javascript:void(0)">${item.status}</a>
                                    </div>
                                </div>
                            `);
                        });
                        $('.image-popup-vertical-fit').magnificPopup({
                            type: 'image',
                            closeOnContentClick: true,
                            mainClass: 'mfp-img-mobile',
                            image: {
                                verticalFit: true
                            }
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Could not get attendance list.',
                        allowOutsideClick: false,
                        showConfirmButton: true,
                    });
                },
                
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Could not get attendance list.',
                allowOutsideClick: false,
                showConfirmButton: true,
            });
        }
    }

    $('#type').select2();

    $('.inner-user-div').slimScroll({
        height: '200px',
    });

    setInterval(updateClock, 1000);
    getAttGpsList();
    updateClock();
    getLocation();
});