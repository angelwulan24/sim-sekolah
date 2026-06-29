# SPP Deadline 14th Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ensure that when school fees (SPP) are saved, the `tenggat_waktu` database column for each monthly SPP record is set to the 14th day of that specific month and year.

**Architecture:** We will modify `Simpan()` in `Transaksi.php`. In the loop that creates each month's SPP record, we will map the month name (e.g., 'Juli', 'Januari') to its numeric value. Based on the academic year format `YYYY/YYYY` and whether the month index is >= 7, we determine the correct year. We then format the date as `YYYY-MM-14` and store it in the database.

**Tech Stack:** PHP, CodeIgniter 3, MySQL

## Global Constraints

- Modify only the `Simpan` method behavior in `application/controllers/Transaksi.php` for SPP.
- The date must be formatted in standard ISO format `YYYY-MM-DD` (specifically `YYYY-MM-14`) to allow correct parsing by `strtotime()`.

---

### Task 1: Update Transaksi Controller `Simpan` Method

**Files:**
- Modify: `application/controllers/Transaksi.php:124-138`

**Interfaces:**
- Consumes: `$_POST['tahun_ajaran']`, `$_POST['nama']`, `$_POST['nominal']`, `$_POST['kelas']`, `$_POST['bulan_awal']`, `$_POST['bulan_akhir']`
- Produces: Data inserted into `jenis_tagihan` database table with the calculated `tenggat_waktu` value.

- [ ] **Step 1: Modify SPP creation loop to calculate tenggat_waktu**

Open [Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/controllers/Transaksi.php) and look at the loop inside `Simpan()` around lines 124-138. Replace the hardcoded `'tenggat_waktu' => 'Setiap Bulan'` with the calculated `YYYY-MM-14` date.

Use the following implementation details:
```php
            foreach ($bulan as $bln) {
                $current_kode = $prefix . '-' . str_pad($start_num, 4, '0', STR_PAD_LEFT);
                $start_num++;

                // Map Indonesian month name to number (1-12)
                $m_idx = array_search($bln, $all_months);
                $m_num = ($m_idx !== false) ? ($m_idx + 1) : 1;

                // Determine correct year from tahun_ajaran (e.g., "2025/2026")
                $years = explode('/', $tahun_ajaran);
                if (count($years) == 2) {
                    $t_year = ($m_num >= 7) ? (int)$years[0] : (int)$years[1];
                } else {
                    $t_year = !empty($tahun_ajaran) ? (int)$tahun_ajaran : (int)date('Y');
                }

                // Format deadline date to the 14th day of the month and year
                $tenggat_spp = sprintf('%04d-%02d-14', $t_year, $m_num);

                // Insert fee type
                $fee_type = array(
                    'kode_tagihan'    => $current_kode,
                    'nama_tagihan'    => 'SPP - ' . $bln,
                    'nominal_tagihan' => $nominal,
                    'tenggat_waktu'   => $tenggat_spp,
                    'tahun_ajaran'    => $tahun_ajaran,
                    'id_kelas'        => $id_kelas
                );
                $this->db->insert('jenis_tagihan', $fee_type);
```

- [ ] **Step 2: Commit the changes**

Run command to commit:
```bash
git add application/controllers/Transaksi.php
git commit -m "feat: set SPP deadline to the 14th of each month and year"
```

- [ ] **Step 3: Verify manually**

Create a new SPP tagihan for year `2025/2026` from the admin panel UI or insert directly via a controller test, and check the database table `jenis_tagihan` to verify that `tenggat_waktu` records are successfully populated as `2025-07-14`, `2025-08-14`, ..., `2026-06-14`.
