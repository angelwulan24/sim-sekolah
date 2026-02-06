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
    $aksi = ($key->status == 'Belum Lunas') ? '<button type="button" class="btn btn-success btn-xs" onclick="BayarBulan(\''.htmlspecialchars($key->bulan).'\')"><i class="fa fa-money"></i> Bayar</button>' : '-';
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

// Handle tahun filter change
$('#tahun-filter').on('change', function(){
	var tahun = $(this).val();
	var id_siswa = '<?=$id_siswa?>';
	window.location.href = "<?=base_url('SPP/Detail/')?>"+id_siswa+"?tahun="+tahun;
});

function BayarBulan(bulan) {
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

$('#form-bayar').validate({
	errorElement: 'div',
	errorClass: 'help-block',
	focusInvalid: false,
	ignore: "",
	highlight: function (e) {
		$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
	},
	success: function (e) {
		$(e).closest('.form-group').removeClass('has-error');
		$(e).remove();
	},
	errorPlacement: function (error, element) {
		if(element.is('input[type=radio]')) {
			var controls = element.closest('div[class*="ra"]');
			if(controls.find(':radio').length > 0) controls.append(error);
			else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
		}
		else if(element.is('.select2')) {
			error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
		}
		else error.insertAfter(element.parent());
	},
	submitHandler: function (form) {
		$(form).find('button[type=submit]').text('Membayar...').attr('disabled',true);
		var isi = $('#form-bayar').serialize();
		$.ajax({
			url: '<?=base_url("SPP/Simpan")?>',
			type:"POST",
			data: isi,
			dataType:"JSON",
			success:function(data){
				$('#modal-bayar').modal('hide');
				location.reload();
			},
			error:function(){
				alert('Terjadi kesalahan');
				$(form).find('button[type=submit]').text('Bayar').attr('disabled',false);
			}
		});
	}
});
</script></div>