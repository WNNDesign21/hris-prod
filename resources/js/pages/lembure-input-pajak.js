$(document).ready(function() {
    const pphTable = $('#pph-lembur-table');
    const datatableUrl = pphTable.data('datatable-url');
    const updateUrlTemplate = pphTable.data('update-url-template');
    const destroyUrlTemplate = pphTable.data('destroy-url-template');
    const csrfToken = pphTable.data('csrf-token');

    // --- Upload Form ---
    // Set default periode for upload form
    const nowForUpload = new Date();
    const yearForUpload = nowForUpload.getFullYear();
    const monthForUpload = (nowForUpload.getMonth() + 1).toString().padStart(2, '0');
    const defaultPeriodeUpload = `${yearForUpload}-${monthForUpload}`;
    $('#periode-upload').val(defaultPeriodeUpload);


    // --- DataTable ---
    // Set default periode for filter
    const nowForFilter = new Date();
    const yearForFilter = nowForFilter.getFullYear();
    const monthForFilter = (nowForFilter.getMonth() + 1).toString().padStart(2, '0');
    const defaultPeriodeFilter = `${yearForFilter}-${monthForFilter}`;
    $('#periode-filter').val(defaultPeriodeFilter);

    // Initialize DataTable
    let dataTable = $('#pph-lembur-table').DataTable({
        processing: true,
        serverSide: false, // Using client-side for simplicity
        ajax: {
            url: datatableUrl,
            type: "POST",
            data: function(d) {
                d.periode = $('#periode-filter').val(); // Use the filter input
                d._token = csrfToken;
            }
        },
        columns: [
            { data: 'ni_karyawan', name: 'ni_karyawan' },
            { data: 'nama', name: 'nama' },
            { data: 'potongan_pph', name: 'potongan_pph', render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },
            { data: 'id', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                return `
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="${data}" data-pph="${row.potongan_pph}"><i class="fa fa-edit"></i> Edit</button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${data}"><i class="fa fa-trash"></i> Delete</button>
                    </div>
                `;
            }}
        ]
    });

    // Trigger initial load
    dataTable.ajax.reload();

    // Reload DataTable when filter button is clicked
    $('#filter-btn').on('click', function() {
        dataTable.ajax.reload();
    });

    // Handle file upload form submission
    $('#form-upload-pph').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Berhasil', response.message, 'success');
                // Reload table after successful upload
                dataTable.ajax.reload();
                $('#form-upload-pph')[0].reset();
                // Reset upload periode to default
                $('#periode-upload').val(defaultPeriodeUpload);
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMessage = xhr.responseJSON.errors.join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                Swal.fire('Gagal', errorMessage, 'error');
            }
        });
    });

    // Handle Edit button click
    $('#pph-lembur-table').on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let currentPph = $(this).data('pph');
        let url = updateUrlTemplate.replace(':id', id);
        
        Swal.fire({
            title: 'Update Potongan PPH',
            input: 'number',
            inputValue: currentPph,
            showCancelButton: true,
            confirmButtonText: 'Update',
            showLoaderOnConfirm: true,
            preConfirm: (newPph) => {
                return $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        potongan_pph: newPph
                    }
                })
                .catch(error => {
                    let msg = 'Request failed';
                    if(error.responseJSON && error.responseJSON.message) {
                        msg = error.responseJSON.message;
                    }
                    Swal.showValidationMessage(msg);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Berhasil', result.value.message, 'success');
                dataTable.ajax.reload();
            }
        });
    });

    // Handle Delete button click
    $('#pph-lembur-table').on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let url = destroyUrlTemplate.replace(':id', id);

        Swal.fire({
            title: 'Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        Swal.fire('Dihapus!', response.message, 'success');
                        dataTable.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });

});