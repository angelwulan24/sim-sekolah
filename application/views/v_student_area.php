<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="box box-primary">
            <div class="box-body box-profile">
                <div class="text-center">
                    <?php if(!empty($student->foto)): ?>
                        <img class="profile-user-img img-responsive img-circle" src="<?= base_url('assets/images/siswa/' . $student->foto); ?>" alt="User profile picture" style="width:100px; height:100px; object-fit:cover;">
                    <?php else: ?>
                        <i class="fa fa-user-circle-o fa-5x text-primary" aria-hidden="true"></i>
                    <?php endif; ?>
                </div>
                <h3 class="profile-username text-center"><?= $student->name; ?></h3>
                <p class="text-muted text-center"><?= $student->nis; ?></p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Kelas</b> <a class="pull-right"><?= $student->kelas; ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="pull-right"><?= $student->status; ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title" style="margin-top: 5px;">Tagihan Bulanan (SPP)</h3>
                <div class="box-tools pull-right" style="margin-right: 15px;">
                    <form action="<?= base_url('StudentArea') ?>" method="GET" class="form-inline">
                        <div class="form-group">
                            <label style="font-weight: normal; margin-right: 5px;">Tahun Ajaran: </label>
                            <select name="tahun" class="form-control input-sm" onchange="this.form.submit()">
                                <?php foreach($tahun_list as $t): ?>
                                    <option value="<?= $t ?>" <?= ($selected_tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Tempat Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tagihan_bulanan as $tb): ?>
                        <tr>
                            <td><?= $tb->label_bayar ?></td>
                            <td>Rp <?= number_format($tb->nominal, 0, ',', '.') ?></td>
                            <td>
                                <?php if($tb->status == 'Lunas'): ?>
                                    <span class="label label-success">Lunas</span>
                                <?php else: ?>
                                    <span class="label label-danger">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $tb->tanggal_bayar ?></td>
                            <td><?= $tb->tempat_bayar ?></td>
                            <td>
                                <?php if($tb->status == 'Lunas'): ?>
                                    <button class="btn btn-default btn-xs" disabled><i class="fa fa-check"></i> Sudah Bayar</button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-xs pay-button" data-jenis="<?= $tb->jenis ?>" data-label_bayar="<?= $tb->label_bayar ?>" data-nominal="<?= $tb->nominal ?>">
                                        <i class="fa fa-credit-card"></i> Bayar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Tagihan Lainnya</h3>
            </div>
            <div class="box-body">
                 <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Nama Tagihan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Tempat Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tagihan_lainnya as $tl): ?>
                        <tr>
                            <td><?= $tl->nama_tagihan ?></td>
                            <td>Rp <?= number_format($tl->nominal, 0, ',', '.') ?></td>
                            <td>
                                <?php if($tl->status == 'Lunas'): ?>
                                    <span class="label label-success">Lunas</span>
                                <?php else: ?>
                                    <span class="label label-danger">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $tl->tanggal_bayar ?></td>
                            <td><?= $tl->tempat_bayar ?></td>
                            <td>
                                <?php if($tl->status == 'Lunas'): ?>
                                    <button class="btn btn-default btn-xs" disabled><i class="fa fa-check"></i> Sudah Bayar</button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-xs pay-button" data-jenis="<?= $tl->jenis ?>" data-label_bayar="<?= $tl->label_bayar ?>" data-nominal="<?= $tl->nominal ?>">
                                        <i class="fa fa-credit-card"></i> Bayar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $midtrans_client_key ?>"></script>
<script>
    $('.pay-button').click(function(){
        var jenis = $(this).data('jenis');
        var label_bayar = $(this).data('label_bayar');
        var nominal = $(this).data('nominal');
        var button = $(this);
        
        button.text('Loading...').attr('disabled', true);
        
        $.ajax({
            url: '<?= base_url() ?>StudentArea/get_token',
            type: 'POST',
            data: {
                jenis: jenis,
                label_bayar: label_bayar,
                nominal: nominal
            },
            dataType: 'json',
            success: function(response){
                button.text('Bayar').attr('disabled', false);
                
                if(response.error){
                    alert(response.error);
                    return;
                }
                
                if(response.token){
                    snap.pay(response.token, {
                        onSuccess: function(result){
                            // Call finish_payment endpoint
                            $.ajax({
                                url: '<?= base_url() ?>StudentArea/finish_payment',
                                type: 'POST',
                                data: {
                                    order_id: result.order_id,
                                    type: 'manual' 
                                },
                                dataType: 'json',
                                success: function(validation){
                                    if(validation.status == 'success'){
                                        alert('Pembayaran Berhasil dan Terverifikasi!');
                                        location.reload();
                                    } else {
                                        alert('Pembayaran berhasil di Midtrans, tetapi gagal verifikasi di sistem: ' + validation.message);
                                        location.reload();
                                    }
                                },
                                error: function(){
                                    alert('Pembayaran berhasil, tetapi gagal menghubungi server untuk verifikasi.');
                                    location.reload();
                                }
                            });
                        },
                        onPending: function(result){
                            alert('Pembayaran Sedang Diproses. Silahkan selesaikan pembayaran.');
                            location.reload(); 
                        },
                        onError: function(result){
                            alert('Pembayaran Gagal!');
                        },
                        onClose: function(){
                            // alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran.');
                }
            },
            error: function(){
                button.text('Bayar').attr('disabled', false);
                alert('Terjadi kesalahan sistem.');
            }
        });
    });
</script>
