<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <a href="#" onclick="Tambah()" class="btn btn-primary btn-sm">Tambah Data </a>
        </div>
        <div class="row" style="margin-bottom: 15px; padding: 0 15px;">
            <div class="col-md-3">
                <select id="jenis_filter" class="form-control">
                    <option value="">-- Pilih Jenis Filter --</option>
                    <option value="hari">Harian</option>
                    <option value="bulan">Bulanan</option>
                    <option value="tahun">Tahunan</option>
                </select>
            </div>
            
            <div class="col-md-3" id="wrap-filter-hari" style="display:none;">
                <input type="date" id="filter_hari" class="form-control" placeholder="Pilih Hari">
            </div>
            
            <div class="col-md-3" id="wrap-filter-bulan" style="display:none;">
                <input type="month" id="filter_bulan" class="form-control">
            </div>
            
            <div class="col-md-3" id="wrap-filter-tahun" style="display:none;">
                <select id="filter_tahun" class="form-control">
                    <option value="">-- Pilih Tahun --</option>
                    <?php 
                        for($i = date('Y'); $i >= date('Y')-5; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <button id="btn-filter" class="btn btn-success btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <button id="btn-reset" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</button>
            </div>
        </div>
	    <div class="box-body">
	    	<div class="table-responsive">    	
		        <table id="list-data" class="table table-bordered table-hover">
		            <thead>
			            <tr>
                      <th style="width: 10px;">No</th>
                      <th>Tanggal</th>
                      <th>Nominal</th>
                      <th>Keterangan</th>
                      <th width="120">Aksi</th>
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
            		<label class="control-label"> Nominal Pemasukan</label>
            		<div><input type="text" required="" placeholder="Nominal Pemasukan" onkeypress="return Angka(this)" autocomplete="off" name="nominal" class="form-control"></div>
            	</div>
                <div class="form-group">
                    <label class="control-label"> Keterangan</label>
                    <div><input type="text" required="" placeholder="Keterangan" autocomplete="off" name="keterangan" class="form-control"></div>
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
                "url": "<?= base_url('Lainnya/getData') ?>",
                "type": "POST",
                "data": function (d) {
                    d.jenis = $('#jenis_filter').val();
                    if(d.jenis === 'hari'){
                        d.tanggal = $('#filter_hari').val();
                    } else if(d.jenis === 'bulan'){
                        d.tanggal = $('#filter_bulan').val();
                    } else if(d.jenis === 'tahun'){
                        d.tanggal = $('#filter_tahun').val();
                    }
                }
            },
            columns: [
                {
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {"data": "Tgl"},
                {"data": "Total",render: $.fn.dataTable.render.number('.',',','')},
                {"data": "keterangan"},
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[1, 'desc']],
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
							url = '<?=base_url('Lainnya/Simpan')?>';
							method = 'Tambah';
						} else {
							url = '<?=base_url('Lainnya/Ubah')?>';
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
							},
							error: function (jqXHR, textStatus, errorThrown){
								sweet('Oops...','Gagal menyimpan data','error');
								$('#simpan').text('Simpan');
								$('#simpan').attr('disabled',false);
							}
						});
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
    }

	function Tambah(){
		label = 'simpan';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty(); 
		$('#modal-form').appendTo("body").modal('show');
		$('.modal-title').text('Tambah Data');
	}

	function Ubah(id){
		label = 'ubah';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();

		$.ajax({
			url: "<?=base_url('Lainnya/edit/')?>"+id,
			type:"GET",
			dataType:"JSON",
			success:function(data){
				$('[name="id"]').val(data.id_pemasukan);
				$('[name="nominal"]').val(data.nominal_pemasukan);
				$('[name="keterangan"]').val(data.ket_pemasukan);
				$('#modal-form').appendTo("body").modal('show');
				$('.modal-title').text('Ubah Data');
			},
			error: function (jqXHR, textStatus, errorThrown){
				sweet('Oops...','Data tidak dapat diambil','error');
			}
		});
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
					url : "<?=base_url('Lainnya/Hapus')?>/"+id,
					type: "POST",
					dataType: "JSON",
					success: function(data){
						reload();
						sweet('Dihapus !','Berhasil Hapus Data','success');
					},
					error: function (jqXHR, textStatus, errorThrown){
						sweet('Oops...','Gagal Hapus Data','error');
					}
				});
			}
		});
	}

    $('#jenis_filter').change(function(){
        $('#wrap-filter-hari').hide();
        $('#wrap-filter-bulan').hide();
        $('#wrap-filter-tahun').hide();
        
        $('#filter_hari').val('');
        $('#filter_bulan').val('');
        $('#filter_tahun').val('');
        
        var jenis = $(this).val();
        if(jenis === 'hari') {
            $('#wrap-filter-hari').show();
        } else if(jenis === 'bulan') {
            $('#wrap-filter-bulan').show();
        } else if(jenis === 'tahun') {
            $('#wrap-filter-tahun').show();
        }
    });

    $('#btn-filter').click(function(){
        table.ajax.reload();
    });

    $('#btn-reset').click(function(){
        $('#jenis_filter').val('').trigger('change');
        table.ajax.reload();
    });

</script>