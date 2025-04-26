<!-- modal Area -->
<div class="modal fade" id="modal-upload">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Data</h4>
                <button type="button" class="btn-close btnCloseUpload" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('master-data.karyawan.upload-karyawan') }}" enctype="multipart/form-data"
                    id="form-upload">
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <label for="method">Method</label>
                            <select name="method" id="method" class="form-control" style="width: 100%" required>
                                <option value="I">Insert</option>
                                <option value="U">Update</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="karyawan_file">File</label>
                            <input type="file" name="karyawan_file" id="karyawan_file" class="form-control"
                                placeholder="Masukkan File Karyawan" style="width: 100%;">
                            <small class="text-muted">Download template upload karyawan <a
                                    href="{{ asset('template/template_upload_karyawan.xlsx') }}">disini</a>.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end gap-1">
                            <button type="submit" class="waves-effect waves-light btn btn-primary"><i
                                    class="fas fa-upload"></i> Upload</button>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row mt-5">
                    <h4>Upload Log Activity</h4>
                    <div class="table-responsive">
                        <table id="upload-table" class="table table-striped display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Causer</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
