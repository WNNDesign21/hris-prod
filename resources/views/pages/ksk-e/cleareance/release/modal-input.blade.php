<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data" id="form-input">
                <div class="modal-header">
                    <h4 class="modal-title">Approval Cleareance</h4>
                    <button type="button" class="btn-close btnClose" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @csrf
                    <div class="form-group">
                        <label for="dept_it">Departemen IT</label>
                        <select name="dept_it" id="dept_it" class="form-control" style="width: 100%">
                            <option value="">TIDAK DIPERLUKAN</option>
                            <option value="{{ $deptIT?->karyawan_id }}" selected>{{ $deptIT?->nama_karyawan }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dept_fat">Departemen Finance</label>
                        <select name="dept_fat" id="dept_fat" class="form-control" style="width: 100%">
                            <option value="">TIDAK DIPERLUKAN</option>
                            <option value="{{ $deptFAT?->karyawan_id }}" selected>{{ $deptFAT?->nama_karyawan }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dept_ga">Departemen GA</label>
                        <select name="dept_ga" id="dept_ga" class="form-control" style="width: 100%">
                            <option value="">TIDAK DIPERLUKAN</option>
                            <option value="{{ $deptGA?->karyawan_id }}" selected>{{ $deptGA?->nama_karyawan }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dept_hr">Departemen HR</label>
                        <select name="dept_hr" id="dept_hr" class="form-control" style="width: 100%">
                            <option value="">TIDAK DIPERLUKAN</option>
                            <option value="{{ $deptHR?->karyawan_id }}" selected>{{ $deptHR?->nama_karyawan }}</option>
                        </select>
                    </div>
                    <p class="text-muted">Note : Abaikan select option jika tidak berkewajiban untuk melakukan approval
                        pada pihak yang dipilih</p>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
