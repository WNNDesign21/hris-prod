$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function () {};

    // ALERT & LOADING
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

    function showToast(options) {
        const toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000, 
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

    //Submit Form
    $('#form-bypass-lembur').on('submit', function (e) {
        loadingSwalShow();
        e.preventDefault();
        let formData = new FormData($(this)[0]);
        let url = $(this).attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                showToast({ title: data.message });
                loadingSwalClose();
                resetForm();
                $('#issued_by').val('').trigger('change');
                $('#jenis_hari').val('').trigger('change');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loadingSwalClose();
                showToast({ icon: "error", title: jqXHR.responseJSON.message });
            },
        })
    });

    function resetForm() {
        $('#list-detail-lembur').empty();
        count = 0;
        jumlah_detail_lembur = 0;
    }

    $('#issued_by').on('change', function () {
        resetForm();
    });

    let count = 0;
    let jumlah_detail_lembur = 0;
    $('#list-detail-lembur').on("click", '.btnDeleteDetailLembur', function (){
        jumlah_detail_lembur--;
        let urutan = $(this).data('urutan');
        $(`#card-detail-lembur-${urutan}`).remove();

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }
    });

    //TAMBAH DETAIL LEMBUR DAN LOGIC DIDALAMNYA
    $('.btnAddDetailLembur').on("click", function (){
        count++;
        jumlah_detail_lembur++;
        let tbody = $('#list-detail-lembur');
        tbody.append(`
             <div class="col-12" id="card-detail-lembur-${count}">
                <div class="box box-bordered border-info">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${count}" id="btn_delete_detail_lembur_${count}"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-6 col-lg-2">
                                <div class="form-group">
                                    <label for="karyawan">Karyawan</label>
                                    <select name="karyawan_id[]" id="karyawan_id_${count}" class="form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-group">
                                    <label for="karyawan">Job Description</label>
                                    <input type="text" name="job_description[]"
                                        id="job_description_${count}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                        style="width: 100%;" required>
                                    </input>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Mulai</label>
                                    <input id="rencana_mulai_lembur_${count}" name="rencana_mulai_lembur[]" type="datetime-local" class="form-control rencanaMulaiLembur" data-urutan="${count}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Selesai</label>
                                    <input id="rencana_selesai_lembur_${count}" name="rencana_selesai_lembur[]" type="datetime-local" class="form-control rencanaSelesaiLembur" data-urutan="${count}">
                                </div>
                            </div>
                             <div class="col-6 col-lg-2">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="text" name="keterangan[]"
                                        id="keterangan_${count}" class="form-control" placeholder="Isi keterangan bypass..."
                                        style="width: 100%;">
                                    </input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `)

        if(jumlah_detail_lembur == 0){
            $('.btnSubmitDetailLembur').attr('disabled', true);
        } else {
            $('.btnSubmitDetailLembur').attr('disabled', false);
        }

        $("#karyawan_id_" + count).select2({
            ajax: {
            url: base_url + "/lembure/pengajuan-lembur/get-data-karyawan-bypass-lembur",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    issued_by: $('#issued_by').val() || "",
                };
            },
            processResults: function (data, params) {
                let selectedIds = [];
                $("select[name='karyawan_id[]']").each(function () {
                if ($(this).val()) {
                    selectedIds.push($(this).val());
                }
                });

                let filteredData = data.results.filter(function (item) {
                return !selectedIds.includes(item.id);
                });

                return {
                    results: filteredData,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
            },
        });
    })

    $('#list-detail-lembur').on('change','.rencanaMulaiLembur', function(){
        let urutan = $(this).data('urutan');
        let startTime = moment($(this).val()).format('YYYY-MM-DDT00:00');
        $('#rencana_selesai_lembur_' + urutan).attr('min', startTime);
        if($(this).val() > $('#rencana_selesai_lembur_' + urutan).val()){
            $('#rencana_selesai_lembur_' + urutan).val($(this).val());
        }
    });

    $('#list-detail-lembur').on('change', '.rencanaSelesaiLembur', function(){
        let urutan = $(this).data('urutan');
        if($(this).val() < $('#rencana_mulai_lembur_' + urutan).val()){
            $(this).val( $('#rencana_mulai_lembur_' + urutan).val());
        }
    })

    $('#issued_by').select2();
    $('#jenis_hari').select2();

});