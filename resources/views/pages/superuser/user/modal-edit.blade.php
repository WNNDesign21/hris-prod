<!-- modal Area -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit User</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit">
                        @method('PATCH')
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="idEdit" id="idEdit" class="form-control">
                            <label for="emailEdit">Email</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="email" name="emailEdit" id="emailEdit" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="usernameEdit">Username</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="text" name="usernameEdit" id="usernameEdit" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="passwordEdit">Password</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="password" name="passwordEdit" id="passwordEdit" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_passwordEdit">Confirm Password</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="password" name="confirm_passwordEdit" id="confirm_passwordEdit"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="organisasiEdit">Organisasi</label>
                            <select name="organisasiEdit" id="organisasiEdit" class="form-control" required
                                style="width:100%;">
                                @foreach ($organisasis as $organisasi)
                                    <option value="{{ $organisasi->id_organisasi }}">{{ $organisasi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rolesEdit">Role</label>
                            <select name="rolesEdit[]" id="rolesEdit" class="form-control" required style="width:100%;"
                                multiple>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
