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
</div>

<script>
function getQr() {
    $('#btn-connect').text('Menghubungkan...').attr('disabled', true);
    
    // Simulation: In a real app, this would fetch the QR from the controller
    setTimeout(function() {
        $('#qr_code_area').html('<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=SimulatedConnect" alt="QR Code"><br><p>Scan QR Code ini</p>');
        $('#btn-connect').text('Refresh QR');
        $('#btn-connect').attr('disabled', false);
        
        // Simulate "Connected" after some time checking
        setTimeout(function(){
            swal("Terhubung!", "WhatsApp Admin Berhasil Terhubung", "success");
             $('#qr_code_area').html('<i class="fa fa-check-circle fa-5x text-success"></i><br><h3>Terhubung</h3><p>Nomor Admin siap digunakan.</p>');
             $('#btn-connect').hide();
        }, 10000); 

    }, 1500); 
}
</script>
