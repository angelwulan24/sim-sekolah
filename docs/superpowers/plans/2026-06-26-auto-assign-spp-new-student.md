# Auto Assign SPP & Bills for New Students Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Automatically assign active academic year bills (with month-based filtering for SPP) when a new student is registered manually or imported via Excel.

**Architecture:** We will add a helper method `_assign_existing_bills($nis_siswa, $id_kelas, $thn_ajaran, $tgl_masuk)` in the `Siswa` controller. In `Simpan()` and `import()`, we will invoke this helper after student record insertion. The helper maps name-based SPP bills to chronological month/year representations and filters them against the student's entry date.

**Tech Stack:** PHP, CodeIgniter 3, MySQL

## Global Constraints

- Modify only the `Siswa` controller (`application/controllers/Siswa.php`).
- Ensure keys in Excel import match the database schema to avoid insert errors.

---

### Task 1: Add Auto-Assign Bill Logic and Update Siswa Controller

**Files:**
- Modify: `application/controllers/Siswa.php`

**Interfaces:**
- Consumes: Database tables `jenis_tagihan` and `siswa`
- Produces: Entries in `tagihan_siswa` matching the new student's criteria

- [ ] **Step 1: Correct Excel import keys in Siswa.php**

Open [Siswa.php](file:///c:/Angga/Projects/sim-sekolah/application/controllers/Siswa.php) and locate the `import()` method. Replace `alamat_siswa` with `alamat_ssiwa`, and `tahun_ajaran` with `thn_ajaran` in the sheet row array mapping (around lines 76-78).

- [ ] **Step 2: Add `_assign_existing_bills` helper method to Siswa.php**

Add the private helper method at the end of the `Siswa` class:

```php
    private function _assign_existing_bills($nis_siswa, $id_kelas, $thn_ajaran, $tgl_masuk) {
        if (empty($thn_ajaran)) {
            return;
        }

        // Query all jenis_tagihan matching the academic year and class (or all classes)
        $this->db->group_start();
        $this->db->where('id_kelas', NULL);
        if (!empty($id_kelas)) {
            $this->db->or_where('id_kelas', $id_kelas);
        }
        $this->db->group_end();
        $this->db->where('tahun_ajaran', $thn_ajaran);
        $jenis_tagihan = $this->db->get('jenis_tagihan')->result();

        if (empty($jenis_tagihan)) {
            return;
        }

        $data_tagihan = array();
        foreach ($jenis_tagihan as $tag) {
            $is_spp = (strpos(strtoupper($tag->nama_tagihan), 'SPP') !== false);
            if ($is_spp) {
                // If student's entry date or tag deadline is empty, default to assigning it
                if (!empty($tgl_masuk) && !empty($tag->tenggat_waktu)) {
                    $entry_year = (int)date('Y', strtotime($tgl_masuk));
                    $entry_month = (int)date('m', strtotime($tgl_masuk));
                    $tag_year = (int)date('Y', strtotime($tag->tenggat_waktu));
                    $tag_month = (int)date('m', strtotime($tag->tenggat_waktu));

                    $entry_val = $entry_year * 12 + $entry_month;
                    $tag_val = $tag_year * 12 + $tag_month;

                    if ($tag_val < $entry_val) {
                        // Skip SPP for months that have already passed
                        continue;
                    }
                }
            }

            // Check if this student already has this bill assigned (to avoid duplicate assignment)
            $check = $this->db->get_where('tagihan_siswa', array(
                'nis_siswa' => $nis_siswa,
                'kode_tagihan' => $tag->kode_tagihan
            ))->row();

            if (!$check) {
                $data_tagihan[] = array(
                    'nis_siswa'      => $nis_siswa,
                    'kode_tagihan'   => $tag->kode_tagihan,
                    'status'         => 'Belum Lunas',
                    'tgl_pembayaran' => NULL
                );
            }
        }

        if (!empty($data_tagihan)) {
            $chunks = array_chunk($data_tagihan, 100);
            foreach ($chunks as $chunk) {
                $this->db->insert_batch('tagihan_siswa', $chunk);
            }
        }
    }
```

- [ ] **Step 3: Call `_assign_existing_bills` inside `Simpan()` and `import()`**

In `Simpan()`:
Call the helper right after `$this->M_General->insert($this->table,$insert);`:
```php
        $this->_assign_existing_bills($insert['nis_siswa'], $insert['id_kelas'], $insert['thn_ajaran'], $insert['tgl_masuk']);
```

In `import()`:
Call the helper in a loop after `$this->M_General->insert_multiple($data);`:
```php
        foreach ($data as $siswa_data) {
            $this->_assign_existing_bills(
                $siswa_data['nis_siswa'],
                $siswa_data['id_kelas'],
                $siswa_data['thn_ajaran'],
                $siswa_data['tgl_masuk']
            );
        }
```

- [ ] **Step 4: Commit changes**

Run command to commit:
```bash
git add application/controllers/Siswa.php
git commit -m "feat: automatically assign SPP and bills to new students on registration/import"
```

- [ ] **Step 5: Verify manually**

Simulate registering a new student for academic year `2025/2026` with entry date `2025-08-10` and verify they only get September and August SPP tags, but not July.
