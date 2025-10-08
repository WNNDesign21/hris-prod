import"./tempus-dominus.min-BlMCE4ys.js";import"./fa-five-DLfCLteg.js";import"./_commonjsHelpers-Cpj98o6Y.js";$(function(){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),$.fn.modal.Constructor.prototype._enforceFocus=function(){};let M;function v(){M=Swal.fire({imageHeight:300,showConfirmButton:!1,title:'<i class="fas fa-sync-alt fa-spin fs-80"></i>',allowOutsideClick:!1,background:"rgba(0, 0, 0, 0)"})}function h(){M.close()}function m(e){Swal.mixin({toast:!0,position:"top-end",showConfirmButton:!1,timer:3e3,timerProgressBar:!0,didOpen:l=>{l.onmouseenter=Swal.stopTimer,l.onmouseleave=Swal.resumeTimer}}).fire({icon:e.icon||"success",title:e.title})}function q(){$.ajax({url:base_url+"/get-planned-pengajuan-lembur-notification",method:"GET",success:function(e){$(".notification-planned-pengajuan-lembur").html(e.data)}})}var U=[{data:"id_lembur"},{data:"issued_date"},{data:"rencana_mulai_lembur"},{data:"issued_by"},{data:"jenis_hari"},{data:"total_durasi"},{data:"status"},{data:"plan_checked_by"},{data:"plan_approved_by"},{data:"plan_reviewed_by"},{data:"plan_legalized_by"},{data:"actual_checked_by"},{data:"actual_approved_by"},{data:"actual_reviewed_by"},{data:"actual_legalized_by"},{data:"aksi"}],G=$("#lembur-table").DataTable({search:{return:!0},order:[[1,"DESC"]],processing:!0,serverSide:!0,ajax:{url:base_url+"/lembure/pengajuan-lembur-datatable",dataType:"json",type:"POST",data:function(e){},error:function(e,r,l){if(e.responseJSON.data){var a=e.responseJSON.data.error;Swal.fire({icon:"error",title:" <br>Application error!",html:'<div class="alert alert-danger text-left" role="alert"><p>Error Message: <strong>'+a+"</strong></p></div>",allowOutsideClick:!1,showConfirmButton:!0}).then(function(){w()})}else{var n=e.responseJSON.message,u=e.responseJSON.line,s=e.responseJSON.file;Swal.fire({icon:"error",title:" <br>Application error!",html:'<div class="alert alert-danger text-left" role="alert"><p>Error Message: <strong>'+n+"</strong></p><p>File: "+s+"</p><p>Line: "+u+"</p></div>",allowOutsideClick:!1,showConfirmButton:!0}).then(function(){w()})}}},scrollX:!0,columns:U,columnDefs:[{orderable:!1,targets:[2,-1]},{targets:[9,10,11],visible:!1},{targets:[-1],createdCell:function(e,r,l,a,n){}}]});function w(){G.search("").draw()}$(".btnReload").on("click",function(){w()}),$(".btnAdd").on("click",function(){V()}),$(".btnClose").on("click",function(){O()});var z={backdrop:!0,keyboard:!1},C=new bootstrap.Modal(document.getElementById("modal-pengajuan-lembur"),z);function V(){C.show()}function O(){C.hide()}function Q(){$("#list-detail-lembur").empty(),p=0,E=0}let p=0,E=0;$("#list-detail-lembur").on("click",".btnDeleteDetailLembur",function(){E--;let e=$(this).data("urutan");$(`#card-detail-lembur-${e}`).remove(),E==0?$(".btnSubmitDetailLembur").attr("disabled",!0):$(".btnSubmitDetailLembur").attr("disabled",!1)}),$(".btnAddDetailLembur").on("click",function(){p++,E++,$("#list-detail-lembur").append(`
             <div class="col-12" id="card-detail-lembur-${p}">
                <div class="box box-bordered border-info">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-danger waves-effect btnDeleteDetailLembur" data-urutan="${p}" id="btn_delete_detail_lembur_${p}"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Karyawan</label>
                                    <select name="karyawan_id[]" id="karyawan_id_${p}" class="form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Job Description</label>
                                    <input type="text" name="job_description[]"
                                        id="job_description_${p}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                        style="width: 100%;" required>
                                    </input>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Mulai</label>
                                    <input id="rencana_mulai_lembur_${p}" name="rencana_mulai_lembur[]" type="datetime-local" class="form-control rencanaMulaiLembur" data-urutan="${p}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Selesai</label>
                                    <input id="rencana_selesai_lembur_${p}" name="rencana_selesai_lembur[]" type="datetime-local" class="form-control rencanaSelesaiLembur" data-urutan="${p}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);let r=moment().format("YYYY-MM-DDT00:00");$("#rencana_mulai_lembur_"+p).attr("min",r),$("#rencana_selesai_lembur_"+p).attr("min",r),E==0?$(".btnSubmitDetailLembur").attr("disabled",!0):$(".btnSubmitDetailLembur").attr("disabled",!1),$("#karyawan_id_"+p).select2({dropdownParent:$("#modal-pengajuan-lembur"),ajax:{url:base_url+"/lembure/pengajuan-lembur/get-data-karyawan-lembur",type:"post",dataType:"json",delay:250,data:function(l){return{search:l.term||"",page:l.page||1}},processResults:function(l,a){let n=[];return $("select[name='karyawan_id[]']").each(function(){$(this).val()&&n.push($(this).val())}),{results:l.results.filter(function(s){return!n.includes(s.id)}),pagination:{more:a.page*30<l.total_count}}},cache:!0}})}),$(".btnSubmitDetailLembur").on("click",function(e){v(),e.preventDefault();let r=$("#form-pengajuan-lembur").attr("action"),l=new FormData($("#form-pengajuan-lembur")[0]);$.ajax({url:r,data:l,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(a){m({title:a.message}),w(),h(),O(),Q()},error:function(a,n,u){h(),m({icon:"error",title:a.responseJSON.message})}})}),$(".btnUpdateDetailLembur").on("click",function(e){v(),e.preventDefault();let r=$("#id_lembur").val(),l=base_url+"/lembure/pengajuan-lembur/update/"+r,a=new FormData($("#form-pengajuan-lembur-edit")[0]);$.ajax({url:l,data:a,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(n){m({title:n.message}),w(),h(),P()},error:function(n,u,s){h(),m({icon:"error",title:n.responseJSON.message})}})}),$("#jenis_hari").select2({dropdownParent:$("#modal-pengajuan-lembur")});const N=document.getElementById("jenis_hari"),L=document.getElementById("jenis_lembur_group"),S=document.getElementById("jenis_lembur");function A(){N&&L&&S&&(N.value==="WD"?(L.style.display="block",S.setAttribute("required","required")):(L.style.display="none",S.removeAttribute("required"),S.value=""))}N?(A(),$("#jenis_hari").on("change",A)):console.log("jenisHariSelect not found on DOMContentLoaded."),$(".btnCloseEdit").on("click",function(){P()});var Z={backdrop:!0,keyboard:!1},J=new bootstrap.Modal(document.getElementById("modal-pengajuan-lembur-edit"),Z);function X(){J.show()}function P(){ee(),J.hide()}function ee(){b=0,$("#id_lembur").val(""),$("#jenis_hariEdit").val(""),$("#list-detail-lembur-edit").empty(),$(".btnUpdateDetailLembur").attr("disabled",!1)}let b=0;$("#lembur-table").on("click",".btnEdit",function(){v();let e=$(this).data("id-lembur"),r=base_url+"/lembure/pengajuan-lembur/get-data-lembur/"+e;$("#id_lembur").val(e),$.ajax({url:r,method:"GET",dataType:"JSON",success:function(l){let n=l.data.header.jenis_hari=="WEEKEND"?"WE":"WD",u=l.data.detail_lembur;b+=u.length;let s=$("#list-detail-lembur-edit").empty();$.each(u,function(o,c){s.append(`
                        <div class="col-12">
                            <div class="box box-bordered border-info">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <label for="karyawan">Karyawan</label>
                                                <input type="hidden" name="id_detail_lemburEdit[]" id="id_detail_lemburEdit_${o}">
                                                <select name="karyawan_idEdit[]" id="karyawan_idEdit_${o}" class="form-control" style="width: 100%;" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <label for="karyawan">Job Description</label>
                                                <input type="text" name="job_descriptionEdit[]"
                                                    id="job_descriptionEdit_${o}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                                    style="width: 100%;" required>
                                                </input>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <label for="karyawan">Rencana Mulai</label>
                                                <input id="rencana_mulai_lemburEdit_${o}" name="rencana_mulai_lemburEdit[]" type="datetime-local" class="form-control rencanaMulaiLemburEdit"
                                                data-urutan="${o}" data-mulai="${c.rencana_mulai_lembur}" required>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <label for="karyawan">Rencana Selesai</label>
                                                <input id="rencana_selesai_lemburEdit_${o}" name="rencana_selesai_lemburEdit[]" type="datetime-local" class="form-control rencanaSelesaiLemburEdit"
                                                data-urutan="${o}" data-selesai="${c.rencana_selesai_lembur}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);let _=moment(c.rencana_mulai_lembur).format("YYYY-MM-DDT00:00");$("#rencana_mulai_lemburEdit_"+o).attr("min",_),$("#rencana_selesai_lemburEdit_"+o).attr("min",_),ae(o,c.karyawan_id),$("#job_descriptionEdit_"+o).val(c.deskripsi_pekerjaan),$("#rencana_mulai_lemburEdit_"+o).val(c.rencana_mulai_lembur),$("#rencana_selesai_lemburEdit_"+o).val(c.rencana_selesai_lembur),$("#id_detail_lemburEdit_"+o).val(c.id_detail_lembur)}),$("#jenis_hariEdit").val(n),$("#jenis_hariEdit").select2({dropdownParent:$("#modal-pengajuan-lembur-edit")}),X(),h()},error:function(l,a,n){m({icon:"error",title:l.responseJSON.message})}})});function ae(e,r){$.ajax({url:base_url+"/lembure/pengajuan-lembur/get-data-karyawan-lembur",method:"GET",dataType:"JSON",success:function(l){let a=l.data,n=$("#karyawan_idEdit_"+e);$.each(a,function(u,s){n.append('<option value="'+s.id+'">'+s.text+"</option>")}),$("#karyawan_idEdit_"+e).val(r).trigger("change"),$("#karyawan_idEdit_"+e).select2({dropdownParent:$("#modal-pengajuan-lembur-edit"),ajax:{url:base_url+"/lembure/pengajuan-lembur/get-data-karyawan-lembur",type:"post",dataType:"json",delay:250,data:function(u){return{search:u.term||"",page:u.page||1}},processResults:function(u,s){let o=[];return $("select[name='karyawan_idEdit[]']").each(function(){$(this).val()&&o.push($(this).val())}),{results:u.results.filter(function(_){return!o.includes(_.id)}),pagination:{more:s.page*10<u.total_count}}},cache:!0}})}})}$("#list-detail-lembur").on("change",".rencanaMulaiLembur",function(){let e=$(this).data("urutan"),r=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#rencana_selesai_lembur_"+e).attr("min",r),$(this).val()>$("#rencana_selesai_lembur_"+e).val()&&$("#rencana_selesai_lembur_"+e).val($(this).val())}),$("#list-detail-lembur").on("change",".rencanaSelesaiLembur",function(){let e=$(this).data("urutan");$(this).val()<$("#rencana_mulai_lembur_"+e).val()&&$(this).val($("#rencana_mulai_lembur_"+e).val())}),$("#list-detail-lembur-edit").on("change",".rencanaMulaiLemburEdit",function(){let e=$(this).data("urutan"),r=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#rencana_selesai_lemburEdit_"+e).attr("min",r),$(this).val()>$("#rencana_selesai_lemburEdit_"+e).val()&&$("#rencana_selesai_lemburEdit_"+e).val($(this).val())}),$("#list-detail-lembur-edit").on("change",".rencanaSelesaiLemburEdit",function(){let e=$(this).data("urutan");$(this).val()<$("#rencana_mulai_lemburEdit_"+e).val()&&$(this).val($(this).data("selesai"))}),$("#list-detail-lembur-edit").on("change",".rencanaMulaiLemburEditNew",function(){let e=$(this).data("urutan"),r=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#rencana_selesai_lemburEditNew_"+e).attr("min",r),$(this).val()>$("#rencana_selesai_lemburEditNew_"+e).val()&&$("#rencana_selesai_lemburEditNew_"+e).val($(this).val())}),$("#list-detail-lembur-edit").on("change",".rencanaSelesaiLemburEditNew",function(){let e=$(this).data("urutan");$(this).val()<$("#rencana_mulai_lemburEditNew_"+e).val()&&$(this).val($("#rencana_mulai_lemburEditNew_"+e).val())}),$(".btnAddDetailLemburEdit").on("click",function(){b++,$("#list-detail-lembur-edit").append(`
            <div class="col-12" id="card-detail-lembur-edit-${b}">
                <div class="box box-bordered border-info">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger waves-effect btnDeleteDetailLemburEditNew" data-urutan="${b}" id="btn_delete_detail_lemburEditNew_${b}">
                                    <i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Karyawan</label>
                                    <select name="karyawan_idEditNew[]" id="karyawan_idEditNew_${b}" class="form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Job Description</label>
                                    <input type="text" name="job_descriptionEditNew[]"
                                        id="job_descriptionEditNew_${b}" class="form-control" placeholder="Pisahkan dengan koma (,)"
                                        style="width: 100%;" required>
                                    </input>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Mulai</label>
                                    <input id="rencana_mulai_lemburEditNew_${b}" name="rencana_mulai_lemburEditNew[]" type="datetime-local" class="form-control rencanaMulaiLemburEditNew"
                                    data-urutan="${b}" required>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label for="karyawan">Rencana Selesai</label>
                                    <input id="rencana_selesai_lemburEditNew_${b}" name="rencana_selesai_lemburEditNew[]" type="datetime-local" class="form-control rencanaSelesaiLemburEditNew"
                                    data-urutan="${b}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);let r=moment().format("YYYY-MM-DDT00:00");$("#rencana_mulai_lemburEditNew_"+b).attr("min",r),$("#rencana_selesai_lemburEditNew_"+b).attr("min",r),b==0?$(".btnUpdateDetailLembur").attr("disabled",!0):$(".btnUpdateDetailLembur").attr("disabled",!1),$("#karyawan_idEditNew_"+b).select2({dropdownParent:$("#modal-pengajuan-lembur-edit"),ajax:{url:base_url+"/lembure/pengajuan-lembur/get-data-karyawan-lembur",type:"post",dataType:"json",delay:250,data:function(l){return{search:l.term||"",page:l.page||1}},processResults:function(l,a){let n=[];return $("select[name='karyawan_idEdit[]']").each(function(){$(this).val()&&n.push($(this).val())}),$("select[name='karyawan_idEditNew[]']").each(function(){$(this).val()&&n.push($(this).val())}),{results:l.results.filter(function(s){return!n.includes(s.id)}),pagination:{more:a.page*10<l.total_count}}},cache:!0}})}),$("#list-detail-lembur-edit").on("click",".btnDeleteDetailLemburEditNew",function(){b--;let e=$(this).data("urutan");$(`#card-detail-lembur-edit-${e}`).remove(),b==0?$(".btnUpdateDetailLembur").attr("disabled",!0):$(".btnUpdateDetailLembur").attr("disabled",!1)}),$("#lembur-table").on("click",".btnDelete",function(){var e=$(this).data("id-lembur");Swal.fire({title:"Delete Lembur",text:"Apakah kamu yakin untuk menghapus Pengajuan Lembur ini?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes, delete it!",allowOutsideClick:!1}).then(r=>{if(r.value){v();var l=base_url+"/lembure/pengajuan-lembur/delete/"+e;$.ajax({url:l,type:"POST",data:{_method:"delete"},dataType:"JSON",success:function(a){h(),w(),m({title:a.message})},error:function(a,n,u){m({icon:"error",title:a.responseJSON.message})}})}})});var te={backdrop:!0,keyboard:!1},I=new bootstrap.Modal(document.getElementById("modal-detail-lembur-done"),te);function B(){I.show()}function K(){le(),I.hide()}function le(){$("#list-detail-lembur-done").empty()}$(".btnDone").on("click",function(){B()}),$(".btnCloseDone").on("click",function(){K()}),$("#lembur-table").on("click",".btnDone",function(){v();let e=$(this).data("id-lembur"),r=base_url+"/lembure/pengajuan-lembur/get-data-lembur/"+e;$("#id_lemburDone").val(e),$.ajax({url:r,method:"GET",dataType:"JSON",success:function(l){h();let a=l.data.header,n=l.data.text_tanggal,u=a.jenis_hari=="WEEKEND"?"WE":"WD",s=a.jenis_lembur,o=a.status,c=l.data.detail_lembur,_=$("#list-detail-lembur-done").empty(),f=l.data.attachment,k=$(".previewAttachmentLembur").empty();f.length>0?$.each(f,function(i,t){t.path.split(".").pop()=="pdf"?k.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${t.path}" data-title="Attachment Ke-${i}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`):k.append(`
                            <a id="attachment_${i}" href="${base_url}/storage/${t.path}" data-title="Attachment Ke-${i}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${t.path}" alt="Attachment Ke-${i}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`)}):k.append("<p>No Attachment Uploaded</p>"),$(".image-popup-vertical-fit").length&&$(".image-popup-vertical-fit").magnificPopup({type:"image",closeOnContentClick:!0,mainClass:"mfp-img-mobile",image:{verticalFit:!0}}),$.each(c,function(i,t){_.append(`
                        <div class="col-12">
                            <div class="box box-bordered border-info ${t.is_rencana_approved=="N"?"bg-danger":""}" id="card-list-detail-done-${i}">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-6 col-lg-2">
                                            <div class="form-group">
                                                <label for="karyawan">Karyawan</label>
                                                 ${t.is_rencana_approved!=="N"?`<input type="hidden" name="id_detail_lembur[]" id="id_detail_lembur_${i}"></input>`:"-"}
                                                <h6 id="karyawan_id_${i}" class="${t.is_rencana_approved=="N"?"text-white":""}"></h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <div class="form-group">
                                                <label for="karyawan">Job Description</label>
                                                <div id="job_description_${i}" class="${t.is_rencana_approved=="N"?"text-white":""}"></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <div class="form-group">
                                                <label for="karyawan">Aktual Mulai</label>
                                                ${t.is_rencana_approved!=="N"?`<input id="aktual_mulai_lembur_${i}" name="aktual_mulai_lembur[]" type="datetime-local" class="form-control aktualMulaiLembur" data-urutan="${i}" data-mulai="${t.rencana_mulai_lembur}" required>`:"-"}
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <div class="form-group">
                                                <label for="karyawan">Aktual Selesai</label>
                                                ${t.is_rencana_approved!=="N"?`<input id="aktual_selesai_lembur_${i}" name="aktual_selesai_lembur[]" type="datetime-local" class="form-control aktualSelesaiLembur" data-urutan="${i}" data-selesai="${t.rencana_selesai_lembur}" required>`:"-"}
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <label for="karyawan">Keterangan</label>
                                                ${t.is_rencana_approved!=="N"?`<input type="text" name="keterangan[]"
                                                    id="keterangan_${i}" class="form-control ${t.is_rencana_approved=="N"?"bg-danger text-white":""}"
                                                    style="width: 100%;">
                                                </input>`:"-"}
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-1 d-flex justify-content-center align-items-center">
                                            ${t.is_rencana_approved!=="N"?`<input type="checkbox" name="is_aktual_approved" data-urutan="${i}" id="is_aktual_approved_${i}" class="filled-in chk-col-primary" ${t.is_aktual_approved=="Y"?"checked":""} placeholder="Isi keterangan tambahan..." value="${t.id_detail_lembur}"/>
                                            <label for="is_aktual_approved_${i}" class="mt-2"></label>`:"-"}
                                            ${t.rencana_last_changed_by?`<br><p >Last Changed : <br><small>${t.rencana_last_changed_by} <br>(${t.rencana_last_changed_at})</small></p>`:""}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);let d=moment(t.rencana_mulai_lembur).format("YYYY-MM-DDT00:00");$("#aktual_mulai_lembur_"+i).attr("min",d),$("#aktual_selesai_lembur_"+i).attr("min",d),$("#aktual_mulai_lembur_"+i).on("change",function(){let g=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#aktual_selesai_lembur_"+i).attr("min",g),$(this).val()>$("#aktual_selesai_lembur_"+i).val()&&$("#aktual_selesai_lembur_"+i).val($(this).val())}),$("#aktual_selesai_lembur_"+i).on("change",function(){$(this).val()<$("#aktual_mulai_lembur_"+i).val()&&$(this).val($(this).data("selesai"))}),$("#is_aktual_approved_"+i).on("change",function(){$(this).is(":checked")?($(this).attr("checked",!0),$("#card-list-detail-done-"+i).hasClass("bg-danger")&&$("#card-list-detail-done-"+i).removeClass("bg-danger"),$("#aktual_mulai_lembur_"+i).attr("readonly",!1),$("#aktual_selesai_lembur_"+i).attr("readonly",!1)):($(this).removeAttr("checked"),$("#card-list-detail-done-"+i).addClass("bg-danger"),$("#aktual_mulai_lembur_"+i).attr("readonly",!0),$("#aktual_selesai_lembur_"+i).attr("readonly",!0))});let y=t.deskripsi_pekerjaan.split(",").map(g=>`<li>${g.trim()}</li>`).join("");$("#job_description_"+i).html(`<ul>${y}</ul>`),t.is_rencana_approved=="Y"&&($("#aktual_mulai_lembur_"+i).val(t.rencana_mulai_lembur),$("#aktual_selesai_lembur_"+i).val(t.rencana_selesai_lembur),$("#id_detail_lembur_"+i).val(t.id_detail_lembur)),$("#karyawan_id_"+i).text(t.nama),$("#form-detail-lembur-done").attr("action",base_url+"/lembure/pengajuan-lembur/done/"+e),$(".btnSubmitDoneLembur").on("click",function(g){v(),g.preventDefault();let D=$("#form-detail-lembur-done").attr("action"),j=new FormData($("#form-detail-lembur-done")[0]),T=[];$("input:checkbox[name=is_aktual_approved]:checked").each(function(){T.push($(this).val())}),j.append("is_aktual_approved",T),Swal.fire({title:"Aktual Lembur",text:"Apakah anda yakin dengan detail lembur ini?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes, Tandai sebagai Selesai!",allowOutsideClick:!1}).then(x=>{x.value&&(v(),$.ajax({url:D,data:j,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(Y){q(),m({title:Y.message}),w(),h(),K()},error:function(Y,_e,pe){h(),m({icon:"error",title:Y.responseJSON.message})}}))})})}),$("#jenis_hariDone").text(u=="WE"?"Weekend":"Weekday"),$("#text_tanggalDone").text(n),$("#jenis_lemburDone").text(s||"-"),o=="WAITING"?$("#statusDone").text(o).removeClass().addClass("badge badge-warning"):o=="PLANNED"?$("#statusDone").text(o).removeClass().addClass("badge badge-info"):o=="COMPLETED"?$("#statusDone").text(o).removeClass().addClass("badge badge-success"):$("#statusDone").text(o).removeClass().addClass("badge badge-danger"),B(),h()},error:function(l,a,n){m({icon:"error",title:l.responseJSON.message})}})});var re={backdrop:!0,keyboard:!1},F=new bootstrap.Modal(document.getElementById("modal-detail-approval-lembur"),re);function ne(){F.show()}function ie(){F.hide()}$(".btnCloseDetail").on("click",function(){ie()}),$("#lembur-table").on("click",".btnDetail",function(){v();let e=$(this).data("id-lembur"),r=base_url+"/lembure/pengajuan-lembur/get-data-lembur/"+e,l=$(this).data("is-member");$.ajax({url:r,method:"GET",dataType:"JSON",success:function(a){let n=a.data.header,u=a.data.text_tanggal,s=n.jenis_hari=="WEEKEND"?"WE":"WD",o=n.jenis_lembur,c=n.status,_=a.data.detail_lembur,f=$("#list-detail-approval-lembur").empty(),k=a.data.attachment,i=$(".previewAttachmentLembur").empty();k.length>0?$.each(k,function(t,d){d.path.split(".").pop()=="pdf"?i.append(`
                            <a id="attachment_${t}" href="${base_url}/storage/${d.path}" data-title="Attachment Ke-${t}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${t}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`):i.append(`
                            <a id="attachment_${t}" href="${base_url}/storage/${d.path}" data-title="Attachment Ke-${t}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${d.path}" alt="Attachment Ke-${t}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`)}):i.append("<p>No Attachment Uploaded</p>"),$(".image-popup-vertical-fit").length&&$(".image-popup-vertical-fit").magnificPopup({type:"image",closeOnContentClick:!0,mainClass:"mfp-img-mobile",image:{verticalFit:!0}}),$.each(_,function(t,d){f.append(`
                         <tr class="${d.is_rencana_approved=="N"||d.is_aktual_approved=="N"?"bg-danger":""}">
                            <td>
                                <span id="karyawan_id_detail_${t}"></span>
                            </td>
                            <td>
                                <div id="job_description_detail_${t}"></div>
                            </td>
                            <td>
                                <span id="rencana_mulai_lembur_detail_${t}"></span>
                            </td>
                            <td>
                                <span id="rencana_selesai_lembur_detail_${t}"></span>
                            </td>
                            <td>
                                ${d.durasi_rencana} ${d.rencana_last_changed_by?`<br><p >Last Changed : <br><small>${d.rencana_last_changed_by} <br>(${d.rencana_last_changed_at})</small></p>`:""}
                            </td>
                            <td>
                                <span id="aktual_mulai_lembur_detail_${t}"></span>
                            </td>
                            <td>
                                <span id="aktual_selesai_lembur_detail_${t}"></span>
                            </td>
                            <td id="checkin-aktual-${t}" class="col-check-in" style="display: none;">-</td>
                            <td id="checkout-aktual-${t}" class="col-check-out" style="display: none;">-</td>
                            <td id="match_status-aktual-${t}" class="col-match-status" style="display: none;">-</td>
                            </td>
                            <td>
                                ${d.durasi_aktual} ${d.aktual_last_changed_by?`<br><p >Last Changed : <br><small>${d.aktual_last_changed_by} <br>(${d.aktual_last_changed_at})</small></p>`:""}
                            </td>
                            <td>
                                `+(d.keterangan?d.keterangan:"-")+`
                            </td>
                             <td>
                                `+(l?"-":d.nominal)+`
                            </td>
                        </tr>
                    `),$("#karyawan_id_detail_"+t).text(d.nama);let y=d.deskripsi_pekerjaan.split(",").map(g=>`<li>${g.trim()}</li>`).join("");$("#job_description_detail_"+t).html(`<ul>${y}</ul>`),$("#rencana_mulai_lembur_detail_"+t).text(d.rencana_mulai_lembur?d.rencana_mulai_lembur.replace("T"," ").replace(":","."):"-"),$("#rencana_selesai_lembur_detail_"+t).text(d.rencana_selesai_lembur?d.rencana_selesai_lembur.replace("T"," ").replace(":","."):"-"),$("#aktual_mulai_lembur_detail_"+t).text(d.aktual_mulai_lembur?d.aktual_mulai_lembur.replace("T"," ").replace(":","."):"-"),$("#aktual_selesai_lembur_detail_"+t).text(d.aktual_selesai_lembur?d.aktual_selesai_lembur.replace("T"," ").replace(":","."):"-")}),c==="COMPLETED"||c==="REJECTED"?($("#th-check-in, #th-check-out, #th-match-status, .col-check-in, .col-check-out, .col-match-status").show(),$.each(_,function(t,d){const y=d.karyawan_id,g=se(d,n,u),D={jenis_hari:n.jenis_hari,jenis_lembur:n.jenis_lembur,aktual_mulai:d.aktual_mulai_lembur,aktual_selesai:d.aktual_selesai_lembur};y&&g&&oe(t,y,g,D)})):$("#th-check-in, #th-check-out, #th-match-status, .col-check-in, .col-check-out, .col-match-status").hide(),$("#jenis_hariDetail").text(s=="WE"?"Weekend":"Weekday"),$("#text_tanggalDetail").text(u),$("#jenis_lemburDetail").text(o||"-"),c=="WAITING"?$("#statusDetail").text(c).removeClass().addClass("badge badge-warning"):c=="PLANNED"?$("#statusDetail").text(c).removeClass().addClass("badge badge-info"):c=="COMPLETED"?$("#statusDetail").text(c).removeClass().addClass("badge badge-success"):$("#statusDetail").text(c).removeClass().addClass("badge badge-danger"),ne(),h()},error:function(a,n,u){m({icon:"error",title:a.responseJSON.message})}})});function se(e,r,l){const a=[e==null?void 0:e.rencana_mulai_lembur,r==null?void 0:r.tanggal_iso,r==null?void 0:r.tanggal];l&&a.push(l);const n=["YYYY-MM-DDTHH:mm","YYYY-MM-DD HH:mm:ss","YYYY-MM-DD HH:mm","YYYY-MM-DD","MM/DD/YYYY hh:mm A","DD/MM/YYYY HH:mm","DD-MM-YYYY HH:mm","DD-MM-YYYY","D MMMM YYYY","dddd, D MMMM YYYY"];typeof(moment==null?void 0:moment.locale)=="function"&&moment.locale("id");for(const u of a){if(!u)continue;for(const o of n){const c=moment(u,o,!0);if(c.isValid())return c.format("YYYY-MM-DD")}const s=moment(u);if(s.isValid())return s.format("YYYY-MM-DD")}return null}function oe(e,r,l,a){const n=moment(l).format("YYYY-MM-DD");$.ajax({url:base_url+"/lembure/approval-lembur/get-list-data-cross-check",method:"POST",dataType:"JSON",data:{id_karyawan:r,date:n},success:function(u){const s=u.data||{},o=s.check_in?moment(s.check_in):null,c=s.check_out?moment(s.check_out):null,_=a.aktual_mulai?moment(a.aktual_mulai):null,f=a.aktual_selesai?moment(a.aktual_selesai):null;$("#checkin-aktual-"+e).text(o?o.format("HH:mm"):"-"),$("#checkout-aktual-"+e).text(c?c.format("HH:mm"):"-");let k="-",i=!1;a.jenis_hari==="WEEKEND"?o&&_&&c&&f&&o.isSameOrBefore(_)&&c.isSameOrAfter(f)&&(i=!0):a.jenis_lembur==="AWAL"?o&&_&&o.isSameOrBefore(_)&&(i=!0):a.jenis_lembur==="AKHIR"&&c&&f&&c.isSameOrAfter(f)&&(i=!0),i?k='<span class="badge badge-success">MATCH</span>':(_||f)&&(k='<span class="badge badge-danger">UNMATCH</span>'),$("#match_status-aktual-"+e).html(k)},error:function(u){$("#checkin-aktual-"+e).text("-"),$("#checkout-aktual-"+e).text("-"),$("#match_status-aktual-"+e).text("-")}})}function ue(e){return["image/jpeg","image/png","image/gif","image/bmp"].includes(e.toLowerCase())}function de(e,r){if(e.files&&e.files[0]){var l=new FileReader;l.onload=function(a){var n=new Image;if(ue(e.files[0].type))n.onload=function(){var s=document.createElement("canvas"),o=s.getContext("2d");const f=Math.min(1280/n.width,720/n.height);s.width=Math.round(n.width*f),s.height=Math.round(n.height*f),o.drawImage(n,0,0,s.width,s.height);const i=s.toDataURL("image/jpeg",.9),t=ce(i),d=e.files[0].name.split(".")[0]+"-compressed.jpg",y=e.files[0].type,g=new File([t],d,{type:y}),D=new DataTransfer;D.items.add(g),e.files=D.files;let j=new FormData;j.append("lembur_id",r),j.append("attachment_lembur",g);let T=base_url+"/lembure/pengajuan-lembur/store-lkh";$.ajax({url:T,type:"POST",data:j,contentType:!1,processData:!1,dataType:"JSON",success:function(x){m({title:x.message}),W(r),$("#attachment_lembur").val("")},error:function(x,Y,_e){m({icon:"error",title:x.responseJSON.message})}})};else{let s=new FormData;s.append("lembur_id",r),s.append("attachment_lembur",e.files[0]);let o=base_url+"/lembure/pengajuan-lembur/store-lkh";$.ajax({url:o,type:"POST",data:s,contentType:!1,processData:!1,dataType:"JSON",success:function(c){m({title:c.message}),W(r),$("#attachment_lembur").val("")},error:function(c,_,f){m({icon:"error",title:c.responseJSON.message})}})}n.src=a.target.result},l.readAsDataURL(e.files[0])}}$("#attachment_lembur").on("change",function(){v();let e=$("#id_lemburDone").val();de(this,e)});function ce(e){const r=atob(e.split(",")[1]),l=e.split(",")[0].split(":")[1].split(";")[0],a=new ArrayBuffer(r.length),n=new Uint8Array(a);for(let s=0;s<r.length;s++)n[s]=r.charCodeAt(s);return new Blob([a],{type:l})}function W(e){let r=base_url+"/lembure/pengajuan-lembur/get-attachment-lembur/"+e;$.ajax({url:r,method:"GET",dataType:"JSON",success:function(l){let a=l.data,n=$(".previewAttachmentLembur").empty();a.length>0?$.each(a,function(u,s){s.path.split(".").pop()=="pdf"?n.append(`
                            <a id="attachment_${u}" href="${base_url}/storage/${s.path}" data-title="Attachment Ke-${u}" target="_blank">
                                <img src="${base_url}/img/pdf-img.png" alt="Attachment Ke-${u}" style="width: 3.5rem;height: 3.5rem;" class="p-0">
                            </a>`):n.append(`
                            <a id="attachment_${u}" href="${base_url}/storage/${s.path}" data-title="Attachment Ke-${u}" class="image-popup-vertical-fit">
                                <img src="${base_url}/storage/${s.path}" alt="Attachment Ke-${u}" style="width: 3.5rem;height: 3.5rem;" class="img-fluid p-0">
                            </a>`)}):n.append("<p>No Attachment Uploaded</p>"),$(".image-popup-vertical-fit").length&&$(".image-popup-vertical-fit").magnificPopup({type:"image",closeOnContentClick:!0,mainClass:"mfp-img-mobile",image:{verticalFit:!0}})},error:function(l,a,n){m({icon:"error",title:l.responseJSON.message})}})}$(".btnRejectClose").on("click",function(){R()});var me={backdrop:!0,keyboard:!1},H=new bootstrap.Modal(document.getElementById("modal-reject-lembur"),me);function be(){$(".modal-title").text("Alasan Cancel"),$("#btnSubmitReject").text("Cancel"),H.show()}function R(){H.hide()}$("#lembur-table").on("click",".btnRejectLembur",function(){let e=$(this).data("id-lembur"),r=base_url+"/lembure/approval-lembur/rejected/"+e;$("#form-reject-lembur").attr("action",r),be()}),$("#form-reject-lembur").on("submit",function(e){v(),e.preventDefault();let r=$("#form-reject-lembur").attr("action");var l=new FormData($("#form-reject-lembur")[0]);$.ajax({url:r,data:l,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(a){m({title:a.message}),w(),R(),h()},error:function(a,n,u){h(),m({icon:"error",title:a.responseJSON.message})}})})});
