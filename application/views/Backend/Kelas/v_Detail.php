<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Data <?php echo $judul;?></h3>
        </div>
	    <div class="box-body">
	    	<div class="table-responsive"> 
		        <table id="list-data" class="tabel table table-bordered table-hover">
		            <thead>
			            <tr>
                      <th style="width: 10px;">No</th>
                      <th>Nama Siswa</th>
                      <!-- <th>Jumlah Siswa</th> -->
                      <th>NIS</th>
                      <th>Jenis Kelamin</th>
                      <th>Tempat Lahir</th>
                      <th>Tanggal Lahir</th>
                      <th>Orangtua / Wali</th>
                      <th>No. Telpon</th>
                      <th>Alamat</th>
                      <th>Status</th>
			            </tr>
		            </thead>
		            <tbody>
		            	<?php $no = 1; foreach ($siswa as $key) {?>
		            	<tr>
		            		<td><?=$no++;?></td>
		            		<td><?=$key->nama_siswa?></td>
		            		<td><?=$key->nis_siswa?></td>
		            		<td><?=$key->jk_siswa?></td>
                            <td><?=$key->tempat_lahirsiswa?></td>
                            <td><?=date('d-m-Y', strtotime($key->tgl_lahirsiswa))?></td>
                            <td><?=$key->ortu_wali?></td>
                            <td><?=$key->telp_siswa?></td>
                            <td><?=$key->alamat_ssiwa?></td>
                            <td><?=$key->status_siswa?></td>
		            	</tr>
		             <?php } ?>
		            </tbody>
		        </table>
	       	</div>
	    </div>
    </div>
</div>