<?php 

$id = $this->uri->segment(3);

$na = $this->db->query("SELECT name FROM siswa WHERE id = '$id'")->row_array();

?>

<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
           <h3> Nama Siswa : <?=$na['name']?> </h3>
        </div>
        <div class="box-body">
            <div style="margin-bottom: 15px;">
               <label>Pilih Tahun Ajaran:</label>
               <select id="tahun-filter" class="form-control" style="width: 250px;">
                   <?php 
                   // Generate tahun ajaran options berdasarkan kelas siswa
                   for($i = 0; $i <= $max_history; $i++) {
                       $tahun = $tahun_sekarang - $i;
                       $tahun_berikutnya = $tahun + 1;
                       $label = ($i == 0) ? 'Tahun Ajaran ' . $tahun . '/' . $tahun_berikutnya . ' (Sekarang)' : 'Tahun Ajaran ' . $tahun . '/' . $tahun_berikutnya;
                       $selected = ($tahun == $selected_tahun) ? 'selected' : '';
                       echo '<option value="'.$tahun.'" '.$selected.'>'.$label.'</option>';
                   }
                   ?>
               </select>
            </div>
            <div class="table-responsive">      
                <table  class="table tabel table-bordered table-hover">
                    <thead>
                        <tr>
                      <th style="width: 10px;">No</th>
                      <th>Bulan</th>
                      <th>Jumlah</th>
                      <th>Status</th>
                      <th>Tanggal Bayar</th>
                      <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
$no=1;
foreach ($isi as $key ) { 
    $status_label = ($key->status == 'Lunas') ? '<span class="label label-success">Lunas</span>' : '<span class="label label-danger">Belum Lunas</span>';
    $aksi = ($key->status == 'Belum Lunas') ? 
        '<button type="button" class="btn btn-success btn-xs" onclick="BayarBulan(\''.htmlspecialchars($key->bulan).'\')"><i class="fa fa-money"></i> Bayar</button>' : 
        '<a href="'.base_url('SPP/CetakBukti/'.$key->id).'" target="_blank" class="btn btn-abu-tua btn-xs"><i class="fa fa-print"></i> Cetak</a>';
    $tanggal_bayar = ($key->status == 'Lunas' && $key->time) ? date('d-m-Y', strtotime($key->time)) : '-';
?>
                        <tr>
                            <td><?=$no++;?></td>
                            <td><?=$key->bulan?></td>
                            <td><?=rupiah($key->nominal)?></td>
                            <td><?=$status_label?></td>
                            <td><?=$tanggal_bayar?></td>
                            <td><?=$aksi?></td>
                        </tr>
<?php  } ?>
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="modal-bayar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pembayaran SPP</h4>
            </div>
<?= form_open('SPP/Simpan','role = "form" id = "form-bayar"')?>
            <div class="modal-body">
            	<input type="hidden" name="id" value="<?=$id?>">
                <div class="form-group">
                    <label class="control-label">Bulan Pembayaran</label>
                    <input type="text" name="bulan" id="bulan-input" readonly="" class="form-control">
                </div>

                <div class="form-group">
                    <label class="control-label"> Nominal</label>
                    <div><input type="text" value="" readonly="" name="harga" class="form-control"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Bayar</button>
            </div>
<?= form_close()?>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

	// Handle tahun filter change
	$('#tahun-filter').on('change', function(){
		var tahun = $(this).val();
		var id_siswa = '<?=$id_siswa?>';
		window.location.href = "<?=base_url('SPP/Detail/')?>"+id_siswa+"?tahun="+tahun;
	});

	window.BayarBulan = function(bulan) {
		$('#bulan-input').val(bulan);
		$('#form-bayar [name="bulan"]').val(bulan);
		
		// Get nominal SPP
		$.ajax({
			url: "<?=base_url($this->uri->segment(1).'/GetSPP/')?>",
			type:"GET",
			dataType:"JSON",
			success:function(data){
				$('[name="harga"]').val(data);
				$('#modal-bayar').modal('show');
			}
		});
	}

	$('#form-bayar').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.find('button[type=submit]').text('Membayar...').attr('disabled',true);
		
		var isi = form.serialize();
		$.ajax({
			url: '<?=base_url("SPP/Simpan")?>',
			type: "POST",
			data: isi,
			dataType: "JSON",
			success: function(data){
				$('#modal-bayar').modal('hide');
				if (data.status) {
					Swal({
						title: 'Sukses',
						text: 'Pembayaran SPP Berhasil. Cetak Bukti Pembayaran?',
						type: 'success',
						showCancelButton: true,
						confirmButtonText: 'Cetak Bukti',
						cancelButtonText: 'Tutup'
					}).then((result) => {
						if (result.value) {
							// Open print page in new tab
							window.open('<?=base_url("SPP/CetakBukti/")?>' + data.id_pembayaran, '_blank');
						}
						// Reload page to update data
						location.reload();
					});
				} else {
					Swal({
						title: 'Gagal',
						text: 'Pembayaran Gagal',
						type: 'error'
					});
					form.find('button[type=submit]').text('Bayar').attr('disabled',false);
				}
			},
			error: function(){
				Swal({
					title: 'Error',
					text: 'Terjadi kesalahan sistem',
					type: 'error'
				});
				form.find('button[type=submit]').text('Bayar').attr('disabled',false);
			}
		});
	});

});
</script></div>