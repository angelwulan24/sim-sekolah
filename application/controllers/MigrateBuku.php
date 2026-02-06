<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MigrateBuku extends CI_Controller {

	public function index()
	{
        // 1. Rename Table
        if ($this->db->table_exists('snack')) {
            $this->db->query("RENAME TABLE snack TO buku");
            echo "Table 'snack' renamed to 'buku'.<br>";
        } else {
            echo "Table 'snack' not found (maybe already renamed).<br>";
        }

        // 2. Update Data in 'pembayaran' table
        $this->db->where('nama', 'Uang Snack');
        $this->db->update('pembayaran', ['nama' => 'Uang Buku']);
        
        if ($this->db->affected_rows() > 0) {
            echo "Updated 'Uang Snack' to 'Uang Buku' in 'pembayaran' table.<br>";
        } else {
            echo "No rows updated in 'pembayaran' table (maybe already updated).<br>";
        }
        
        echo "Migration completed.";
	}
}
