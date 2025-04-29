<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah User</h4>
                <button type="button" class="btn-close btnCloseInput" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('superuser.user.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-input">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="organisasi">Organisasi</label>
                            <select name="organisasi" id="organisasi" class="form-control" required style="width:100%;">
                                <option value="" disabled selected>Pilih Organisasi</option>
                                @foreach ($organisasis as $organisasi)
                                    <option value="{{ $organisasi->id_organisasi }}">{{ $organisasi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="roles">Role</label>
                            <select name="roles" id="roles" class="form-control" required style="width:100%;">
                                <option value="" disabled selected>Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Tambah</button>
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
