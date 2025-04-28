<!-- Validation wizard -->
<div class="box">
    <div class="box-header with-border">
        <h4 class="box-title">Pembuatan Organisasi Baru</h4>
        <h6 class="box-subtitle">Lakukan beberapa konfigurasi awal untuk pembuatan organisasi baru.</h6>
    </div>
    <!-- /.box-header -->
    <div class="box-body wizard-content">
        <form action="{{ route('superuser.organisasi.store') }}" class="validation-wizard wizard-circle" id="form-input"
            method="POST">
            <h6>Organisasi</h6>
            <section>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="nama" class="form-label">Nama Organisasi</label>
                            <input type="text" class="form-control required" id="nama" name="nama">
                        </div>
                        <div class="form-group">
                            <label for="alamat" class="form-label">Alamat Organisasi</label>
                            <textarea name="alamat" id="alamat" rows="6" class="form-control required"></textarea>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Step 2 -->
            <h6>Akun Personalia</h6>
            <section>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personalia_email" class="form-label">Email</label>
                            <input type="email" class="form-control required" id="personalia_email"
                                name="personalia_email">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personalia_username" class="form-label">Username</label>
                            <input type="text" class="form-control required" id="personalia_username"
                                name="personalia_username">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personalia_password" class="form-label">Password</label>
                            <input type="password" class="form-control required" id="personalia_password"
                                name="personalia_password">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personalia_password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control required" id="personalia_password_confirmation"
                                name="personalia_password_confirmation">
                        </div>
                    </div>
                </div>
            </section>
            <!-- Step 3 -->
            <h6>Akun Security</h6>
            <section>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="security_email" class="form-label">Email</label>
                            <input type="email" class="form-control required" id="security_email"
                                name="security_email">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="security_username" class="form-label">Username</label>
                            <input type="text" class="form-control required" id="security_username"
                                name="security_username">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="security_password" class="form-label">Password</label>
                            <input type="password" class="form-control required" id="security_password"
                                name="security_password">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="security_password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control required" id="security_password_confirmation"
                                name="security_password_confirmation">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <!-- /.box-body -->
</div>
