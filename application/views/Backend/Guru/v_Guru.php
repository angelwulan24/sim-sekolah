<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">

            <div class="pull-right">
                <?php if ($this->session->userdata('role') == 1) { ?>
            	<a href="#" onclick="Tambah()" class="btn btn-primary btn-sm">Tambah Data </a>
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
                      <th>Nama</th>
                      <th>NIP/NIK</th>
                      <th style="width: 10PX;">JK</th>
                      <th style="width: 20px;">Bidang Studi</th>
                      <th>Alamat</th>
                      <th>Telepon</th>
                      <th width="50">Status</th>
                      <th width="250">Aksi</th>
			            </tr>
		            </thead>
		            <tbody>
		              
		            </tbody>
		        </table>
	       	</div>
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
                    <label class="control-label"> NIP/NIK</label>
                    <div><input type="text" onkeypress="return Angka(this)" required="" placeholder="NIP/NIK" autocomplete="off" name="nip" class="form-control"></div>
                </div>
            	<div class="form-group">
                    <div class="ra">
                		<label class="control-label">Jenis Kelamin</label><br>
                		<input type="radio" class="minimal" required="" name="gender" value="Laki-Laki" ><span class="lbl"> Laki-Laki</span>
                		<input type="radio" required="" name="gender" class="minimal" value="Perempuan"><span class="lbl"> Perempuan</span>
                    </div>
            	</div>
                <div class="form-group">
                    <label class="control-label"> Bidang Studi</label>
                    <select name="bidang" required="" data-placeholder="--Pilih Bidang Studi--" class="form-control">
                        <option value="">--Pilih Bidang Studi--</option>
                        <option value="Guru Kelas">Guru Kelas</option>
                        <option value="Pendidikan Agama">Pendidikan Agama</option>
                        <option value="PPKn">PPKn</option>
                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                        <option value="Matematika">Matematika</option>
                        <option value="IPA">IPA</option>
                        <option value="IPS">IPS</option>
                        <option value="Seni Budaya">Seni Budaya</option>
                        <option value="PJOK">PJOK</option>
                        <option value="Prakarya">Prakarya</option>
                        <option value="TIK / Informatika">TIK / Informatika</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Geografi">Geografi</option>
                        <option value="Ekonomi">Ekonomi</option>
                        <option value="Sosiologi">Sosiologi</option>
                        <option value="Fisika">Fisika</option>
                        <option value="Kimia">Kimia</option>
                        <option value="Biologi">Biologi</option>
                        <option value="Bahasa Daerah">Bahasa Daerah</option>
                        <option value="Bimbingan Konseling (BK)">Bimbingan Konseling (BK)</option>
                        <option value="BTA">BTA</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label"> Alamat</label>
                    <div><input type="text" required="" placeholder="Alamat" autocomplete="off" name="alamat" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Telepon</label>
                    <div><input type="text" required="" placeholder="Telepon" autocomplete="off" name="telepon" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label">Status</label>
                    <select name="status" required="" data-placeholder="--Pilih--" class="form-control">
                        <option value="">--Pilih--</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
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
                "type": "POST"
            },
            columns: [
                {
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "foto",
                    "render": function(data, type, row) {
                        if(data) return '<img src="<?=base_url("assets/images/guru/")?>' + data + '" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                        return '<img src="https://via.placeholder.com/50" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                    }
                },
                {"data": "name"},
                {"data": "nip"},
                {"data": "sex"},
                {"data": "bidang"},
                {"data": "alamat"},
                {"data": "number"},
                {
                    "data": "status",
                    "render": function(data, type, row) {
                        var stat = "success";
                        if(data == "Berhenti") stat = "danger";
                        else if(data == "Cuti") stat = "warning";
                        return '<span class="label label-'+stat+' control-label">'+data+'</span>';
                    }
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[1, 'asc']],
            rowId: function(a){
                return a;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
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
			},
			invalidHandler: function (form) {}
		});

	});

    function reload(){
        table.ajax.reload(null,false);
    }
	function sweet(judul,text,tipe){
        Swal({
            title: judul,
            text: text,
            type: tipe
        });
    };

	function Tambah(){
		label = 'simpan';
		$('#form')[0].reset();
        if ($.fn.iCheck) {
            $('[name="gender"]').iCheck('uncheck');
        }
		$('.form-group').removeClass('has-error');
		$('.help-block').empty(); 
		$('#modal-form').modal('show');
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

        function DetailGaji(id) {
            document.location.href= "<?= base_url($this->uri->segment(1).'/DetailGaji/')?>"+id;
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
				$('[name="id"]').val(data.id);
                $('[name="nama"]').val(data.name);
                $('[name="nip"]').val(data.nip);
                $('[name="telepon"]').val(data.number);
                $('[name="alamat"]').val(data.alamat);
                $('[name="bidang"]').val(data.bidang).trigger('change');
                if ($.fn.iCheck) {
                    $('[name="gender"]').iCheck('uncheck');
                    $('[name="gender"][value="'+data.sex+'"]').iCheck('check');
                } else {
                    $('[name="gender"][value="'+data.sex+'"]').prop('checked', true);
                }
                $('[name="status"]').val(data.status).trigger('change');
                
                $('#modal-form').modal('show');
                $('.modal-title').text('Ubah Data'); 
			},
            error: function (jqXHR, textStatus, errorThrown){
                sweet('Oops...','Data tidak dapat diambil','error');
            }
		});
	}
</script>