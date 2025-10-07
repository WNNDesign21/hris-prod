$(function(){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),$.fn.modal.Constructor.prototype._enforceFocus=function(){};let r;function d(){r=Swal.fire({imageHeight:300,showConfirmButton:!1,title:'<i class="fas fa-sync-alt fa-spin fs-80"></i>',allowOutsideClick:!1,background:"rgba(0, 0, 0, 0)"})}function s(){r.close()}function o(e){Swal.mixin({toast:!0,position:"top-end",showConfirmButton:!1,timer:3e3,timerProgressBar:!0,didOpen:l=>{l.onmouseenter=Swal.stopTimer,l.onmouseleave=Swal.resumeTimer}}).fire({icon:e.icon||"success",title:e.title})}$("#form-bypass-lembur").on("submit",function(e){d(),e.preventDefault();let t=new FormData($(this)[0]),l=$(this).attr("action");$.ajax({url:l,type:"POST",data:t,contentType:!1,processData:!1,success:function(i){o({title:i.message}),s(),u(),$("#issued_by").val("").trigger("change"),$("#jenis_hari").val("").trigger("change")},error:function(i,b,c){s(),o({icon:"error",title:i.responseJSON.message})}})});function u(){$("#list-detail-lembur").empty(),a=0,n=0}$("#issued_by").on("change",function(){u()});let a=0,n=0;$("#list-detail-lembur").on("click",".btnDeleteDetailLembur",function(){n--;let e=$(this).data("urutan");$(`#card-detail-lembur-${e}`).remove(),n==0?$(".btnSubmitDetailLembur").attr("disabled",!0):$(".btnSubmitDetailLembur").attr("disabled",!1)}),$(".btnAddDetailLembur").on("click",function(){a++,n++,$("#list-detail-lembur").append(`
             <div class="col-12" id="card-detail-lembur-${a}">
                <div class="box box-bordered border-info">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${a}" id="btn_delete_detail_lembur_${a}"><i
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
                                    <select name="karyawan_id[]" id="karyawan_id_${a}" class="form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-group">
                                    <label for="karyawan">Job Description</label>
                                    <input type="text" name="job_description[]"
                                        id="job_description_${a}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                        style="width: 100%;" required>
                                    </input>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Mulai</label>
                                    <input id="rencana_mulai_lembur_${a}" name="rencana_mulai_lembur[]" type="datetime-local" class="form-control rencanaMulaiLembur" data-urutan="${a}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Selesai</label>
                                    <input id="rencana_selesai_lembur_${a}" name="rencana_selesai_lembur[]" type="datetime-local" class="form-control rencanaSelesaiLembur" data-urutan="${a}">
                                </div>
                            </div>
                             <div class="col-6 col-lg-2">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="text" name="keterangan[]"
                                        id="keterangan_${a}" class="form-control" placeholder="Isi keterangan bypass..."
                                        style="width: 100%;">
                                    </input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `),n==0?$(".btnSubmitDetailLembur").attr("disabled",!0):$(".btnSubmitDetailLembur").attr("disabled",!1),$("#karyawan_id_"+a).select2({ajax:{url:base_url+"/lembure/pengajuan-lembur/get-data-karyawan-bypass-lembur",type:"post",dataType:"json",delay:250,data:function(t){return{search:t.term||"",page:t.page||1,issued_by:$("#issued_by").val()||""}},processResults:function(t,l){let i=[];return $("select[name='karyawan_id[]']").each(function(){$(this).val()&&i.push($(this).val())}),{results:t.results.filter(function(c){return!i.includes(c.id)}),pagination:{more:l.page*30<t.total_count}}},cache:!0}})}),$("#list-detail-lembur").on("change",".rencanaMulaiLembur",function(){let e=$(this).data("urutan"),t=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#rencana_selesai_lembur_"+e).attr("min",t),$(this).val()>$("#rencana_selesai_lembur_"+e).val()&&$("#rencana_selesai_lembur_"+e).val($(this).val())}),$("#list-detail-lembur").on("change",".rencanaSelesaiLembur",function(){let e=$(this).data("urutan");$(this).val()<$("#rencana_mulai_lembur_"+e).val()&&$(this).val($("#rencana_mulai_lembur_"+e).val())}),$("#issued_by").select2(),$("#jenis_hari").select2()});
