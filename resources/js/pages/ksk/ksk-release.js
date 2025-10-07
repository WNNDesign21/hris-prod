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

    function updateKskNotification(){
        $.ajax({
            url: base_url + '/ajax/ksk/get-ksk-notification',
            method: 'GET',
            success: function(response){
                $('.notification-release').html(response.html_release);
                $('.notification-approval').html(response.html_approval);
            }, error: function(jqXHR, textStatus, errorThrown){
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    }

    var columnsUnreleasedTable = [
        { data: "level" },
        { data: "divisi" },
        { data: "departemen" },
        { data: "release_for" },
        { data: "jumlah_karyawan_habis" },
        { data: "action" }
    ];

    var unreleasedTable = $("#unreleased-table").DataTable({
        search: {
            return: true,
        },
        order: [[0, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/release/datatable-unreleased",
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

    var releasedTable = $("#released-table").DataTable({
        search: {
            return: true,
        },
        order: [[3, "DESC"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "/ksk/release/datatable-released",
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

    // //RELOAD TABLE
    $('.btnReload').on("click", function (){
        refreshTable();
    })

    //  // MODAL REJECT
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

    $('#unreleased-table').on('click', '.btnRelease', function(){
        loadingSwalShow();
        let idDepartemen = $(this).data('id-departemen');
        let idDivisi = $(this).data('id-divisi');
        let parentId = $(this).data('parent-id');
        let tahunSelesai = $(this).data('tahun-selesai');
        let bulanSelesai = $(this).data('bulan-selesai');
        let namaDivisi = $(this).data('nama-divisi');
        let namaDepartemen = $(this).data('nama-departemen');
        let url = base_url + '/ksk/ajax/release/get-karyawans';

        $('#id_departemen_header').val(idDepartemen);
        $('#nama_departemen_header').val(namaDepartemen);
        $('#nama_divisi_header').val(namaDivisi);
        $('#id_divisi_header').val(idDivisi);
        $('#parent_id_header').val(parentId);
        $('#tahun_selesai_header').val(tahunSelesai);
        $('#bulan_selesai_header').val(bulanSelesai);
        $('#divisi').text(namaDivisi);
        $('#departemen').text(namaDepartemen);
        $('#release_date').text(moment().locale('id').format('dddd, DD MMMM YYYY'));

        $.ajax({
            url: url,
            data: {
                id_departemen: idDepartemen,
                id_divisi: idDivisi,
                parent_id: parentId,
                tahun_selesai: tahunSelesai,
                bulan_selesai: bulanSelesai
            },
            method: 'POST',
            dataType: 'JSON',
            success: function (response){
                loadingSwalClose();
                let html = response.html;
                $('#list-karyawan').empty().html(html);
                openInput();
            },
            error: function (jqXHR, textStatus, errorThrown){
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        });
    })

    $('#released-table').on('click', '.btnDetail', function(){
        loadingSwalShow();

        let idKsk = $(this).data('id-ksk');
        let namaDivisi = $(this).data('nama-divisi');
        let namaDepartemen = $(this).data('nama-departemen');
        let url = base_url + '/ksk/ajax/release/get-detail-ksk/' + idKsk

        $('#divisiDetail').text(namaDivisi);
        $('#departemenDetail').text(namaDepartemen);
        $('#release_dateDetail').text(moment().locale('id').format('dddd, DD MMMM YYYY'));

        $.ajax({
            url: base_url + '/ksk/ajax/release/get-ksk/' + idKsk,
            method: 'GET',
            dataType: 'JSON',
            success: function (response){
                let data = response.data;
                $('#released_byDetail').empty().html(data.released_by);
                $('#checked_byDetail').empty().html(data.checked_by);
                $('#approved_byDetail').empty().html(data.approved_by);
                $('#reviewed_div_byDetail').empty().html(data.reviewed_div_by);
                $('#reviewed_ph_byDetail').empty().html(data.reviewed_ph_by);
                $('#reviewed_dir_byDetail').empty().html(data.reviewed_dir_by);
            },
            error: function (jqXHR, textStatus, errorThrown){
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'JSON',
            success: function (response){
                loadingSwalClose();
                let html = response.html;
                $('#list-detail-ksk').empty().html(html);
                onClickUpdate();
                openDetail();
            },
            error: function (jqXHR, textStatus, errorThrown){
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            }
        })
    })

    function onClickUpdate(){
        $('.btnUpdate').on('click', function(){
            loadingSwalShow();
            let id = $(this).data('id');
            let idKskDetail = $('#id_ksk_detailEdit'+id).val();
            let jumlahSuratPeringatan = $('#jumlah_surat_peringatanEdit'+id).val();
            let jumlahSakit = $('#jumlah_sakitEdit'+id).val();
            let jumlahIzin = $('#jumlah_izinEdit'+id).val();
            let jumlahAlpa = $('#jumlah_alpaEdit'+id).val();

            let url = base_url + '/ksk/release/update-detail-ksk/' + idKskDetail;
            let formData = new FormData();

            formData.append('_method', 'PATCH');
            formData.append('jumlah_surat_peringatan', jumlahSuratPeringatan);
            formData.append('jumlah_sakit', jumlahSakit);
            formData.append('jumlah_izin', jumlahIzin);
            formData.append('jumlah_alpa', jumlahAlpa);

            $.ajax({
                url: url,
                data: formData,
                method: 'POST',
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function (data){
                    loadingSwalClose();
                    showToast({ title: data.message });
                },
                error: function (jqXHR, textStatus, errorThrown){
                    loadingSwalClose();
                    showToast({ icon: "error", title: jqXHR.responseJSON.message });
                }
            })
        })
    }

    $('#form-input').on('submit', function (e) {
        e.preventDefault();
        loadingSwalShow();
        let url = $(this).attr('action');
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
                updateKskNotification();
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
