$(function() {
    var table = $('#kontrak-ringkasan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/master-data/kontrak/ringkasan", // Use direct URL for AJAX
        columns: [
            {data: 'ni_karyawan', name: 'karyawans.ni_karyawan'},
            {data: 'nama_karyawan', name: 'karyawans.nama'},
            {data: 'tanggal_masuk', name: 'karyawans.tanggal_mulai'},
            {data: 'dept', name: 'departemens.nama'},
            {data: 'tanggal_penetapan_jabatan', name: 'tanggal_penetapan_jabatan', orderable: false, searchable: false},
            {data: 'tanggal_penetapan_kartap', name: 'tanggal_penetapan_kartap', orderable: false, searchable: false},
            {data: 'lama_kerja', name: 'lama_kerja'},
            {data: 'jenis_kontrak', name: 'karyawans.jenis_kontrak'},
            {data: 'jumlah_kontrak', name: 'jumlah_kontrak'},
            {data: 'tanggal_berakhir', name: 'karyawans.tanggal_selesai'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('.btnReload').on('click', function() {
        table.ajax.reload();
    });

    // Event listener for End Kontrak button
    $('#kontrak-ringkasan-table').on('click', '.btnEndKontrak', function() {
        var id_kontrak = $(this).data('id');
        Swal.fire({
            title: 'Akhiri Kontrak?',
            text: "Kontrak ini akan diakhiri pada hari ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Akhiri!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/master-data/kontrak/end-kontrak/' + id_kontrak,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // Assuming CSRF token is available
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            response.message,
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON.message,
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Event listener for Edit button
    $('#kontrak-ringkasan-table').on('click', '.btnEditKontrak', function() {
        var id_kontrak = $(this).data('id');
        // Redirect to an edit page or open a modal
        window.location.href = '/master-data/kontrak/edit/' + id_kontrak; // Example redirect
        // Or if using modal:
        // $.ajax({
        //     url: '/master-data/kontrak/edit-form/' + id_kontrak,
        //     type: 'GET',
        //     success: function(response) {
        //         // Populate modal with response.data and show modal
        //     },
        //     error: function(xhr) {
        //         Swal.fire('Error!', xhr.responseJSON.message, 'error');
        //     }
        // });
    });
});