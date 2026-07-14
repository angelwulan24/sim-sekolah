<div style="margin-bottom: 10px;" class="col-xs-12">
  <div class="pull-right">
       <a href="<?=base_url('Laporan/Cetak_detail/'.$this->uri->segment(3))?>" class="btn btn-info btn-print-laporan"><i class="fa fa-print"></i> Cetak</a>  
  </div>
</div>

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="box-title">Laporan Pemasukan Sekolah</h3>
                 </div>
                 <div class="col-md-6 text-right">
                    <h4 class="text-primary"><b>Tanggal Laporan: <?= tgl_indo($isi['tanggal']) ?></b></h4>
                 </div>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-striped">
<?php $katot = 0;

if (!empty($isi['spp'])) { ?>
                <thead><tr> <th colspan="4" style="background-color: #ffff00;">SPP</th></tr></thead>
                <tbody>
                    <tr>
                      <th>Tanggal</th>
                      <th>Nama Siswa</th>
                      <th>Bulan</th>
                      <th colspan="2">Nominal</th>
                    </tr>
            <?php 
            $total = 0;
            foreach ($isi['spp'] as $k) {?>
                    <tr>
                      <td><?= tgl_indo($isi['tanggal']) ?></td>
                      <td><?=$k->name?></td>
                      <td><?=$k->bulan?></td>
                      <td style="width: 10px;">Rp.</td>
                      <td style="text-align: right;"><?=number_format($k->nominal,0,',','.')?></td>
                    </tr>
            <?php 
            $total+=$k->nominal;
             }$katot+=$total; ?>
                    <tr>
                      <th colspan="3">Sub Total</th>
                      <th>Rp.</th>
                      <th style="text-align: right;"><?=number_format($total,0,',','.')?></th>
                    </tr>
                </tbody>
<?php } ?>

<?php if (!empty($isi['tagihan_lainnya'])) { ?>
                <thead><tr> <th colspan="4" style="size: 16px; background-color: #ffff00;">Tagihan Lainnya</th></tr></thead>
                <tbody>
                    <tr>
                      <th>Tanggal</th>
                      <th>Keterangan</th>
                      <th colspan="2">Nominal</th>
                    </tr>
                     <?php 
			$total = 0;
			foreach ($isi['tagihan_lainnya'] as $k) { ?>
                    <tr>
                      <td><?= tgl_indo($isi['tanggal']) ?></td>
                      <td><?=$k->keterangan?></td>
                      <td style="width: 10px;">Rp.</td>
                      <td style="text-align: right;"><?=number_format($k->nominal,0,',','.')?></td>
                    </tr>
            <?php
            $total+=$k->nominal;
             }$katot+=$total; ?>
                    <tr>
                      <th colspan="2">Total</th>
                      <th>Rp.</th>
                      <th style="text-align: right;"><?=number_format($total,0,',','.')?></th>
                    </tr>
                </tbody>
<?php } ?>

<?php if (!empty($isi['pemasukan'])) { ?>
                <thead><tr> <th colspan="4" style="size: 16px; background-color: #ffff00;">Pemasukan Lainnya</th></tr></thead>
                <tbody>
                    <tr>
                      <th>Tanggal</th>
                      <th>Keterangan</th>
                      <th colspan="2">Nominal</th>
                    </tr>
                     <?php 
			$total = 0;
			foreach ($isi['pemasukan'] as $k) { ?>
                    <tr>
                      <td><?= tgl_indo($isi['tanggal']) ?></td>
                      <td><?=$k->keterangan?></td>
                      <td style="width: 10px;">Rp.</td>
                      <td style="text-align: right;"><?=number_format($k->nominal,0,',','.')?></td>
                    </tr>
            <?php
            $total+=$k->nominal;
             }$katot+=$total; ?>
                    <tr>
                      <th colspan="2">Total</th>
                      <th>Rp.</th>
                      <th style="text-align: right;"><?=number_format($total,0,',','.')?></th>
                    </tr>
                </tbody>
<?php } ?>
                  <tr style="size: 18; background-color: #ffff00;">
                    <th colspan="3"> <i>Total Pemasukan hari ini</i> </th>
                    <th>Rp.</th>
                    <th style="text-align: right;"><?=number_format($katot,0,',','.')?></th>
                  </tr>
            </table>
        </div>
    </div>
</div>
<div class="col-xs-12">
	<div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="box-title">Laporan Pengeluaran Sekolah</h3>
                </div>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-striped">
           	<?php $katot = 0;
if (!empty($isi['gaji'])) {
 ?>
            	<thead><tr><th colspan="4" style="background-color: #00ccff;">GAJI GURU</th></tr></thead>
                <tbody>
                    <tr>
                      <th>Tanggal</th>
                      <th>Nama Guru</th>
                      <th>Bulan</th>
                      <th colspan="2">Nominal</th>
                   	</tr>
               <?php 
			$total = 0;
			foreach ($isi['gaji'] as $k) { ?>
                    <tr>
                      <td><?= tgl_indo($isi['tanggal']) ?></td>
                      <td><?=$k->name?></td>
                      <td><?=$k->periode?></td>
                      <td style="width: 10px;">Rp.</td>
                      <td style="text-align: right;"><?=number_format($k->gaji,0,',','.')?></td>
                    </tr>
             <?php
            $total+=$k->gaji;
             }$katot+=$total; ?>

                    <tr>
                      <th colspan="3">Total</th>
                      <th>Rp.</th>
                      <th style="text-align: right;"><?=number_format($total,0,',','.')?></th>
                    </tr>
                  </tbody>
             <?php } 
if (!empty($isi['pengeluaran'])) {
             ?>
				<thead><tr><th colspan="4" style="size: 16px; background-color: #00ccff;">Pengeluaran Lainnya</th></tr></thead>
                <tbody>
                    <tr>
                      <th>Tanggal</th>
                      <th>Keterangan</th>
                      <th colspan="2">Nominal</th>
                    </tr>
                   <?php 
		$total = 0;
		foreach ($isi['pengeluaran'] as $k) { ?>
                    <tr>
                      <td><?= tgl_indo($isi['tanggal']) ?></td>
                      <td><?=$k->keterangan?></td>
                      <td style="width: 10px;">Rp.</td>
                      <td style="text-align: right;"><?=number_format($k->nominal,0,',','.')?></td>
                    </tr>
         <?php
            $total+=$k->nominal;
             }$katot+=$total; ?>
                  </tbody>
                  <tr style="size: 18; background-color: #00ccff;">
                    <th colspan="3"> <i>Total Pengeluaran hari ini</i> </th>
                    <th>Rp.</th>
                    <th style="text-align: right;"><?=number_format($katot,0,',','.')?></th>
                  </tr>
       <?php } ?>
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', '.btn-print-laporan', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        Swal({
            title: 'Cetak Laporan',
            text: 'Apakah Anda yakin ingin mencetak laporan detail ini?',
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
                    text: 'Laporan detail sedang diunduh ke perangkat Anda.',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = url;
                });
            }
        });
    });
});
</script>