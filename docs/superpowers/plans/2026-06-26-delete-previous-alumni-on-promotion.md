# Delete Previous Alumni on Promotion Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Automatically delete previous alumni records (including their user accounts and photo files) when a new batch of 6th-grade students is promoted to alumni (graduated).

**Architecture:** We will modify `ProsesKenaikan()` in `Kelas.php`. When `$ke == 'lulus'`, we will first fetch all existing alumni from the database, loop through them to delete their photo files, delete their corresponding user accounts from the `users` table, and delete their student records from the `siswa` table before updating the newly graduated students to alumni.

**Tech Stack:** PHP, CodeIgniter 3, MySQL

## Global Constraints

- Modify only `ProsesKenaikan()` inside `application/controllers/Kelas.php`.
- Ensure all associated files and user logins are properly cleaned up to prevent dangling data.

---

### Task 1: Update Kelas Controller `ProsesKenaikan` Method

**Files:**
- Modify: `application/controllers/Kelas.php:119-149`

**Interfaces:**
- Consumes: `$_POST['dari_kelas']`, `$_POST['ke_kelas']`
- Produces: Deleted previous alumni records/files and newly promoted class of students set as Alumni.

- [ ] **Step 1: Update ProsesKenaikan() in Kelas.php to clean up previous alumni**

Open [Kelas.php](file:///c:/Angga/Projects/sim-sekolah/application/controllers/Kelas.php) and modify `ProsesKenaikan()` method to implement the cleanup of previous alumni before updating new graduates.

Use the following implementation:
```php
	function ProsesKenaikan(){
		$dari = $this->input->post('dari_kelas');
		$ke = $this->input->post('ke_kelas');
		
		if (!empty($dari) && !empty($ke) && $dari != $ke) {
			if ($ke == 'lulus') {
                // Delete previous alumni
                $alumni_siswa = $this->db->get_where('siswa', array('status_siswa' => 'Alumni'))->result();
                if (!empty($alumni_siswa)) {
                    $alumni_user_ids = array();
                    foreach ($alumni_siswa as $al) {
                        if (!empty($al->id_users)) {
                            $alumni_user_ids[] = $al->id_users;
                        }
                        if (!empty($al->foto_siswa) && file_exists('./assets/images/siswa/' . $al->foto_siswa)) {
                            unlink('./assets/images/siswa/' . $al->foto_siswa);
                        }
                    }
                    if (!empty($alumni_user_ids)) {
                        $this->db->where_in('id_users', $alumni_user_ids);
                        $this->db->delete('users');
                    }
                    $this->db->where('status_siswa', 'Alumni');
                    $this->db->delete('siswa');
                }

				$data = array(
					'status_siswa' => 'Alumni',
					'id_kelas'     => NULL
				);
			} else {
				$data = array(
					'id_kelas' => $ke
				);
			}

			$this->db->where('id_kelas', $dari);
			$this->db->update('siswa', $data);

            $count = $this->db->affected_rows();
            if ($count > 0) {
                $this->session->set_flashdata('success', 'Berhasil memproses ' . $count . ' siswa.');
            } else {
                $this->session->set_flashdata('error', 'Tidak ada data siswa yang diupdate. Pastikan kelas asal memiliki siswa.');
            }
		} else {
            $this->session->set_flashdata('error', 'Kelas asal dan tujuan tidak boleh sama atau kosong.');
        }
		
		redirect('Kelas/Kenaikan');
	}
```

- [ ] **Step 2: Commit the changes**

Run command to commit:
```bash
git add application/controllers/Kelas.php
git commit -m "feat: automatically delete previous alumni on promotion"
```

- [ ] **Step 3: Verify manually**

Perform a class promotion of a class to 'lulus' (alumni) and check that any pre-existing alumni students (and their login accounts/profile photos) are correctly deleted.
