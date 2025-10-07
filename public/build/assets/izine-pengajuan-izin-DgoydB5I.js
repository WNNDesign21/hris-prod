$(function(){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),$.fn.modal.Constructor.prototype._enforceFocus=function(){};let d;function s(){d=Swal.fire({imageHeight:300,showConfirmButton:!1,title:'<i class="fas fa-sync-alt fa-spin fs-80"></i>',allowOutsideClick:!1,background:"rgba(0, 0, 0, 0)"})}function o(){d.close()}function t(e){Swal.mixin({toast:!0,position:"top-end",showConfirmButton:!1,timer:2e3,timerProgressBar:!0,didOpen:r=>{r.onmouseenter=Swal.stopTimer,r.onmouseleave=Swal.resumeTimer}}).fire({icon:e.icon||"success",title:e.title})}function _(){$.ajax({url:base_url+"/get-pengajuan-izin-notification",method:"GET",success:function(e){$(".notification-pengajuan-izin").html(e.data)}})}var D=[{data:"id_izin"},{data:"rencana_mulai_or_masuk"},{data:"rencana_selesai_or_keluar"},{data:"aktual_mulai_or_masuk"},{data:"aktual_selesai_or_keluar"},{data:"jenis_izin"},{data:"durasi"},{data:"keterangan"},{data:"checked_by"},{data:"approved_by"},{data:"legalized_by"},{data:"aksi"}],E=$("#pengajuan-izin-table").DataTable({search:{return:!0},order:[[0,"DESC"]],processing:!0,serverSide:!0,ajax:{url:base_url+"/izine/pengajuan-izin-datatable",dataType:"json",type:"POST",data:function(e){},error:function(e,n,r){if(e.responseJSON.data){var a=e.responseJSON.data.error;Swal.fire({icon:"error",title:" <br>Application error!",html:'<div class="alert alert-danger text-left" role="alert"><p>Error Message: <strong>'+a+"</strong></p></div>",allowOutsideClick:!1,showConfirmButton:!0}).then(function(){u()})}else{var i=e.responseJSON.message,l=e.responseJSON.line,m=e.responseJSON.file;Swal.fire({icon:"error",title:" <br>Application error!",html:'<div class="alert alert-danger text-left" role="alert"><p>Error Message: <strong>'+i+"</strong></p><p>File: "+m+"</p><p>Line: "+l+"</p></div>",allowOutsideClick:!1,showConfirmButton:!0}).then(function(){u()})}}},scrollX:!0,columns:D,columnDefs:[{orderable:!1,targets:[-2,-1]},{targets:[-1],createdCell:function(e,n,r,a,i){}}]});function u(){E.search("").draw()}$(".btnReload").on("click",function(){u()}),$(".btnAdd").on("click",function(){M()}),$(".btnClose").on("click",function(){f()});var z={backdrop:!0,keyboard:!1},k=new bootstrap.Modal(document.getElementById("modal-pengajuan-izin"),z);function M(){k.show()}function f(){k.hide(),S()}function S(){$("#jenis_izin").val("TM").trigger("change"),$("#keterangan").val(""),$("#conditional_field").empty().append(`
            <div class="form-group">
                <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                    Mulai</label>
                <input type="date" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                    class="form-control" required>
            </div>
            <div class="form-group">
                <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana
                    Selesai</label>
                <input type="date" name="rencana_selesai_or_keluar"
                    id="rencana_selesai_or_keluar" class="form-control" required>
            </div>    
            <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                    Selesai di tanggal yang sama!</small>     
        `);let e=moment().format("YYYY-MM-DD");$("#rencana_mulai_or_masuk").attr("min",e),$("#rencana_selesai_or_keluar").attr("min",e),$("#rencana_mulai_or_masuk").on("change",function(){let n=$(this).val();$("#rencana_selesai_or_keluar").attr("min",n),n>$("#rencana_selesai_or_keluar").val()&&$("#rencana_selesai_or_keluar").val(n)}),$("#rencana_selesai_or_keluar").on("change",function(){$(this).val()<$("#rencana_mulai_or_masuk").val()&&$(this).val($("#rencana_mulai_or_masuk").val())})}$("#jenis_izin").select2({dropdownParent:$("#modal-pengajuan-izin")});let p=moment().format("YYYY-MM-DD");$("#rencana_mulai_or_masuk").attr("min",p),$("#rencana_selesai_or_keluar").attr("min",p),$("#rencana_mulai_or_masuk").on("change",function(){let e=$(this).val();$("#rencana_selesai_or_keluar").attr("min",e),e>$("#rencana_selesai_or_keluar").val()&&$("#rencana_selesai_or_keluar").val(e)}),$("#rencana_selesai_or_keluar").on("change",function(){$(this).val()<$("#rencana_mulai_or_masuk").val()&&$(this).val($("#rencana_mulai_or_masuk").val())}),$("#jenis_izin").on("change",function(){var e=$(this).val();if(e=="TM"){$("#conditional_field").empty().append(`
                <div class="form-group">
                    <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                        Mulai</label>
                    <input type="date" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                        class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana
                        Selesai</label>
                    <input type="date" name="rencana_selesai_or_keluar"
                        id="rencana_selesai_or_keluar" class="form-control" required>
                </div>    
                <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                        Selesai di tanggal yang sama!</small>     
            `);let n=moment().format("YYYY-MM-DD");$("#rencana_mulai_or_masuk").attr("min",n),$("#rencana_selesai_or_keluar").attr("min",n),$("#rencana_mulai_or_masuk").on("change",function(){let r=$(this).val();$("#rencana_selesai_or_keluar").attr("min",r),r>$("#rencana_selesai_or_keluar").val()&&$("#rencana_selesai_or_keluar").val(r)}),$("#rencana_selesai_or_keluar").on("change",function(){$(this).val()<$("#rencana_mulai_or_masuk").val()&&$(this).val($("#rencana_mulai_or_masuk").val())})}else if(e=="SH"){$("#conditional_field").empty().append(`
                <div class="form-group">
                    <label for="masuk_or_keluar" id="label_masuk_or_keluar">Masuk / Keluar</label>
                    <select name="masuk_or_keluar" id="masuk_or_keluar" class="form-control" required>
                        <option value="M">Masuk</option>
                        <option value="K">Keluar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rencana_masuk_or_keluar" id="label_rencana_masuk_or_keluar">Jam Masuk / Keluar</label>
                    <input type="datetime-local" name="rencana_masuk_or_keluar" id="rencana_masuk_or_keluar"
                        class="form-control" required>
                </div>
            `);let n=moment().format("YYYY-MM-DDT00:00");$("#rencana_masuk_or_keluar").attr("min",n),$("#masuk_or_keluar").select2({dropdownParent:$("#modal-pengajuan-izin")})}else if(e=="KP"){$("#conditional_field").empty().append(`
                <div class="form-group">
                    <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana
                        Keluar</label>
                    <input type="datetime-local" name="rencana_selesai_or_keluar"
                        id="rencana_selesai_or_keluar" class="form-control" required>
                </div> 
                <div class="form-group">
                    <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                        Kembali</label>
                    <input type="datetime-local" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                        class="form-control" required>
                </div>
            `);let n=moment().format("YYYY-MM-DDT00:00");$("#rencana_selesai_or_keluar").attr("min",n),$("#rencana_mulai_or_masuk").attr("min",n),$("#rencana_selesai_or_keluar").on("change",function(){let r=$(this).val();$("#rencana_mulai_or_masuk").attr("min",r),r>$("#rencana_mulai_or_masuk").val()&&$("#rencana_mulai_or_masuk").val(r)}),$("#rencana_mulai_or_masuk").on("change",function(){$(this).val()<$("#rencana_selesai_or_keluar").val()&&$(this).val($("#rencana_selesai_or_keluar").val())})}else if(e=="PL"){$("#conditional_field").empty().append(`
                <div class="form-group">
                    <label for="rencana_selesai_or_keluar" id="label_rencana_selesai_or_keluar">Rencana Pulang</label>
                    <input type="datetime-local" name="rencana_selesai_or_keluar" id="rencana_selesai_or_keluar"
                        class="form-control" required>
                </div>
            `);let n=moment().format("YYYY-MM-DDT00:00");$("#rencana_selesai_or_keluar").attr("min",n)}}),$("#form-pengajuan-izin").on("submit",function(e){s(),e.preventDefault();let n=$("#form-pengajuan-izin").attr("action");var r=new FormData($("#form-pengajuan-izin")[0]);$.ajax({url:n,data:r,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(a){_(),t({title:a.message}),u(),f(),o()},error:function(a,i,l){o(),t({icon:"error",title:a.responseJSON.message})}})}),$("#form-pengajuan-izin-edit").on("submit",function(e){s(),e.preventDefault();let n=$("#id_izinEdit").val(),r=base_url+"/izine/pengajuan-izin/update/"+n;var a=new FormData($("#form-pengajuan-izin-edit")[0]);$.ajax({url:r,data:a,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(i){_(),t({title:i.message}),u(),c(),o()},error:function(i,l,m){o(),t({icon:"error",title:i.responseJSON.message})}})}),$("#pengajuan-izin-table").on("click",".btnDelete",function(){var e=$(this).data("id-izin");console.log(e),Swal.fire({title:"Delete Izin",text:"Apakah kamu yakin untuk menghapus Pengajuan Izin ini?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes, delete it!",allowOutsideClick:!1}).then(n=>{if(n.value){s();var r=base_url+"/izine/pengajuan-izin/delete/"+e;$.ajax({url:r,type:"POST",data:{_method:"delete"},dataType:"JSON",success:function(a){_(),o(),u(),t({title:a.message})},error:function(a,i,l){t({icon:"error",title:a.responseJSON.message})}})}})}),$("#pengajuan-izin-table").on("click",".btnCancel",function(){var e=$(this).data("id-izin");console.log(e),Swal.fire({title:"Cancel Izin",text:"Setelah di cancel, data akan hilang dan tidak bisa kembali",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes, Cancel it!",allowOutsideClick:!1}).then(n=>{if(n.value){s();var r=base_url+"/izine/pengajuan-izin/delete/"+e;$.ajax({url:r,type:"POST",data:{_method:"delete"},dataType:"JSON",success:function(a){_(),o(),u(),t({title:a.message})},error:function(a,i,l){t({icon:"error",title:a.responseJSON.message})}})}})}),$("#pengajuan-izin-table").on("click",".btnEdit",function(){s();var e=$(this).data("id-izin"),n=base_url+"/izine/pengajuan-izin/get-data-izin/"+e;$.ajax({url:n,type:"GET",success:function(r){var a=r.data;if($("#id_izinEdit").val(a.id_izin),$("#keteranganEdit").val(a.keterangan),a.jenis_izin=="TM"){$("#conditional_fieldEdit").empty().append(`
                        <div class="form-group">
                            <label for="rencana_mulai_or_masukEdit" id="label_rencana_mulai_or_masukEdit">Rencana
                                Mulai</label>
                            <input type="date" name="rencana_mulai_or_masukEdit" id="rencana_mulai_or_masukEdit"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="rencana_selesai_or_keluarEdit" id="label_rencana_selesai_or_keluarEdit">Rencana
                                Selesai</label>
                            <input type="date" name="rencana_selesai_or_keluarEdit"
                                id="rencana_selesai_or_keluarEdit" class="form-control" required>
                        </div>    
                        <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                                Selesai di tanggal yang sama!</small>     
                    `);let i=moment(a.rencana_mulai_or_masuk).format("YYYY-MM-DD");$("#rencana_mulai_or_masukEdit").attr("min",moment().format("YYYY-MM-DD")),$("#rencana_selesai_or_keluarEdit").attr("min",i),$("#rencana_mulai_or_masukEdit").on("change",function(){let l=$(this).val();$("#rencana_selesai_or_keluarEdit").attr("min",l),l>$("#rencana_selesai_or_keluarEdit").val()&&$("#rencana_selesai_or_keluarEdit").val(l)}),$("#rencana_selesai_or_keluarEdit").on("change",function(){$(this).val()<$("#rencana_mulai_or_masukEdit").val()&&$(this).val($("#rencana_mulai_or_masukEdit").val())}),$("#rencana_mulai_or_masukEdit").val(moment(a.rencana_mulai_or_masuk).format("YYYY-MM-DD")),$("#rencana_selesai_or_keluarEdit").val(moment(a.rencana_selesai_or_keluar).format("YYYY-MM-DD"))}else if(a.jenis_izin=="SH"){$("#conditional_fieldEdit").empty().append(`
                        <div class="form-group">
                            <label for="masuk_or_keluarEdit" id="label_masuk_or_keluarEdit">Masuk / Keluar</label>
                            <select name="masuk_or_keluarEdit" id="masuk_or_keluarEdit" class="form-control" style="width:100%;" required>
                                <option value="M">Masuk</option>
                                <option value="K">Keluar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rencana_masuk_or_keluarEdit" id="label_rencana_masuk_or_keluar">Jam Masuk / Keluar</label>
                            <input type="datetime-local" name="rencana_masuk_or_keluarEdit" id="rencana_masuk_or_keluarEdit"
                                class="form-control" required>
                        </div>
                    `);let i=moment(a.rencana_mulai_or_masuk?a.rencana_mulai_or_masuk:a.rencana_selesai_or_keluar).format("YYYY-MM-DDT00:00");$("#rencana_masuk_or_keluarEdit").attr("min",i),$("#masuk_or_keluarEdit").select2({dropdownParent:$("#modal-pengajuan-izin-edit")}),$("#masuk_or_keluarEdit").val(a.masuk_or_keluar).trigger("change"),$("#rencana_masuk_or_keluarEdit").val(moment(a.rencana_mulai_or_masuk?a.rencana_mulai_or_masuk:a.rencana_selesai_or_keluar).format("YYYY-MM-DDTHH:mm"))}else if(a.jenis_izin=="KP"){$("#conditional_fieldEdit").empty().append(`
                        <div class="form-group">
                            <label for="rencana_selesai_or_keluarEdit" id="label_rencana_selesai_or_keluarEdit">Rencana
                                Keluar</label>
                            <input type="datetime-local" name="rencana_selesai_or_keluarEdit"
                                id="rencana_selesai_or_keluarEdit" class="form-control" required>
                        </div>  
                        <div class="form-group">
                            <label for="rencana_mulai_or_masukEdit" id="label_rencana_mulai_or_masukEdit">Rencana Kembali</label>
                            <input type="datetime-local" name="rencana_mulai_or_masukEdit" id="rencana_mulai_or_masukEdit"
                                class="form-control" required>
                        </div>
                        <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                                Selesai di tanggal yang sama!</small>     
                    `);let i=moment(a.rencana_selesai_or_keluar?a.rencana_selesai_or_keluar:a.rencana_mulai_or_masuk).format("YYYY-MM-DDT00:00");$("#rencana_selesai_or_keluarEdit").attr("min",moment().format("YYYY-MM-DDT00:00")),$("#rencana_mulai_or_masukEdit").attr("min",i),$("#rencana_selesai_or_keluarEdit").on("change",function(){let l=moment($(this).val()).format("YYYY-MM-DDT00:00");$("#rencana_mulai_or_masukEdit").attr("min",l),l>$("#rencana_mulai_or_masukEdit").val()&&$("#rencana_mulai_or_masukEdit").val(l)}),$("#rencana_mulai_or_masukEdit").on("change",function(){moment($(this).val()).format("YYYY-MM-DDTHH:mm")<$("#rencana_selesai_or_keluarEdit").val()&&$(this).val($("#rencana_selesai_or_keluarEdit").val())}),$("#rencana_selesai_or_keluarEdit").val(moment(a.rencana_selesai_or_keluar).format("YYYY-MM-DDTHH:mm")),$("#rencana_mulai_or_masukEdit").val(moment(a.rencana_mulai_or_masuk).format("YYYY-MM-DDTHH:mm"))}else if(a.jenis_izin=="PL"){$("#conditional_fieldEdit").empty().append(`
                        <div class="form-group">
                            <label for="rencana_selesai_or_keluarEdit" id="label_rencana_selesai_or_keluarEdit">Jam Keluar</label>
                            <input type="datetime-local" name="rencana_selesai_or_keluarEdit" id="rencana_selesai_or_keluarEdit"
                                class="form-control" required>
                        </div>
                    `);let i=moment(a.rencana_selesai_or_keluar?a.rencana_selesai_or_keluar:a.rencana_mulai_or_masuk).format("YYYY-MM-DDT00:00");$("#rencana_selesai_or_keluarEdit").attr("min",i),$("#rencana_selesai_or_keluarEdit").val(moment(a.rencana_selesai_or_keluar?a.rencana_selesai_or_keluar:a.rencana_mulai_or_masuk).format("YYYY-MM-DDTHH:mm"))}o(),g()},error:function(r,a,i){t({icon:"error",title:r.responseJSON.message})}})}),$(".btnEdit").on("click",function(){g()}),$(".btnCloseEdit").on("click",function(){c()});var y={backdrop:!0,keyboard:!1},v=new bootstrap.Modal(document.getElementById("modal-pengajuan-izin-edit"),y);function g(){v.show()}function c(){v.hide(),T()}function T(){$("#conditional_fieldEdit").empty(),$("#keteranganEdit").val("")}var w={backdrop:!0,keyboard:!1},b=new bootstrap.Modal(document.getElementById("modal-aktual-pengajuan-izin"),w);function j(){b.show()}function O(){b.hide(),C()}function C(){$("#id_izinAktual").val(""),$("#aktual_mulai_or_masukAktual").val(""),$("#aktual_selesai_or_keluarAktual").val("")}$("#pengajuan-izin-table").on("click",".btnDone",function(){s();var e=$(this).data("id-izin"),n=base_url+"/izine/pengajuan-izin/get-data-izin/"+e;$.ajax({url:n,type:"GET",success:function(r){var a=r.data;$("#id_izinAktual").val(a.id_izin);let i=moment(a.rencana_mulai_or_masuk).format("YYYY-MM-DD");$("#aktual_mulai_or_masukAktual").attr("min",i),$("#aktual_selesai_or_keluarAktual").attr("min",i),$("#aktual_mulai_or_masukAktual").on("change",function(){let l=$(this).val();$("#aktual_selesai_or_keluarAktual").attr("min",l),l>$("#aktual_selesai_or_keluarAktual").val()&&$("#aktual_selesai_or_keluarAktual").val(l)}),$("#aktual_selesai_or_keluarAktual").on("change",function(){$(this).val()<$("#aktual_mulai_or_masukAktual").val()&&$(this).val($("#aktual_mulai_or_masukAktual").val())}),$("#aktual_mulai_or_masukAktual").val(moment(a.rencana_mulai_or_masuk).format("YYYY-MM-DD")),$("#aktual_selesai_or_keluarAktual").val(moment(a.rencana_selesai_or_keluar).format("YYYY-MM-DD")),o(),j()},error:function(r,a,i){t({icon:"error",title:r.responseJSON.message})}})}),$("#form-aktual-pengajuan-izin").on("submit",function(e){s(),e.preventDefault();let n=$("#id_izinAktual").val(),r=base_url+"/izine/pengajuan-izin/done/"+n;var a=new FormData($("#form-aktual-pengajuan-izin")[0]);$.ajax({url:r,data:a,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(i){_(),t({title:i.message}),u(),O(),o()},error:function(i,l,m){o(),t({icon:"error",title:i.responseJSON.message})}})}),$(".btnDone").on("click",function(){openFormAktual()}),$(".btnCloseEdit").on("click",function(){c()});var x={backdrop:!0,keyboard:!1},h=new bootstrap.Modal(document.getElementById("modal-show-qrcode"),x);function J(){h.show()}function N(){h.hide(),u()}$(".btnCloseQrcode").on("click",function(){s();var e=base_url+"/delete-qrcode-img";$.ajax({url:e,type:"POST",data:{_method:"delete",file_path:Y},dataType:"JSON",success:function(n){console.log(n),o(),N()},error:function(n,r,a){t({icon:"error",title:n.responseJSON.message})}})});let Y;$("#pengajuan-izin-table").on("click",".btnShowQR",function(){s();var e=$(this).data("id-izin"),n=base_url+"/generate-qrcode";let r=new FormData;r.append("id",e),$.ajax({url:n,data:r,method:"POST",contentType:!1,processData:!1,dataType:"JSON",success:function(a){let i=a.data;Y=i,$("#qr-code").attr("src",i),J(),o()},error:function(a,i,l){t({icon:"error",title:a.responseJSON.message})}})})});
