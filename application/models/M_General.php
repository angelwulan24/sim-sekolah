<?php
/**
 * @author P.S Nasution
 */
class M_General extends CI_Model{
   
    public function getAll($tables,$sort,$type){
        $this->db->order_by($sort,$type);
        return $this->db->get($tables);
    }
    
    public function countAll($tables){
        return $this->db->get($tables)->num_rows();
    }

    public function getByID($tables,$pk,$id,$type){
        $this->db->order_by($pk,$type);
        $this->db->where($pk,$id);
        return $this->db->get($tables);
    }

    public function countByID($tables,$pk,$id){
        $this->db->where($pk,$id);
        return $this->db->get($tables)->num_rows();
    }


    public function insert($tables,$data){
        $this->db->insert($tables,$data);
    }
    
    public function update($tables,$data,$pk,$id){
        $this->db->where($pk,$id);
        $this->db->update($tables,$data);
    }
    
    public function delete($tables,$pk,$id){
        $this->db->where($pk,$id);
        $this->db->delete($tables);
    }

    public function truncate ($tables){
        $this->db->truncate($tables);
    }
    
    function login($tables,$username,$password){
       return $this->db->get_where($tables,array('email'=>$username,'password'=>$password));        
    }

    public function save_log ($param){
        $sql = $this->db->insert_string('log',$param);
        $ex  = $this->db->query($sql);
        return $this->db->affected_rows($sql);
    }


    function upload_file($filename){ 
        $this->load->library('upload'); // Load librari upload
        
        $config['upload_path'] = './excel/';
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = '2048';
        $config['overwrite'] = true;
        $config['file_name'] = $filename;
    
        $this->upload->initialize($config); // Load konfigurasi uploadnya
        if($this->upload->do_upload('file')){ // Lakukan upload dan Cek jika proses upload berhasil
            // Jika berhasil :
            $return = array('status' => true, 'file' => $this->upload->data(), 'error' => '');
            return $return;
        }else{
            // Jika gagal :
            $return = array('status' => false, 'file' => '', 'error' => $this->upload->display_errors());
            return $return;
        }
    }

    public function insert_multiple($data){
        $this->db->insert_batch('siswa', $data);
    }

    function getSiswa($kls = ''){
        $this->datatables->select('nis_siswa, nama_siswa, jk_siswa');
        $this->datatables->from('siswa');
        $this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail($1)" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Detail</a></center> ','nis_siswa');
        if($kls != ''){
            $this->datatables->where('id_kelas',$kls);
        }
        return $this->datatables->generate();
    }

    function cek_laporan(){
        date_default_timezone_set('Asia/Jakarta');
        $this->db->where('tanggal',date('Y-m-d'));
        $cek = $this->db->get('laporan')->num_rows();
        if ($cek > 0){
        return;
        }
        else{
            $sql = $this->db->query("SELECT kas_masuk,kas_keluar,saldo_awal FROM laporan ORDER BY tanggal DESC LIMIT 1")->row_array();
            if(!empty($sql)){
                $kas_awal = $sql['saldo_awal'] + $sql['kas_masuk'] - $sql['kas_keluar'];
                $this->db->insert('laporan',array('tanggal'=>date('Y-m-d'),'saldo_awal'=>$kas_awal));
            }
            else{
                $this->db->insert('laporan',array('tanggal'=>date('Y-m-d')));
            }
        return;
        }
    }

    function update_kas($tipe,$nominal){
        // Ensure there is a laporan row for today
        $this->cek_laporan();

        // Calculate dynamic kas_masuk for today
        $today = date('Y-m-d');
        
        $pemasukan_today = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pemasukan AS DECIMAL(15,2))), 0) AS total FROM pemasukan WHERE tgl_pemasukan = '$today'")->row()->total;
        $tagihan_today = $this->db->query("SELECT COALESCE(SUM(CAST(j.nominal_tagihan AS DECIMAL(15,2))), 0) AS total 
                                            FROM tagihan_siswa AS t 
                                            JOIN jenis_tagihan AS j ON t.kode_tagihan = j.kode_tagihan 
                                            WHERE t.status = 'Lunas' AND DATE(t.tgl_pembayaran) = '$today'")->row()->total;
        $kas_masuk_new = $pemasukan_today + $tagihan_today;

        // Calculate dynamic kas_keluar for today
        $pengeluaran_today = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pengeluaran AS DECIMAL(15,2))), 0) AS total FROM pengeluaran WHERE tgl_pengeluaran = '$today'")->row()->total;
        $gaji_today = $this->db->query("SELECT COALESCE(SUM(CAST(jam AS DECIMAL(10,2)) * CAST(nominal_gaji AS DECIMAL(15,2))), 0) AS total FROM gaji WHERE tgl_gaji = '$today'")->row()->total;
        $kas_keluar_new = $pengeluaran_today + $gaji_today;

        // Update the laporan record for today
        $this->db->where('tanggal', $today);
        $this->db->update('laporan', array(
            'kas_masuk' => $kas_masuk_new,
            'kas_keluar' => $kas_keluar_new
        ));
        return;
    }

    function get_Laporan($id){

        $r = $this->db->query("SELECT tanggal FROM laporan where id = '$id'")->row_array();
        $t = $r['tanggal'];

        // Ambil SEMUA tagihan dari tabel tagihan_siswa yang dibayar pada tanggal $t
        $all_tagihan = $this->db->query("SELECT s.nama_siswa AS name, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal 
                                       FROM tagihan_siswa AS t
                                       JOIN siswa AS s ON s.nis_siswa = t.nis_siswa
                                       JOIN jenis_tagihan AS j ON j.kode_tagihan = t.kode_tagihan
                                       WHERE DATE(t.tgl_pembayaran) = '$t' 
                                       AND t.status = 'Lunas'")->result();

        // Pisahkan ke kategori lama untuk kompatibilitas tampilan (view laporan mengharapkan array terpisah)
        $spp = []; $ujian = []; $buku = []; $baju = []; $pendaftaran = []; $lainnya_tagihan = [];
        
        foreach($all_tagihan as $row) {
            $jenis = strtoupper($row->jenis_tagihan);
            if(strpos($jenis, 'SPP') !== false) {
                $spp[] = (object)['name' => $row->name, 'bulan' => str_replace('SPP - ', '', $row->jenis_tagihan), 'nominal' => $row->nominal];
            } else if(strpos($jenis, 'UJIAN') !== false) {
                $ujian[] = (object)['name' => $row->name, 'periode' => $row->jenis_tagihan, 'nominal' => $row->nominal];
            } else if(strpos($jenis, 'BUKU') !== false) {
                $buku[] = (object)['name' => $row->name, 'total' => $row->nominal, 'jumlah' => 1];
            } else if(strpos($jenis, 'BAJU') !== false) {
                $baju[] = (object)['name' => $row->name, 'total' => $row->nominal, 'jumlah' => 1];
            } else if(strpos($jenis, 'PENDAFTARAN') !== false) {
                $pendaftaran[] = (object)['siswa' => $row->name, 'nominal' => $row->nominal];
            } else {
                // Semua tagihan kustom (Praktek, dll) masuk ke kategori ini
                $lainnya_tagihan[] = (object)['keterangan' => $row->name . " (".$row->jenis_tagihan.")", 'nominal' => $row->nominal];
            }
        }

        $pemasukan = $this->db->query("SELECT nominal_pemasukan AS nominal, ket_pemasukan AS keterangan FROM pemasukan WHERE tgl_pemasukan = '$t'")->result();
        // Gabungkan tagihan kustom ke pemasukan lainnya agar muncul di laporan
        $pemasukan = array_merge($pemasukan, $lainnya_tagihan);

        $gaji     = $this->db->query("SELECT u.nama_guru AS name, g.periode, (g.jam * g.nominal_gaji) as gaji FROM gaji AS g JOIN guru AS u ON g.NUPTK = u.NUPTK WHERE g.tgl_gaji = '$t'")->result();
        $pengeluaran = $this->db->query("SELECT nominal_pengeluaran AS nominal, ket_pengeluaran AS keterangan FROM pengeluaran WHERE tgl_pengeluaran = '$t'")->result();

        return array('baju'=>$baju,'gaji'=>$gaji,'pemasukan'=>$pemasukan,'pendaftaran'=>$pendaftaran,'pengeluaran'=>$pengeluaran,'buku'=>$buku,'spp'=>$spp,'ujian'=>$ujian,'tanggal'=>$t);

    }
}


