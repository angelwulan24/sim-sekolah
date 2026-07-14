<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="box box-primary">
            <div class="box-body box-profile">
                <div class="text-center">
                    <?php if(!empty($student->foto)): ?>
                        <img class="profile-user-img img-responsive img-circle" src="<?= base_url('assets/images/siswa/' . $student->foto); ?>" alt="User profile picture" style="width:120px; height:120px; object-fit:cover; border: 3px solid #3c8dbc;">
                    <?php else: ?>
                        <div class="profile-user-img img-circle" style="width:120px; height:120px; line-height:110px; display:inline-block; background:#f4f4f4; border: 3px solid #ddd;">
                            <i class="fa fa-user fa-5x text-muted" style="vertical-align:middle;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h3 class="profile-username text-center"><?= $student->name; ?></h3>
                <p class="text-muted text-center">NIS: <?= $student->nis; ?></p>
                
                <hr>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Kelas</b> <span class="pull-right label label-primary"><?= $student->kelas; ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Status Akun</b> <span class="pull-right label label-success text-uppercase"><?= $student->status; ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Tempat Lahir</b> <span class="pull-right"><?= $student->tempat; ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Tanggal Lahir</b> <span class="pull-right"><?= !empty($student->tanggal) ? date('d-m-Y', strtotime($student->tanggal)) : '-'; ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Orangtua / Wali</b> <span class="pull-right"><?= $student->orangtua_wali; ?></span>
                    </li>
                    <li class="list-group-item" style="border-bottom:none;">
                        <b>No. Telpon</b> <span class="pull-right"><?= $student->telpon; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Tagihan SPP Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calendar"></i> Tagihan SPP</h3>
                <div class="box-tools pull-right">
                    <form action="<?= base_url('StudentArea') ?>" method="GET" class="form-inline" style="display:inline-block;">
                        <label style="font-weight: normal; margin-right: 5px;">Tahun Ajaran: </label>
                        <select name="tahun" class="form-control input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Tahun --</option>
                            <?php foreach($tahun_list as $tl): ?>
                                <option value="<?= $tl->tahun_ajaran ?>" <?= ($selected_tahun == $tl->tahun_ajaran) ? 'selected' : '' ?>><?= $tl->tahun_ajaran ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <div class="alert alert-info" style="margin-bottom: 15px;">
                    <i class="icon fa fa-info-circle"></i> <b>Info Pembayaran:</b> Batas pembayaran SPP setiap bulannya paling lambat <b>tanggal 14</b>.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th style="width: 30px;"><input type="checkbox" id="check-all-spp"></th>
                                <th style="width: 10px;">No</th>
                                <th>Bulan</th>
                                <th>Tahun Ajaran</th>
                                <th>Nominal</th>
                                <th>Tenggat Waktu</th>
                                <th>Status</th>
                                <th>Waktu Bayar</th>
                                <th>Tempat Bayar</th>
                                <th style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($tagihan_spp as $s) { 
                            ?>
                            <tr>
                                <td style="vertical-align:middle;">
                                    <?php if($s->status == 'Belum Lunas') { ?>
                                        <input type="checkbox" class="check-item check-spp" 
                                               data-jenis="SPP" 
                                               data-label_bayar="<?= str_replace("SPP - ", "", $s->jenis_tagihan) ?>" 
                                               data-nominal="<?= $s->nominal ?>"
                                               data-tagihan_id="<?= $s->id ?>">
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td style="vertical-align:middle;"><?=$no++?></td>
                                <td style="vertical-align:middle; font-weight:bold;"><?=str_replace("SPP - ", "", $s->jenis_tagihan)?></td>
                                <td style="vertical-align:middle;"><?=$s->tahun_ajaran ? $s->tahun_ajaran : '-'?></td>
                                <td style="vertical-align:middle;">Rp <?=number_format($s->nominal,0,',','.')?></td>
                                <td style="vertical-align:middle;">
                                    <?=!empty($s->tenggat_waktu) ? date('d M Y', strtotime($s->tenggat_waktu)) : '-'?>
                                </td>
                                <td style="vertical-align:middle;">
                                    <?php if($s->status == 'Lunas') { ?>
                                        <span class="label label-success">Lunas</span>
                                    <?php } else { ?>
                                        <span class="label label-danger">Belum Lunas</span>
                                    <?php } ?>
                                </td>
                                <td style="vertical-align:middle;"><?=$s->waktu_bayar ? date('d-m-Y H:i', strtotime($s->waktu_bayar)) : '-'?></td>
                                <td style="vertical-align:middle;"><?=$s->status == 'Lunas' ? 'Online' : '-'?></td>
                                <td style="vertical-align:middle;">
                                    <?php if($s->status == 'Belum Lunas') { ?>
                                        <button class="btn btn-warning btn-xs btn-flat shadow pay-button" 
                                                data-jenis="SPP" 
                                                data-label_bayar="<?= str_replace("SPP - ", "", $s->jenis_tagihan) ?>" 
                                                data-nominal="<?= $s->nominal ?>"
                                                data-tagihan_id="<?= $s->id ?>">
                                            <i class="fa fa-money"></i> Bayar
                                        </button>
                                    <?php } else { ?>
                                        <a href="<?=base_url('StudentArea/print_tagihan/'.$s->id)?>" class="btn btn-abu-tua btn-xs btn-flat btn-print-tagihan">
                                            <i class="fa fa-print"></i> Cetak Bukti
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (count($tagihan_spp) == 0): ?>
                                <tr><td colspan="10" class="text-muted">Tidak ada tagihan SPP.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Tagihan Lainnya Box -->
        <div class="box box-warning" style="margin-top: 20px;">
            <div class="box-header with-border">
                <i class="fa fa-list-alt"></i>
                <h3 class="box-title">Tagihan Lainnya</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th style="width: 30px;"><input type="checkbox" id="check-all-lain"></th>
                                <th style="width: 10px;">No</th>
                                <th>Jenis Tagihan</th>
                                <th>Tahun Ajaran</th>
                                <th>Tenggat Waktu</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Waktu Bayar</th>
                                <th>Tempat Bayar</th>
                                <th style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no2 = 1;
                            foreach($tagihan_lainnya as $l) { 
                            ?>
                            <tr>
                                <td style="vertical-align:middle;">
                                    <?php if($l->status == 'Belum Lunas') { ?>
                                        <input type="checkbox" class="check-item check-lain" 
                                               data-jenis="<?= explode(' ', $l->jenis_tagihan)[0] ?>" 
                                               data-label_bayar="<?= $l->tahun_ajaran ?>" 
                                               data-nominal="<?= $l->nominal ?>"
                                               data-tagihan_id="<?= $l->id ?>">
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td style="vertical-align:middle;"><?=$no2++?></td>
                                <td style="vertical-align:middle; font-weight:bold;"><?=$l->jenis_tagihan?></td>
                                <td style="vertical-align:middle;"><?=$l->tahun_ajaran ? $l->tahun_ajaran : '-'?></td>
                                <td style="vertical-align:middle;">
                                    <?=!empty($l->tenggat_waktu) ? date('d M Y', strtotime($l->tenggat_waktu)) : '-'?>
                                </td>
                                <td style="vertical-align:middle;">Rp <?=number_format($l->nominal,0,',','.')?></td>
                                <td style="vertical-align:middle;">
                                    <?php if($l->status == 'Lunas') { ?>
                                        <span class="label label-success">Lunas</span>
                                    <?php } else { ?>
                                        <span class="label label-danger">Belum Lunas</span>
                                    <?php } ?>
                                </td>
                                <td style="vertical-align:middle;"><?=$l->waktu_bayar ? date('d-m-Y H:i', strtotime($l->waktu_bayar)) : '-'?></td>
                                <td style="vertical-align:middle;"><?=$l->status == 'Lunas' ? 'Online' : '-'?></td>
                                <td style="vertical-align:middle;">
                                    <?php if($l->status == 'Belum Lunas') { ?>
                                        <button class="btn btn-warning btn-xs btn-flat shadow pay-button" 
                                                data-jenis="<?= explode(' ', $l->jenis_tagihan)[0] ?>" 
                                                data-label_bayar="<?= $l->tahun_ajaran ?>" 
                                                data-nominal="<?= $l->nominal ?>"
                                                data-tagihan_id="<?= $l->id ?>">
                                            <i class="fa fa-money"></i> Bayar
                                        </button>
                                    <?php } else { ?>
                                        <a href="<?=base_url('StudentArea/print_tagihan/'.$l->id)?>" class="btn btn-abu-tua btn-xs btn-flat btn-print-tagihan">
                                            <i class="fa fa-print"></i> Cetak Bukti
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (count($tagihan_lainnya) == 0): ?>
                                <tr><td colspan="10" class="text-muted">Tidak ada tagihan lainnya.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Bulk Pay Button -->
<div id="bulk-pay-container" style="display:none; position: fixed; bottom: 20px; right: 20px; z-index: 1000; background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.2); border-left: 5px solid var(--pastel-green);">
    <div style="margin-bottom: 10px; color: var(--text-dark);">
        <strong id="bulk-count">0</strong> Item Terpilih<br>
        <span style="font-size: 1.2em; font-weight: bold; color: var(--pastel-green-hover);">Total: Rp <span id="bulk-total">0</span></span>
    </div>
    <button class="btn btn-primary btn-block" id="btn-bulk-pay">
        <i class="fa fa-shopping-cart"></i> Bayar Sekarang
    </button>
</div>

<style>
    /* Match Admin Font Family and styles */
    body, h1, h2, h3, h4, h5, h6, .box, .table, .btn, .label {
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
    }
    
    /* Table Headers - Match Admin standard tables */
    .table > thead > tr > th { 
        vertical-align: middle; 
        font-weight: bold;
        border-bottom: 2px solid #f4f4f4 !important;
    }
    
    .label {
        font-weight: bold;
    }
    
    /* Action Buttons */
    .btn-flat { border-radius: 4px !important; }
</style>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $midtrans_client_key ?>"></script>
<script>
    function updateBulkUI() {
        var selected = $('.check-item:checked');
        var count = selected.length;
        var total = 0;
        
        selected.each(function(){
            total += parseInt($(this).data('nominal'));
        });
        
        $('#bulk-count').text(count);
        $('#bulk-total').text(total.toLocaleString('id-ID'));
        
        if (count > 0) {
            $('#bulk-pay-container').fadeIn();
        } else {
            $('#bulk-pay-container').fadeOut();
        }
    }

    $(document).on('change', '.check-item', function() {
        updateBulkUI();
    });

    $('#check-all-spp').change(function() {
        $('.check-spp').prop('checked', this.checked);
        updateBulkUI();
    });

    $('#check-all-lain').change(function() {
        $('.check-lain').prop('checked', this.checked);
        updateBulkUI();
    });

    // Bulk Pay Logic
    $('#btn-bulk-pay').click(function(){
        var items = [];
        $('.check-item:checked').each(function(){
            items.push({
                jenis: $(this).data('jenis'),
                label_bayar: $(this).data('label_bayar'),
                nominal: $(this).data('nominal'),
                tagihan_id: $(this).data('tagihan_id')
            });
        });
        
        if (items.length === 0) return;
        
        var btn = $(this);
        
        Swal({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin ingin membayar tagihan yang dipilih?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Bayar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.value) {
                btn.html('<i class="fa fa-spinner fa-spin"></i> Memproses...').attr('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('StudentArea/get_token_bulk') ?>',
                    type: 'POST',
                    data: { items: JSON.stringify(items) },
                    dataType: 'json',
                    success: function(response){
                        btn.html('<i class="fa fa-shopping-cart"></i> Bayar Sekarang').attr('disabled', false);
                        
                        if(response.error){
                            Swal({ title: 'Gagal', text: response.error, type: 'error' });
                            return;
                        }
                        
                        if(response.token){
                            snap.pay(response.token, {
                                onSuccess: function(result){
                                    console.log('Payment success, verifying...', result);
                                    $.ajax({
                                        url: '<?= base_url('StudentArea/finish_payment') ?>',
                                        type: 'POST',
                                        data: { order_id: result.order_id },
                                        dataType: 'json',
                                        success: function(val){
                                            if(val.status == 'success') {
                                                Swal({
                                                    title: 'Lunas!',
                                                    text: 'Pembayaran berhasil dikonfirmasi',
                                                    type: 'success',
                                                    showCancelButton: true,
                                                    confirmButtonText: '<i class="fa fa-print"></i> Cetak Bukti',
                                                    cancelButtonText: 'Tutup',
                                                    confirmButtonColor: '#28a745'
                                                }).then((res) => {
                                                    if(res.value) {
                                                        Swal({
                                                            title: 'Berhasil!',
                                                            text: 'Bukti pembayaran sedang diunduh ke perangkat Anda.',
                                                            type: 'success',
                                                            timer: 2000,
                                                            showConfirmButton: false
                                                        }).then(() => {
                                                            window.location.href = "<?=base_url('StudentArea/print_tagihan/')?>" + val.ids;
                                                            setTimeout(() => { location.reload(); }, 1000);
                                                        });
                                                    } else {
                                                        location.reload();
                                                    }
                                                });
                                            } else {
                                                Swal({ title: 'Menunggu', text: val.message, type: 'info' })
                                                .then(() => { location.reload(); });
                                            }
                                        },
                                        error: function(){
                                            location.reload();
                                        }
                                    });
                                },
                                onPending: function(result){
                                    Swal({ title: 'Menunggu', text: 'Silakan selesaikan pembayaran.', type: 'info' })
                                    .then(() => { location.reload(); });
                                },
                                onError: function(result){
                                    Swal({ title: 'Error', text: 'Pembayaran Gagal!', type: 'error' });
                                }
                            });
                        }
                    },
                    error: function(){
                        btn.html('<i class="fa fa-shopping-cart"></i> Bayar Sekarang').attr('disabled', false);
                        Swal({ title: 'Error', text: 'Terjadi kesalahan sistem.', type: 'error' });
                    }
                });
            }
        });
    });

    // Existing single pay logic using Midtrans and verify redirect
    $(document).on('click', '.pay-button', function(e){
        e.preventDefault();
        var btn = $(this);
        var item = [{
            jenis: btn.data('jenis'),
            label_bayar: btn.data('label_bayar'),
            nominal: btn.data('nominal'),
            tagihan_id: btn.data('tagihan_id')
        }];

        if (typeof snap === 'undefined') {
            Swal({ title: 'Error', text: 'Midtrans library tidak termuat. Periksa koneksi internet Anda.', type: 'error' });
            return;
        }

        Swal({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin ingin membayar tagihan ini?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Bayar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.value) {
                btn.html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('StudentArea/get_token_bulk') ?>',
                    type: 'POST',
                    data: { items: JSON.stringify(item) },
                    dataType: 'json',
                    success: function(response){
                        btn.html('<i class="fa fa-money"></i>').attr('disabled', false);
                        if(response.error){
                            Swal({ title: 'Gagal', text: response.error, type: 'error' });
                            return;
                        }
                        if(response.token){
                            snap.pay(response.token, {
                                onSuccess: function(result){
                                    $.ajax({
                                        url: '<?= base_url('StudentArea/finish_payment') ?>',
                                        type: 'POST',
                                        data: { order_id: result.order_id },
                                        dataType: 'json',
                                        success: function(val){
                                            if(val.status == 'success') {
                                                Swal({
                                                    title: 'Lunas!',
                                                    text: 'Pembayaran berhasil dikonfirmasi',
                                                    type: 'success',
                                                    showCancelButton: true,
                                                    confirmButtonText: '<i class="fa fa-print"></i> Cetak Bukti',
                                                    cancelButtonText: 'Tutup',
                                                    confirmButtonColor: '#28a745'
                                                }).then((res) => {
                                                    if(res.value) {
                                                        Swal({
                                                            title: 'Berhasil!',
                                                            text: 'Bukti pembayaran sedang diunduh ke perangkat Anda.',
                                                            type: 'success',
                                                            timer: 2000,
                                                            showConfirmButton: false
                                                        }).then(() => {
                                                            window.location.href = "<?=base_url('StudentArea/print_tagihan/')?>" + val.ids;
                                                            setTimeout(() => { location.reload(); }, 1000);
                                                        });
                                                    } else {
                                                        location.reload();
                                                    }
                                                });
                                            } else {
                                                Swal({ title: 'Menunggu', text: val.message, type: 'info' })
                                                .then(() => { location.reload(); });
                                            }
                                        },
                                        error: function(){
                                            location.reload();
                                        }
                                    });
                                },
                                onPending: function(result){
                                    Swal({ title: 'Menunggu', text: 'Silakan selesaikan pembayaran.', type: 'info' })
                                    .then(() => { location.reload(); });
                                },
                                onError: function(result){
                                    Swal({ title: 'Error', text: 'Pembayaran Gagal!', type: 'error' });
                                }
                            });
                        }
                    },
                    error: function(){
                        btn.html('<i class="fa fa-money"></i>').attr('disabled', false);
                        Swal({ title: 'Error', text: 'Terjadi kesalahan sistem.', type: 'error' });
                    }
                });
            }
        });
    });

    // Print Receipt confirmation
    $(document).on('click', '.btn-print-tagihan', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        Swal({
            title: 'Cetak Bukti',
            text: 'Apakah Anda yakin ingin mencetak bukti pembayaran ini?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.value) {
                Swal({
                    title: 'Berhasil!',
                    text: 'Bukti pembayaran sedang diunduh ke perangkat Anda.',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = url;
                });
            }
        });
    });
</script>
