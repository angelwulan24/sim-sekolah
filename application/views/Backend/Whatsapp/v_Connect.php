<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Koneksi WhatsApp Gateway</h3>
        </div>
	    <div class="box-body">
            <div class="text-center">
                <div id="qr_code_area">
                    <!-- Placeholder for QR Code or Status -->
                    <p class="text-muted">Klik tombol di bawah untuk menghubungkan WhatsApp</p>
                    <i class="fa fa-whatsapp fa-5x text-success"></i>
                </div>
                <br>
                <button class="btn btn-success btn-lg" id="btn-connect" onclick="getQr()">Hubungkan WhatsApp</button>
            </div>
            <div class="alert alert-info" style="margin-top: 20px;">
                <h4><i class="icon fa fa-info"></i> Informasi</h4>
                <p>Fitur ini digunakan untuk menghubungkan nomor WhatsApp Admin sebagai pengirim notifikasi otomatis sistem.</p>
                <p>1. Klik "Hubungkan WhatsApp"</p>
                <p>2. Scan QR Code yang muncul menggunakan WhatsApp di HP Admin</p>
                <p>3. Setelah terhubung, sistem akan otomatis menggunakan nomor tersebut untuk mengirim pesan.</p>
            </div>
	    </div>
    </div>

    <div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">Uji Coba Pengiriman</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label>Nomor WhatsApp (Contoh: 08123456xxx)</label>
                <div class="input-group">
                    <input type="text" id="test_phone" class="form-control" placeholder="Masukkan nomor untuk tes">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onclick="testKirim()">Kirim Pesan Tes</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Socket.io Client -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:5001');

    socket.on('connection_status', (data) => {
        if (data.status === 'connected') {
            $('#qr_code_area').html('<i class="fa fa-check-circle fa-5x text-success"></i><br><h3>Terhubung</h3><p>Nomor Admin siap digunakan.</p>');
            $('#btn-connect').hide();
            Swal({
                title: 'Terhubung!',
                text: 'Koneksi ke WhatsApp Gateway Berhasil.',
                type: 'success',
                timer: 2500,
                showConfirmButton: false
            });
        } else if (data.status === 'disconnected') {
            $('#qr_code_area').html('<p class="text-muted">Koneksi Terputus</p><i class="fa fa-whatsapp fa-5x text-danger"></i>');
            $('#btn-connect').show().text('Menghubungkan...');
        } else if (data.status === 'logged_out') {
            $('#qr_code_area').html('<i class="fa fa-times-circle fa-5x text-danger"></i><br><h3>Keluar</h3><p>Silakan mulai ulang Gateway service dan scan QR ulang.</p>');
            $('#btn-connect').show().text('Hubungkan WhatsApp').attr('disabled', false);
        }
    });

    socket.on('qr_code', (url) => {
        $('#qr_code_area').html('<img src="' + url + '" alt="QR Code"><br><p>Scan QR Code ini</p>');
        $('#btn-connect').text('Menunggu Hasil Scan...').attr('disabled', true).show();
    });

    function getQr() {
        $('#btn-connect').text('Memeriksa Gateway...').attr('disabled', true);
        if(!socket.connected) {
            $('#qr_code_area').html('<p class="text-danger">Gagal terhubung ke Service Gateway. Pastikan Service Node.js (port 5001) sedang berjalan.</p>');
            $('#btn-connect').text('Coba Ulang').attr('disabled', false);
        } else {
             $('#qr_code_area').html('<p>Gateway terhubung. Menunggu QR Code...</p>');
        }
    }
    function testKirim() {
        const phone = $('#test_phone').val();
        if(!phone) {
            Swal({ title: 'Gagal', text: 'Masukkan nomor telpon', type: 'error' });
            return;
        }

        Swal({ title: 'Mengirim...', onOpen: () => { Swal.showLoading() } });

        $.ajax({
            url: '<?= base_url("Whatsapp/test_send") ?>',
            type: 'POST',
            data: { phone: phone },
            dataType: 'json',
            success: function(res) {
                if(res.status) {
                    Swal({ title: 'Berhasil', text: 'Pesan tes berhasil dikirim ke ' + phone, type: 'success' });
                } else {
                    Swal({ title: 'Gagal', text: res.message, type: 'error' });
                }
            },
            error: function() {
                Swal({ title: 'Error', text: 'Terjadi kesalahan sistem', type: 'error' });
            }
        });
    }
</script>
