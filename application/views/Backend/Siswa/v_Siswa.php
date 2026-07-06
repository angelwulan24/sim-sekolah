<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <div class="col-sm-2">
<?php $kls= $this->db->query("SELECT id_kelas AS id, nama_kelas AS nama FROM kelas")->result() ?>
                <select name="kelas" id="kelas" data-placeholder="--Pilih kelas--" class="form-control select2">
                    <option value=""></option>
                <?php foreach ($kls as $key) {?>    
                    <option value="<?=$key->id?>"><?=$key->nama?></option>
                <?php } ?>
                </select>
            </div>

            <div class="pull-right">
                <?php if ($this->session->userdata('role') == 1) { ?>
                <div class="btn-group">
                    <a  class="btn btn-primary" onclick="Import()" ><i class="fa fa-download"></i> Import Data</a>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?=base_url('excel/form.xlsx?v=' . filemtime('excel/form.xlsx'))?>">Format Data</a></li>
                    </ul>
                </div>

            	<a href="#" onclick="Tambah()" class="btn btn-primary">Tambah Data </a>
                <?php } ?>
            </div>

        </div>
	    <div class="box-body">
	    	<div class="table-responsive">    	
		        <table id="list-data" class="table table-bordered table-hover">
		            <thead>
			            <tr>
                      <th style="width: 10px;">No</th>
                      <th>Foto</th>
                      <th>Nama Siswa</th>
                      <th>NIS</th>
                      <th>Jenis Kelamin</th>
                      <th>Agama</th>
                      <th>Tempat Lahir</th>
                      <th>Tanggal Lahir</th>
                      <th>Orangtua / Wali</th>
                      <th>No. Telpon</th>
                      <th>Alamat</th>
                      <th>Tanggal Masuk</th>
                      <th>Tahun Ajaran</th>
                      <th>Kelas</th>
                      <th>Status</th>
                      <th width="150">Aksi</th>
			            </tr>
		            </thead>
		            <tbody>
		              
		            </tbody>
		        </table>
	       	</div>
	    </div>
    </div>
</div>


<div class="modal fade" id="modal-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
<?= form_open('','role = "form" id = "form-import"')?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label"> File</label>
                    <input type="file" required="" accept=".xlsx" name="file">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="import" class="btn btn-primary">Import</button>
            </div>
<?= form_close()?>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal-form">
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
                    <label class="control-label"> Nama Lengkap</label>
                    <div><input type="text" required="" placeholder="Nama Lengkap" autocomplete="off" name="nama" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> NIS</label>
                    <div><input type="text" onkeypress="return Angka(this)" required="" placeholder="NIP/NIK" autocomplete="off" name="nis" class="form-control"></div>
                </div>
                <div class="form-group">
                    <div class="ra">
                        <label class="control-label">Jenis Kelamin</label><br>
                        <input type="radio" class="minimal" required="" name="gender" value="Laki-Laki" ><span class="lbl"> Laki-Laki</span>
                        <input type="radio" required="" name="gender" class="minimal" value="Perempuan"><span class="lbl"> Perempuan</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Agama</label>
                    <div><input type="text" value="Islam" readonly="" name="agama" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tempat Lahir</label>
                    <div><input type="text" required="" placeholder="Tempat Lahir" autocomplete="off" name="tempat" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tanggal Lahir</label>
                    <div><input type="text" required="" placeholder="Tanggal Lahir" autocomplete="off" name="tanggal" class="form-control datepicker"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Orangtua / Wali</label>
                    <div><input type="text" required="" placeholder="Orangtua / Wali" autocomplete="off" name="orangtua_wali" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> No. Telpon</label>
                    <div><input type="text" onkeypress="return Angka(this)" required="" placeholder="No. Telpon" autocomplete="off" name="telpon" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Alamat</label>
                    <div><input type="text" required="" placeholder="Alamat" autocomplete="off" name="alamat" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tanggal Masuk</label>
                    <div><input type="text" required="" placeholder="Tanggal Masuk" autocomplete="off" name="tanggal_masuk" class="form-control datepicker"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tahun Ajaran</label>
                    <div><input type="text" required="" readonly="" value="<?=current_school_year()?>" placeholder="Contoh: 2023/2024" autocomplete="off" name="tahun_ajaran" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label">Kelas</label>
                    <select name="kelas" required="" data-placeholder="--Pilih--" class="form-control select2">
                        <option value="">--Pilih--</option>
                            <?php foreach ($kls as $key) {?>    
                                <option value="<?=$key->id?>"><?=$key->nama?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Status</label>
                    <select name="status" required="" data-placeholder="--Pilih--" class="form-control">
                        <option value="">--Pilih--</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Alumni">Alumni</option>
                        <option value="Berhenti">Berhenti</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="simpan"  class="btn btn-primary">Simpan</button>
            </div>
<?= form_close()?>
        </div>
    </div>
</div>


<script type="text/javascript">

	var label;
	var table;
	$(document).ready(function(){
		$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings){
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        };

         load_data ();

    function load_data(is_kelas){

       table =  $("#list-data").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#list-data input')
                    .off('.DT')
                    .on('keyup.DT', function(e) {
                        api.search(this.value).draw();
                    });
            },
            oLanguage: {
                sSearch       :"<i class='fa fa-search fa-fw'></i> Cari: ",
                sLengthMenu   :"Tampilkan _MENU_ data",
                sInfo         :"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                sInfoFiltered :"(disaring dari _MAX_ total data)", 
                sZeroRecords  :"Oops..data kosong", 
                sEmptyTable   :"Data kosong.", 
                sInfoEmpty    :"Menampilkan 0 sampai 0 data",
                sProcessing   :"Sedang memproses...", 
                oPaginate: {
                    sPrevious :"Sebelumnya",
                    sNext     :"Selanjutnya",
                    sFirst    :"Pertama",
                    sLast     :"Terakhir"
                }
            },
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?= base_url().$this->uri->segment(1).'/getData'?>",
                "type": "POST",
                "data":{is_kelas : is_kelas }
            },
            columns: [
                {
                    "data": "nis_siswa",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "foto_siswa",
                    "render": function(data, type, row) {
                        if(data) return '<img src="<?=base_url("assets/images/siswa/")?>' + data + '" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                        return '<img src="https://via.placeholder.com/50" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                    }
                },
                {"data": "nama_siswa"},
                {"data": "nis_siswa"},
                {"data": "jk_siswa"},
                {"data": "agama_siswa"},
                {"data": "tempat_lahirsiswa"},
                {"data": "tgl_lahirsiswa"},
                {"data": "ortu_wali"},
                {"data": "telp_siswa"},
                {"data": "alamat_ssiwa"},
                {"data": "tgl_masuk"},
                {"data": "thn_ajaran"},
                {"data": "nama_kelas"},
                {
                    "data": "status_siswa",
                    "render": function(data, type, row) {
                        var text = data ? data : "Aktif";
                        var stat = "success"; // Default Hijau (Aktif)
                        
                        // Cek status dengan case-insensitive
                        var checkStatus = text.toLowerCase().trim();
                        
                        if(checkStatus === "berhenti") {
                            stat = "danger"; // Merah
                        } else if(checkStatus === "alumni" || checkStatus === "lulus") {
                            stat = "warning"; // Kuning
                        } else if(checkStatus === "cuti") {
                            stat = "info"; // Biru
                        }
                        
                        return '<span class="label label-'+stat+' control-label">'+text+'</span>';
                    }
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[2, 'asc']],
            rowId: function(a){
                return a.nis_siswa;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
        });
    }

            $('#form-import').on('submit',function(event){

            event.preventDefault();
            $('#import').text('Mengimport..');
            $('#import').attr('disabled',true);

            $.ajax({
                url: '<?=base_url($this->uri->segment(1).'/import')?>',
                method:"POST",
                data:new FormData(this),
                cache:false,
                contentType:false,
                processData:false,
                success:function(data){
                    $('#modal-import').modal('hide');
                    reload();
                    sweet('Sukses','Berhasil Import Data','success');
                    $('#import').text('Import');
                    $('#import').attr('disabled',false);
                },
                error: function (jqXHR, textStatus, errorThrown){
                    sweet('Oops...','Data gagal di import','error');
                }
            });
        });

		$('#form').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			ignore: "",
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
				var textConfirm = label == 'simpan' ? 'Apakah Anda yakin ingin menambah data ini?' : 'Apakah Anda yakin ingin mengubah data ini?';
				Swal({
					title: 'Konfirmasi',
					text: textConfirm,
					type: 'question',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, Simpan',
					cancelButtonText: 'Batal'
				}).then((result) => {
					if (result.value) {
						$('#simpan').text('Menyimpan...');
						$('#simpan').attr('disabled',true);
						var url,method;
						if (label == 'simpan'){
							url = '<?=base_url($this->uri->segment(1).'/Simpan')?>';
							method = 'Tambah';
						}
						else {
							url = '<?=base_url($this->uri->segment(1).'/Ubah')?>';
							method = 'Ubah';
						}
						var isi = new FormData($('#form')[0]);
						$.ajax({
							url: url,
							type:"POST",
							data: isi,
							contentType:false,
							processData:false,
							dataType:"JSON",
							success:function(data){
								$('#modal-form').modal('hide');
								reload();
								sweet('Di '+method,'Berhasil '+method+' Data','success');
								$('#simpan').text('Simpan');
								$('#simpan').attr('disabled',false);
							}
						});
					}
				});
			},
			invalidHandler: function (form) {}
		});


        $('#kelas').on('change',function(){

            var kelas = $(this).val();
        $('#list-data').DataTable().destroy();

            if (kelas != ''){
                load_data(kelas);
            }
            else{
                load_data();
            }
       });

	});

    function reload(){
        table.ajax.reload(null,false);
    }

    function Import(){
        label = 'import';
        $('#form-import')[0].reset();
        $('#modal-import').appendTo("body").modal('show');
        $('.modal-title').text('Import Data');
    }

	function sweet(judul,text,tipe){
        Swal({
            title: judul,
            text: text,
            type: tipe
        });
    }

	function Tambah(){
		label = 'simpan';
		$('#form')[0].reset();
        $('[name="tahun_ajaran"]').val('<?=current_school_year()?>');
        if ($.fn.iCheck) {
            $('[name="gender"]').iCheck('uncheck');
        }
		$('.form-group').removeClass('has-error');
		$('.help-block').empty(); 
		$('#modal-form').appendTo("body").modal('show');
		$('.modal-title').text('Tambah Data');
	}

        function Hapus(id){
            Swal({
                title: 'Ingin menghapus data?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.value) {
                    $.ajax({
                        url : "<?=base_url($this->uri->segment(1).'/Hapus')?>/"+id,
                        type: "POST",
                        dataType: "JSON",
                        success: function(data){
                            reload();
                            sweet('Dihapus !','Berhasil Hapus Data','success');
                        },
                        error: function (jqXHR, textStatus, errorThrown){
                            sweet('Oops...','Gagal Hapus Data','error');
                            console.log(jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });
        }

	function Ubah(id){
		label = 'ubah';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();

		$.ajax({
			url: "<?=base_url($this->uri->segment(1).'/edit/')?>"+id,
			type:"GET",
			dataType:"JSON",
			success:function(data){
				$('[name="id"]').val(data.nis_siswa);
                $('[name="nama"]').val(data.nama_siswa);
                $('[name="nis"]').val(data.nis_siswa);
                $('[name="agama"]').val(data.agama_siswa ? data.agama_siswa : 'Islam');
                $('[name="orangtua_wali"]').val(data.ortu_wali);
                $('[name="telpon"]').val(data.telp_siswa);
                $('[name="alamat"]').val(data.alamat_ssiwa);
                $('[name="tempat"]').val(data.tempat_lahirsiswa);
                $('[name="tanggal"]').val(data.tgl_lahirsiswa);
                $('[name="tanggal_masuk"]').val(data.tgl_masuk);
                $('[name="tahun_ajaran"]').val(data.thn_ajaran);
                $('[name="status"]').val(data.status_siswa).trigger('change');
                $('#modal-form [name="kelas"]').val(data.id_kelas).trigger('change');
                if ($.fn.iCheck) {
                    $('[name="gender"]').iCheck('uncheck');
                    $('[name="gender"][value="'+data.jk_siswa+'"]').iCheck('check');
                } else {
                    $('[name="gender"][value="'+data.jk_siswa+'"]').prop('checked', true);
                }
                $('#modal-form').appendTo("body").modal('show');
                $('.modal-title').text('Ubah Data'); 
			},
            error: function (jqXHR, textStatus, errorThrown){
                sweet('Oops...','Data tidak dapat diambil','error');
            }
		});
	}
</script>