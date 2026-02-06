<?php 

$id = isset($id_siswa) ? $id_siswa : $this->uri->segment(3);

$na = $this->db->query("SELECT name FROM siswa WHERE id = '$id'")->row_array();

?>

<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
            <input type="hidden" id="id" value="<?=$id?>">
           <h3> Nama Siswa : <?=$na['name']?> </h3> 
        </div>
        <div class="box-header" style="border-top: 1px solid #ddd;">
            <?php if(isset($tahun_list) && isset($selected_tahun)): ?>
            <div class="form-group" style="margin: 0;">
                <label class="control-label" style="margin-bottom: 10px;">Tahun Ajaran</label>
                <select id="tahun-filter" class="form-control" style="width: 300px;">
                    <?php foreach($tahun_list as $t): ?>
                        <?php $label = ($t == $tahun_sekarang) ? "Tahun Ajaran " . $t . "/" . ($t+1) . " (Sekarang)" : "Tahun Ajaran " . $t . "/" . ($t+1); ?>
                        <option value="<?=$t?>" <?=$t == $selected_tahun ? 'selected' : ''?>><?=$label?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="box-body">
            <div class="table-responsive">      
                <table  class="table tabel table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">No</th>
                            <th>Periode Ujian</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
$no=1;
foreach ($isi as $key ) { 
    $status = $key->status;
    $tanggal_bayar = $key->time ? tanggal($key->time, 'bulan') : '-';
?>
                        <tr>
                            <td><?=$no++;?></td>
                            <td><?=$key->periode . ' ' . $selected_tahun?></td>
                            <td><?=rupiah($key->nominal)?></td>
                            <td><span class="label <?=$status == 'Lunas' ? 'label-success' : 'label-danger'?>"><?=$status?></span></td>
                            <td><?=$tanggal_bayar?></td>
                            <td style="text-align: center;">
                                <?php if($status == 'Belum Lunas' && $selected_tahun == $tahun_sekarang): ?>
                                <button onclick="BayarBulan('<?=$key->periode?>', '<?=$key->periode_label?>', <?=$key->nominal?>)" class="btn btn-success btn-sm">Bayar</button>
                                <?php elseif($status == 'Lunas'): ?>
                                <span class="text-muted">-</span>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
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
                <h4 class="modal-title">Pembayaran Ujian</h4>
            </div>
            <div class="modal-body">
                <p>Periode: <strong id="periode-bayar"></strong></p>
                <p>Nominal: <strong id="nominal-bayar"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="ProsesBayar()">Bayar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var siswa = $('#id').val();
    var periode_bayar_val = '';
    
    $(document).ready(function(){
        // Year filter handler
        $('#tahun-filter').on('change', function(){
            var tahun = $(this).val();
            var id = $('#id').val();
            window.location.href = "<?=base_url('Ujian/Detail/')?>" + id + "?tahun=" + tahun;
        });
    });
    
    function BayarBulan(periode, periode_label, nominal){
        periode_bayar_val = periode_label;
        $('#periode-bayar').text(periode + ' ' + <?=$selected_tahun?>);
        $('#nominal-bayar').text('Rp ' + nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        $('#modal-bayar').modal('show');
    }
    
    function ProsesBayar(){
        // Process payment
        var id_siswa = $('#id').val();
        $.ajax({
            url: '<?=base_url("Ujian/Simpan")?>',
            type: 'POST',
            data: {
                id_siswa: id_siswa,
                bulan: periode_bayar_val,
                harga: 0
            },
            dataType: 'json',
            success: function(data) {
                $('#modal-bayar').modal('hide');
                if(data.status) {
                    Swal({
                        title: 'Sukses',
                        text: 'Pembayaran Ujian Berhasil',
                        type: 'success'
                    });
                    location.reload();
                } else {
                    Swal({
                        title: 'Gagal',
                        text: 'Pembayaran sudah dilakukan sebelumnya',
                        type: 'error'
                    });
                }
            }
        });
    }
</script>