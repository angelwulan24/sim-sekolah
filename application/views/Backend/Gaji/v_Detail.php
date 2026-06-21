<style>
    .box-gaji { border-top: 3px solid #A5D6A7 !important; }
    .bg-pastel-gaji { background-color: #A5D6A7 !important; color: #2D4A3E !important; }
    .table-gaji > thead > tr > th { 
        background-color: #A5D6A7 !important; 
        color: #2D4A3E !important; 
        border: 1px solid #94c396 !important;
    }
</style>

<div class="col-xs-12">
	<div class="box box-gaji">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Detail Riwayat Gaji</h3>
            <h4 class="pull-right" style="margin-top: 0;">Nama Guru : <strong><?=get_guru($id)?></strong></h4>
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
                            <td><?=tanggal($v->tanggal,'bln').' - '.jam($v->tanggal).' WIB'?></td>  
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