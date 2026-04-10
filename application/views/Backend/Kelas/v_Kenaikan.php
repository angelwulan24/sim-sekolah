<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $judul;?></h3>
        </div>
	    <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Fitur ini digunakan untuk menaikkan seluruh siswa dari satu kelas ke kelas lain secara otomatis.
            </div>
            <div class="alert alert-warning">
                <i class="fa fa-warning"></i> <b>PENTING:</b> Lakukan kenaikan kelas dari tingkat paling tinggi terlebih dahulu (misal: Kelas XII ke Lulus/Alumni, lalu Kelas XI ke Kelas XII, dst) untuk menghindari penumpukan data siswa di satu kelas.
            </div>
            <?=form_open($this->uri->segment(1).'/ProsesKenaikan', array('class' => 'form-horizontal'))?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Dari Kelas</label>
                    <div class="col-sm-4">
                        <select name="dari_kelas" class="form-control select2" required>
                            <option value="">-- Pilih Kelas Asal --</option>
                            <?php foreach ($kelas as $k) { ?>
                                <option value="<?=$k->id?>"><?=$k->nama?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Ke Kelas</label>
                    <div class="col-sm-4">
                        <select name="ke_kelas" class="form-control select2" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            <?php foreach ($kelas as $k) { ?>
                                <option value="<?=$k->id?>"><?=$k->nama?></option>
                            <?php } ?>
                            <option value="lulus">LULUS (Alumni)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah anda yakin ingin memproses kenaikan kelas ini? Data yang sudah diubah tidak dapat dikembalikan secara otomatis.')">Proses Kenaikan</button>
                    </div>
                </div>
            <?=form_close()?>
	    </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
