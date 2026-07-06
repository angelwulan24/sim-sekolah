<style>
    .box-gaji { border-top: 3px solid #A5D6A7 !important; }
    .bg-pastel-gaji { background-color: #A5D6A7 !important; color: #2D4A3E !important; }
    .table-gaji > thead > tr > th { 
        background-color: #A5D6A7 !important; 
        color: #2D4A3E !important; 
        border: 1px solid #94c396 !important;
    }
</style>

<div class="row">
	<div class="col-md-3">
		<div class="box box-primary">
            <div class="box-body box-profile">
            	<?php if(!empty($guru->foto_guru)) { ?>
              		<img class="profile-user-img img-responsive img-circle" src="<?=base_url('assets/images/guru/'.$guru->foto_guru)?>" alt="User profile picture" style="height: 100px; width: 100px; object-fit: cover;">
              	<?php } else { ?>
					<img class="profile-user-img img-responsive img-circle" src="<?=base_url('assets/images/no-image.png')?>" alt="User profile picture" style="height: 100px; width: 100px; object-fit: cover;">
				<?php } ?>
              		<h3 class="profile-username text-center"><?=$guru->nama_guru?></h3>
              		<p class="text-muted text-center">NUPTK: <?=$guru->NUPTK?></p>

              		<ul class="list-group list-group-unbordered">
		                <li class="list-group-item">
		                  <b>Jenis Kelamin</b> <a class="pull-right"><?=$guru->jk_guru?></a>
		                </li>
		                <li class="list-group-item">
		                  <b>No Telpon</b> <a class="pull-right"><?=$guru->telp_guru?></a>
		                </li>
		                <li class="list-group-item">
		                  <b>Bidang Studi</b> <a class="pull-right"><?=$guru->bidang_studi?></a>
		                </li>
		                <li class="list-group-item">
		                  <b>Status Guru</b> <a class="pull-right">
		                  	<?php 
		                  		if($guru->status_guru == 'Berhenti') echo '<span class="label label-danger">Berhenti</span>';
		                  		else if($guru->status_guru == 'Cuti') echo '<span class="label label-warning">Cuti</span>';
		                  		else echo '<span class="label label-success">Aktif</span>';
		                  	?>
		                  </a>
		                </li>
              		</ul>
            </div>
            <!-- /.box-body -->
      	</div>
	</div>

	<div class="col-md-9">
		<div class="box box-gaji">
	        <div class="box-header">
	            <h3 class="box-title"><i class="fa fa-info-circle"></i> Detail Riwayat Gaji</h3>
	        </div>
		    <div class="box-body">
		    	<div class="table-responsive">    	
			        <table  class="table table-gaji table-bordered table-hover">
			            <thead>
				            <tr>
	                            <th style="width: 10px;">No</th>
	                            <th>Tanggal Penerimaan</th>
	                            <th>Periode</th>
	                            <th>Jumlah Jam</th>
	                            <th>Total Gaji</th>
				            </tr>
			            </thead>
			            <tbody>
	                    <?php 
	                    $no=1;
	                    foreach ($isi as $v) { ?>
	                        <tr>
	                            <td><?=$no++;?></td>  
	                            <td><?=tanggal($v->tgl_gaji,'bln').' - '.jam($v->tgl_gaji).' WIB'?></td>  
	                            <td><?=$v->periode?></td>  
	                            <td><?=$v->jam.' Jam'?></td>
	                            <td><?=rupiah($v->jam*$v->nominal)?></td>    
	                        </tr>
	                    <?php } ?>
			              
			            </tbody>
			        </table>
		       	</div>
		    </div>
	    </div>
	</div>
</div>