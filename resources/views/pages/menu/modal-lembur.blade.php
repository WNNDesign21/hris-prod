<div class="modal fade" id="modal-lembur">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header p-4">
                <h4 class="modal-title">Slip Lembur</h4>
                <button type="button" class="btn-close btnCloseLembur" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('home.export-slip-lembur') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Periode Lembur</label>
                        <input type="month" class="form-control" id="periode-slip" name="periode_slip">
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-sm btn-success" id="btnSlipLembur"><i
                                class="ti-download"></i>
                            Unduh</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
