!function($) {
    "use strict";

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

    // MODAL DETAIL CUTI
    var modalEventCutiOptions = {
        backdrop: true,
        keyboard: false,
    };

    var modalEventCuti = new bootstrap.Modal(
        document.getElementById("modal-event-cuti"),
        modalEventCutiOptions
    );

    function openDetail() {
        modalEventCuti.show();
    }

    function closeDetail() {
        modalEventCuti.hide();
        resetDetail();
    }

    function resetDetail(){
        $('#nama').text('');
        $('#alasan_cuti').text('');
        $('#jenis_cuti').text('');
        $('#durasi_cuti').text('');
        $('#rencana_cuti').text('');
        $('#karyawan_pengganti').text();
        $('#status_cuti').text('');
        $('#attachment').empty();
    }

    $('.btnClose').on('click', function (){
        closeDetail();
    })

    var CalendarApp = function() {
        this.$body = $("body")
        this.$calendar = $('#calendarEvent'),
        this.$event = ('#external-events div.external-event'),
        this.$categoryForm = $('#add-new-events form'),
        this.$extEvents = $('#external-events'),
        this.$modal = $('#my-event'),
        this.$saveCategoryBtn = $('.save-category'),
        this.$calendarObj = null
    };
    CalendarApp.prototype.onEventClick =  function (calEvent, jsEvent, view) {},
    CalendarApp.prototype.init = function() {
        var $this = this;
        let url = base_url + '/master-data/event/get-data-event-calendar';
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                var defaultEvents = data;
                $this.$calendarObj = $this.$calendar.fullCalendar({
                    defaultView: 'month',
                    handleWindowResize: true,
                     
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    nextDayThreshold: '09:00:00',
                    events: defaultEvents,
                    eventLimit: true, 
                    drop: function(date) { $this.onDrop($(this), date); },
                    select: function (start, end, allDay) { $this.onSelect(start, end, allDay); },
                    eventClick: function(calEvent, jsEvent, view) { 
                        $this.onEventClick(calEvent, jsEvent, view); 
                        let nama_karyawan = calEvent.nama_karyawan;
                        let alasan_cuti = calEvent.alasan_cuti;
                        let jenis_cuti = calEvent.jenis_cuti;
                        let durasi_cuti = calEvent.durasi_cuti;
                        let rencana_mulai_cuti = calEvent.rencana_mulai_cuti;   
                        let rencana_selesai_cuti = calEvent.rencana_selesai_cuti;
                        let aktual_mulai_cuti = calEvent.aktual_mulai_cuti;   
                        let aktual_selesai_cuti = calEvent.aktual_selesai_cuti;
                        let karyawan_pengganti = calEvent.karyawan_pengganti;
                        let status_cuti = calEvent.status_cuti;
                        let attachment = calEvent.attachment;

                        $('#nama_karyawan').text(nama_karyawan);
                        $('#alasan_cuti').text(alasan_cuti);
                        $('#jenis_cuti').text(jenis_cuti);
                        $('#durasi_cuti').text(durasi_cuti);
                        $('#rencana_cuti').text(rencana_mulai_cuti + ' - ' + rencana_selesai_cuti);
                        $('#aktual_cuti').text((aktual_mulai_cuti ? aktual_mulai_cuti : '') + ' - ' + (aktual_selesai_cuti ? aktual_selesai_cuti : ''));
                        $('#karyawan_pengganti').text(karyawan_pengganti);
                        $('#status_cuti').text(status_cuti);
                        $('#attachment').empty();
                        $('#attachment').append(attachment);

                        openDetail();
                    }
                });
            },
            error: function(error) {
                console.error('Failed to fetch events:', error);
            }
        });

        this.$saveCategoryBtn.on('click', function(){
            var categoryName = $this.$categoryForm.find("input[name='category-name']").val();
            var categoryColor = $this.$categoryForm.find("select[name='category-color']").val();
            if (categoryName !== null && categoryName.length != 0) {
                $this.$extEvents.append('<div class="m-15 external-event bg-' + categoryColor + '" data-class="bg-' + categoryColor + '" style="position: relative;"><i class="fa fa-hand-o-right"></i>' + categoryName + '</div>')
            }

        });
    },

    $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp
    
}(window.jQuery),

function($) {
    "use strict";
    $.CalendarApp.init()
	
}(window.jQuery);