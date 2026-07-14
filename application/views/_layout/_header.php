<?php 
$masuk = $this->db->query("SELECT id_users AS id, email, password, nama_users AS name, role, gambar FROM users WHERE id_users = '".$this->session->userdata('id')."'")->row_array();
$foto_profil = base_url('assets/dist/img/') . (!empty($masuk['gambar']) ? $masuk['gambar'] : 'user.png');

if ($masuk['role'] == 3) {
    $siswa = $this->db->query("SELECT foto_siswa AS foto FROM siswa WHERE nis = '".$masuk['email']."'")->row_array();
    if ($siswa && !empty($siswa['foto'])) {
        $foto_profil = base_url('assets/images/siswa/') . $siswa['foto'];
    }
}
?>
<a href="<?=base_url('Beranda')?>" class="logo" style="display: flex; align-items: center; justify-content: center; height: 50px; overflow: hidden;">
    <span class="logo-mini"><b>MI</b></span>
    <span class="logo-lg" style="font-size: 14px; font-weight: 700; letter-spacing: 0px; line-height: 1.2;">MI DAAR EL-MUFLIHIN</span>
</a>
<nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    </a>
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?php echo $foto_profil; ?>" class="user-image" alt="User Image" style="object-fit:cover;">
                    <span class="hidden-xs"> <?php echo $masuk['name']?></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <img src="<?php echo $foto_profil; ?>" class="img-circle" alt="User Image" style="object-fit:cover; width:90px; height:90px;">
                        <p> <?php echo $masuk['name']?></p>
                    </li>
                    <li class="user-footer">
                        <?php if ($masuk['role'] != 3): ?>
                        <div class="pull-left">
                          <!--   <a href="#" onclick="Password()" class="btn btn-default btn-flat">Password</a> -->
                            <a href="#" onclick="UbahFoto()" class="btn btn-default btn-flat">Ubah Foto</a>
                        </div>
                        <?php endif; ?>
                        <div class="pull-right" style="<?= ($masuk['role'] == 3) ? 'width: 100%; text-align: center;' : '' ?>">
                            <a href="<?php echo base_url('Auth/logout')?>" class="btn btn-default btn-flat btn-logout-confirm" style="<?= ($masuk['role'] == 3) ? 'width: 100%;' : '' ?>">Logout</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<?php if($this->session->flashdata('message')): ?>
    <div style="padding: 15px 15px 0 15px;">
        <?= $this->session->flashdata('message') ?>
    </div>
<?php endif; ?>

<div class="modal fade" id="modal-foto">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Ubah Foto Profil</h4>
            </div>
            <?= form_open_multipart('Auth/update_foto') ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Foto Baru</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

 <!--   
<div class="modal fade"  id="modal-password">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
<?= form_open('','role = "form" id = "form"')?>
            <div class="modal-body">
                <input type="hidden" name="id" value="">
                <div class="form-group">
                    <label class="control-label"> Password Lama</label>
                    <div><input type="password" required="" placeholder="Password Lama" autocomplete="off" name="lama" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Password Baru</label>
                    <div><input type="password" id="password" required="" placeholder="Password Baru" autocomplete="off" name="baru" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Konfirmasi Password</label>
                    <div><input type="password" required="" placeholder="Konfirmasi Password" autocomplete="off" name="ulangi" class="form-control"></div>
                </div>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="simpan"  class="btn btn-primary">Ganti</button>
            </div>
<?= form_close()?>
        </div>
    </div>
</div>
 -->


<script type="text/javascript">

    $(document).ready(function(){

        $('#for').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            rules:{
                baru:{
                    minlength:6
                },
                ulangi:{
                    minlength:6,
                    equalTo:"#password"
                }
            },
            messages:{
                baru:{
                    required:"Password baru tidak boleh kosong",
                    minlength:"Minimal 6 Karakter"
                },
                ulangi:{
                    required: "Password Konfirmasi tidak boleh kosong",
                    minlength: "Minimal 6 Karakter",
                    equalTo: "Konfirmasi Password tidak cocok"
                },
                lama: "Password lama tidak boleh kosong"
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
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
                $('#simpan').text('Menyimpan...');
                $('#simpan').attr('disabled',true);
                var url,method;
                if (label == 'simpan'){
                    url = '<?=base_url('Auth/Simpan')?>';
                    method = 'Ubah';
                }
                var isi = $('#form').serialize();
                $.ajax({
                    url: url,
                    type:"POST",
                    data: isi,
                    dataType:"JSON",
                    success:function(data){
                        $('#modal-password').modal('hide');
                        $('#simpan').text('Simpan');
                        $('#simpan').attr('disabled',false);
                        if(data.status)
                            sweet('Sukses','Berhasil '+method+' Password','success');
                        else
                            sweet('Gagal ','Gagal '+method+' Password','error');
                    }
                });
            },
            invalidHandler: function (form) {}
        });


    });

        function sweet(judul,text,tipe){
        Swal({
            title: judul,
            text: text,
            type: tipe
        });
    }
    
    function Password(){

        label = 'simpan';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty(); 
        $('#modal-password').appendTo("body").modal('show');
        $('.modal-title').text('Ganti Password');
    }

    function UbahFoto(){
        $('#modal-foto').appendTo("body").modal('show');
    }

</script>
