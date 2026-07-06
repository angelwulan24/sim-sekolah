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
        $this->datatables->select('nis, nama_siswa, jk_siswa');
        $this->datatables->from('siswa');
        $this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail($1)" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Detail</a></center> ','nis');
        if($kls != ''){
            $this->datatables->where('id_kelas',$kls);
        }
        return $this->datatables->generate();
    }

    function cek_laporan(){
        // Tabel laporan telah dihapus - fungsi ini dipertahankan untuk kompatibilitas
        return;
    }

    function update_kas($tipe,$nominal){
        // Tabel laporan telah dihapus.
        // Data keuangan kini dihitung dinamis langsung dari tabel pemasukan, tagihan_siswa, dan pengeluaran.
        return;
    }

    function get_Laporan($id){

        // $id adalah date string (Y-m-d) sejak tabel laporan dihapus
        $t = $id;

        // Ambil SEMUA tagihan dari tabel tagihan_siswa yang dibayar pada tanggal $t
        $all_tagihan = $this->db->query("SELECT s.nama_siswa AS name, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal 
                                       FROM tagihan_siswa AS t
                                       JOIN siswa AS s ON s.nis = t.nis_siswa
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

        $pemasukan = $this->db->query("SELECT nominal_pemasukan AS nominal, ket_pemasukan AS keterangan FROM pemasukan WHERE DATE(tgl_pemasukan) = '$t'")->result();

        $gaji     = $this->db->query("SELECT u.nama_guru AS name, g.periode, (g.jam * g.nominal_gaji) as gaji FROM gaji AS g JOIN guru AS u ON g.NUPTK = u.NUPTK WHERE g.tgl_gaji = '$t'")->result();
        $pengeluaran = $this->db->query("SELECT nominal_pengeluaran AS nominal, ket_pengeluaran AS keterangan FROM pengeluaran p WHERE DATE(tgl_pengeluaran) = '$t' AND NOT EXISTS (SELECT 1 FROM gaji g WHERE g.id_pengeluaran = p.id_pengeluaran)")->result();


        return array('baju'=>$baju,'gaji'=>$gaji,'pemasukan'=>$pemasukan,'pendaftaran'=>$pendaftaran,'pengeluaran'=>$pengeluaran,'buku'=>$buku,'spp'=>$spp,'ujian'=>$ujian,'tagihan_lainnya'=>$lainnya_tagihan,'tanggal'=>$t);

    }

    function get_Laporan_periode($awal, $akhir){
        // $awal dan $akhir adalah date string (Y-m-d)
        $t_awal = $awal;
        $t_akhir = $akhir;

        // Ambil SEMUA tagihan dari tabel tagihan_siswa yang dibayar pada rentang tanggal $t_awal s.d $t_akhir
        $all_tagihan = $this->db->query("SELECT s.nama_siswa AS name, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal 
                                       FROM tagihan_siswa AS t
                                       JOIN siswa AS s ON s.nis = t.nis_siswa
                                       JOIN jenis_tagihan AS j ON j.kode_tagihan = t.kode_tagihan
                                       WHERE DATE(t.tgl_pembayaran) >= '$t_awal' AND DATE(t.tgl_pembayaran) <= '$t_akhir'
                                       AND t.status = 'Lunas'")->result();

        // Pisahkan ke kategori
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
                $lainnya_tagihan[] = (object)['keterangan' => $row->name . " (".$row->jenis_tagihan.")", 'nominal' => $row->nominal];
            }
        }

        $pemasukan = $this->db->query("SELECT nominal_pemasukan AS nominal, ket_pemasukan AS keterangan FROM pemasukan WHERE DATE(tgl_pemasukan) >= '$t_awal' AND DATE(tgl_pemasukan) <= '$t_akhir'")->result();

        $gaji     = $this->db->query("SELECT u.nama_guru AS name, g.periode, (g.jam * g.nominal_gaji) as gaji FROM gaji AS g JOIN guru AS u ON g.NUPTK = u.NUPTK WHERE g.tgl_gaji >= '$t_awal' AND g.tgl_gaji <= '$t_akhir'")->result();
        $pengeluaran = $this->db->query("SELECT nominal_pengeluaran AS nominal, ket_pengeluaran AS keterangan FROM pengeluaran p WHERE DATE(tgl_pengeluaran) >= '$t_awal' AND DATE(tgl_pengeluaran) <= '$t_akhir' AND NOT EXISTS (SELECT 1 FROM gaji g WHERE g.id_pengeluaran = p.id_pengeluaran)")->result();

        return array('baju'=>$baju,'gaji'=>$gaji,'pemasukan'=>$pemasukan,'pendaftaran'=>$pendaftaran,'pengeluaran'=>$pengeluaran,'buku'=>$buku,'spp'=>$spp,'ujian'=>$ujian,'tagihan_lainnya'=>$lainnya_tagihan,'awal'=>$t_awal,'akhir'=>$t_akhir);
    }
}


