<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="box box-primary">
            <div class="box-body box-profile">
                <!-- <img class="profile-user-img img-responsive img-circle" src="<?= base_url(); ?>assets/dist/img/user.png" alt="User profile picture"> -->
                <div class="text-center">
                    <i class="fa fa-user-circle-o fa-5x text-primary" aria-hidden="true"></i>
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
                <h3 class="box-title">Tagihan Belum Dibayar</h3>
            </div>
            <div class="box-body">
                <p>Silahkan pilih bulan untuk pembayaran SPP.</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Simple Logic to generate months for the current year
                        $months = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        $year = date('Y');
                        
                        // Get paid months
                        $paid_months = [];
                        foreach($spp as $p){
                            // format stored in DB: "Januari-2020" or similar based on SPP.php
                            $paid_months[] = $p->bulan;
                        }
                        
                        foreach($months as $m){
                            $label = $m . '-' . $year;
                            if(!in_array($label, $paid_months)){
                                // Get nominal (Hardcoded 70000 based on DB or query)
                                $nominal = 70000;
                        ?>
                        <tr>
                            <td><?= $label ?></td>
                            <td>Rp <?= number_format($nominal, 0, ',', '.') ?></td>
                            <td>
                                <button class="btn btn-success btn-sm pay-button" data-bulan="<?= $label ?>" data-nominal="<?= $nominal ?>">
                                    <i class="fa fa-credit-card"></i> Bayar
                                </button>
                            </td>
                        </tr>
                        <?php 
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Riwayat Pembayaran</h3>
            </div>
            <div class="box-body">
                 <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <th>Bulan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($spp as $row): ?>
                        <tr>
                            <td><?= $row->tanggal ?></td>
                            <td><?= $row->bulan ?></td>
                            <td>Rp <?= number_format($row->nominal, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="YOUR_CLIENT_KEY"></script>
<script>
    $('.pay-button').click(function(){
        var bulan = $(this).data('bulan');
        var nominal = $(this).data('nominal');
        alert('Fitur pembayaran untuk ' + bulan + ' akan diimplementasikan segera (Midtrans Integration).');
        // Here you would call an endpoint to get Snap Token
    });
</script>
