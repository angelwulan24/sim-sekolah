# Overhaul Database Schema and Refactor Codebase Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Reconstruct the school management database schema with clean foreign keys and normalized column structures, and refactor the CodeIgniter application's controllers, models, and views to use the new definitions.

**Architecture:** Initialize the database with the new schema, create a PHP CLI seeder script, and incrementally refactor logical components (Auth, Master Data, Inflow, Outflow, Reports, Student Area) verifying syntax and pages at each phase.

**Tech Stack:** PHP, CodeIgniter 3, MySQL/MariaDB

## Global Constraints
- All column references must strictly match the new table structures.
- All file operations must use full absolute paths.
- Run linter on all modified files at the end of each task.

---

### Task 1: Reconstruct Database Schema & Implement Seeder Script

**Files:**
- Modify: `schema.sql`
- Create: `seed_new_schema.php`

**Interfaces:**
- Consumes: None
- Produces: Corrected SQL schema file and a runnable CLI seed script.

- [ ] **Step 1: Overwrite schema.sql**
  Write the clean SQL table structures, indices, foreign key constraints, and view definitions matching the spec to [schema.sql](file:///c:/Angga/Projects/sim-sekolah/schema.sql).

- [ ] **Step 2: Create seed_new_schema.php**
  Write a PHP CLI script at [seed_new_schema.php](file:///c:/Angga/Projects/sim-sekolah/seed_new_schema.php) to drop all tables, execute `schema.sql`, and seed mock data:
  ```php
  <?php
  $conn = new mysqli('localhost', 'root', '123', 'sim_sekolah', 33060);
  if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

  // Drop all current tables and views
  $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
  $tables = ['tagihan_siswa', 'pemasukan', 'jenis_tagihan', 'gaji', 'siswa', 'kelas', 'guru', 'users', 'pengeluaran', 'laporan'];
  foreach ($tables as $t) {
      $conn->query("DROP TABLE IF EXISTS `$t` CASCADE;");
  }
  $conn->query("DROP VIEW IF EXISTS `v_pengeluaran_gabungan` CASCADE;");

  // Read and run schema.sql
  $sql = file_get_contents('schema.sql');
  $conn->multi_query($sql);
  while ($conn->next_result()) { /* flush multi query */ }

  // Insert mock data
  $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
  
  // Seed Users (Admin: admin@gmail.com / 123456, Student: 11111111 / 123456)
  $p1 = password_hash('123456', PASSWORD_DEFAULT);
  $conn->query("INSERT INTO users (id_users, email, password, nama_users, role, gambar) VALUES 
    (1, 'admin@gmail.com', '$p1', 'Administrator', 1, 'user.png'),
    (2, '11111111', '$p1', 'Angelina Wulandari', 3, 'user.png')");

  // Seed Guru
  $conn->query("INSERT INTO guru (NUPTK, nama_guru, jk_guru, agama_guru, bidang_studi, alamat_guru, status_guru, telp_guru, foto_guru) VALUES 
    ('1001', 'Nurul Nuraeni', 'Perempuan', 'Islam', 'Pendidikan Agama', 'Jl. Cikande', 'Aktif', '08123456789', 'foto.jpg')");

  // Seed Kelas
  $conn->query("INSERT INTO kelas (id_kelas, nama_kelas, NUPTK, ket_kelas) VALUES 
    (1, 'Kelas 1', '1001', 'Wali Kelas Agama')");

  // Seed Siswa
  $conn->query("INSERT INTO siswa (nis_siswa, nama_siswa, jk_siswa, agama_siswa, status_siswa, ortu_wali, tempat_lahirsiswa, tgl_lahirsiswa, alamat_ssiwa, telp_siswa, id_kelas, foto_siswa, tgl_masuk, thn_ajaran, id_users) VALUES 
    ('11111111', 'Angelina Wulandari', 'Perempuan', 'Islam', 'Aktif', 'Kastini', 'Serang', '2019-11-24', 'Cikande Permai', '085216165214', 1, 'avatar.jpg', '2026-04-20', '2025/2026', 2)");

  // Seed Jenis Tagihan
  $conn->query("INSERT INTO jenis_tagihan (kode_tagihan, nama_tagihan, nominal_tagihan, tenggat_waktu, tahun_ajaran, id_kelas) VALUES 
    ('KM-0001', 'SPP - Juli', '15000', 'Setiap Bulan', '2025/2026', 1)");

  // Seed Tagihan Siswa
  $conn->query("INSERT INTO tagihan_siswa (id_tagihan, id_pemasukan, nis_siswa, kode_tagihan, status, tgl_pembayaran) VALUES 
    (1, NULL, '11111111', 'KM-0001', 'Belum Lunas', NULL)");

  echo "Database initialized and seeded successfully.\n";
  ```

- [ ] **Step 3: Run the seeder script**
  Run: `php seed_new_schema.php`
  Expected Output: `Database initialized and seeded successfully.`

- [ ] **Step 4: Commit schema changes**
  Run: `git add schema.sql seed_new_schema.php; git commit -m "db: initialize new schema tables and CLI seeder"`

---

### Task 2: Core Helpers & General Models Realignment

**Files:**
- Modify: `application/helpers/system_helper.php`
- Modify: `application/models/M_General.php`

**Interfaces:**
- Consumes: None
- Produces: `current_school_year()` helper and realigned DDL functions.

- [ ] **Step 1: Add current_school_year() in system_helper.php**
  Append this function:
  ```php
  function current_school_year() {
      $month = (int)date('m');
      $year = (int)date('Y');
      if ($month >= 7) {
          return $year . '/' . ($year + 1);
      } else {
          return ($year - 1) . '/' . $year;
      }
  }
  ```

- [ ] **Step 2: Update M_General.php**
  Rewrite column mappings inside `M_General.php` (such as `getSiswa()`, `update_kas()`, and `get_Laporan()`) to query the new column and table names. Specifically, `get_Laporan()` must load cash flow details correctly by summing `pemasukan` + `tagihan_siswa` (paid) for `kas_masuk`, and `pengeluaran` + `gaji` (hours * nominal) for `kas_keluar`.

- [ ] **Step 3: Run PHP linter**
  Run: `php -l application/helpers/system_helper.php; php -l application/models/M_General.php`
  Expected Output: `No syntax errors detected`

- [ ] **Step 4: Commit changes**
  Run: `git add application/helpers/system_helper.php application/models/M_General.php; git commit -m "refactor: implement school year helper and align general models"`

---

### Task 3: Refactor Authentication and User Access

**Files:**
- Modify: `application/controllers/Auth.php`
- Modify: `application/views/v_Login.php`
- Modify: `application/views/v_Registrasi.php`

**Interfaces:**
- Consumes: None
- Produces: Correct login and registration handlers pointing to `users` table.

- [ ] **Step 1: Update Auth.php**
  Modify queries to use `id_users`, `nama_users` instead of `id` and `name`, and remove validations for `telpon` or `active` since they are deleted.

- [ ] **Step 2: Update Login & Registration Views**
  Correct name inputs to bind to `nama_users`.

- [ ] **Step 3: Run PHP linter**
  Run: `php -l application/controllers/Auth.php; php -l application/views/v_Login.php; php -l application/views/v_Registrasi.php`
  Expected Output: `No syntax errors detected`

- [ ] **Step 4: Commit changes**
  Run: `git add application/controllers/Auth.php application/views/v_Login.php application/views/v_Registrasi.php; git commit -m "refactor: update auth logic to use new users table schema"`

---

### Task 4: Refactor Master Data (Guru, Kelas, Siswa)

**Files:**
- Modify: `application/controllers/Guru.php`, `application/models/M_Guru.php`, `application/views/Backend/Guru/v_Guru.php`, `application/views/Backend/Guru/v_Detail_Gaji.php`
- Modify: `application/controllers/Kelas.php`, `application/models/M_Kelas.php`, `application/views/Backend/Kelas/v_Kelas.php`, `application/views/Backend/Kelas/v_Detail.php`
- Modify: `application/controllers/Siswa.php`, `application/models/M_Siswa.php`, `application/views/Backend/Siswa/v_Siswa.php`

**Interfaces:**
- Consumes: Core helper methods and new schema tables.
- Produces: Realigned CRUD views for Master Data.

- [ ] **Step 1: Refactor Guru Component**
  Replace columns in `Guru.php` and `M_Guru.php` with `NUPTK` and `nama_guru` systems.

- [ ] **Step 2: Refactor Kelas Component**
  Point the teacher assignment select query to `guru.NUPTK` and display name using joins.

- [ ] **Step 3: Refactor Siswa Component**
  Change student registrations to save to `siswa` with columns: `nis_siswa` (username), `id_kelas`, and generate `id_users`.

- [ ] **Step 4: Run PHP linter on all modified files**
  Verify syntax on modified controllers, models, and views.

- [ ] **Step 5: Commit changes**
  Run: `git add application/controllers/Guru.php application/models/M_Guru.php application/views/Backend/Guru/ application/controllers/Kelas.php application/models/M_Kelas.php application/views/Backend/Kelas/ application/controllers/Siswa.php application/models/M_Siswa.php application/views/Backend/Siswa/; git commit -m "refactor: align guru, kelas, and siswa components"`

---

### Task 5: Refactor Financial Inflow (jenis_tagihan, tagihan_siswa, pemasukan)

**Files:**
- Modify: `application/controllers/Transaksi.php`, `application/models/M_Transaksi.php`, `application/views/Backend/Transaksi/v_Transaksi.php`
- Modify: `application/controllers/Tagihan.php`, `application/models/M_Tagihan.php`, `application/views/Backend/Tagihan/v_Tagihan.php`, `application/views/Backend/Tagihan/v_Detail.php`
- Modify: `application/controllers/Lainnya.php`, `application/models/M_Lainnya.php`, `application/views/Backend/Lainnya/v_Lainnya.php`

**Interfaces:**
- Consumes: `siswa.nis_siswa`, `kelas.id_kelas`, `jenis_tagihan.kode_tagihan`.
- Produces: Working billing setup and other income recording pages.

- [ ] **Step 1: Refactor Transaksi (jenis_tagihan)**
  Update Transaksi controller to manage `jenis_tagihan` using columns `kode_tagihan` (PK), `nama_tagihan`, `nominal_tagihan`, and remove the `tipe` column option.

- [ ] **Step 2: Refactor Tagihan (tagihan_siswa)**
  Align `Tagihan.php` to handle `tagihan_siswa` table, updating joins to `siswa.nis_siswa` and `jenis_tagihan.kode_tagihan`.

- [ ] **Step 3: Refactor Lainnya (pemasukan)**
  Update Lainnya controller/model to use `pemasukan` table structure.

- [ ] **Step 4: Run PHP linter**
  Verify all modified files have correct PHP syntax.

- [ ] **Step 5: Commit changes**
  Run: `git add application/controllers/Transaksi.php application/models/M_Transaksi.php application/views/Backend/Transaksi/ application/controllers/Tagihan.php application/models/M_Tagihan.php application/views/Backend/Tagihan/ application/controllers/Lainnya.php application/models/M_Lainnya.php application/views/Backend/Lainnya/; git commit -m "refactor: align cash inflow components"`

---

### Task 6: Refactor Financial Outflow (gaji, pengeluaran)

**Files:**
- Modify: `application/controllers/Gaji.php`, `application/models/M_Gaji.php`, `application/views/Backend/Gaji/v_Gaji.php`, `application/views/Backend/Gaji/v_Detail.php`
- Modify: `application/controllers/Pengeluaran.php`, `application/models/M_Pengeluaran.php`, `application/views/Backend/Pengeluaran/v_Pengeluaran.php`

**Interfaces:**
- Consumes: `guru.NUPTK`
- Produces: Corrected expense tracking and teacher wage disbursement pages.

- [ ] **Step 1: Refactor Gaji**
  Update `Gaji.php` and `M_Gaji.php` to query `gaji` columns: `id_gaji`, `NUPTK` (FK), `periode`, `jam`, `nominal_gaji`, `tgl_gaji`.

- [ ] **Step 2: Refactor Pengeluaran**
  Update `Pengeluaran.php` and `M_Pengeluaran.php` to query `pengeluaran` columns: `id_pengeluaran`, `nominal_pengeluaran`, `tgl_pengeluaran`, `ket_pengeluaran`, `bukti`.

- [ ] **Step 3: Run PHP linter**
  Verify syntax on modified outflow files.

- [ ] **Step 4: Commit changes**
  Run: `git add application/controllers/Gaji.php application/models/M_Gaji.php application/views/Backend/Gaji/ application/controllers/Pengeluaran.php application/models/M_Pengeluaran.php application/views/Backend/Pengeluaran/; git commit -m "refactor: align cash outflow components"`

---

### Task 7: Refactor Downstream Modules (Laporan, Tunggakan, StudentArea)

**Files:**
- Modify: `application/controllers/Laporan.php`, `application/models/M_Laporan.php`, `application/views/Backend/v_Laporan.php`
- Modify: `application/controllers/Tunggakan.php`, `application/views/Backend/Tunggakan/v_Tunggakan.php`
- Modify: `application/controllers/StudentArea.php`, `application/views/v_student_area.php`

**Interfaces:**
- Consumes: `current_school_year()`, `tagihan_siswa`, `pemasukan`, `pengeluaran`, `gaji`.
- Produces: Aligned daily ledger generation and active school year filtering on billings.

- [ ] **Step 1: Refactor Laporan Kas**
  Adjust cash queries in `M_Laporan.php` to sum total cash:
  - `kas_masuk`: SUM(`pemasukan.nominal_pemasukan`) + SUM(`tagihan_siswa.nominal_tagihan` where status = 'Lunas').
  - `kas_keluar`: SUM(`pengeluaran.nominal_pengeluaran`) + SUM(`gaji.jam` * `gaji.nominal_gaji`).

- [ ] **Step 2: Refactor Info Tunggakan**
  Filter outstanding student bills in `Tunggakan.php` to hide unpaid SPP bills from previous years.

- [ ] **Step 3: Refactor Student Area**
  Update Student Area controller and view (`v_student_area.php`) to query `tagihan_siswa`, hiding unpaid SPP where `tahun_ajaran < current_school_year()`.

- [ ] **Step 4: Run PHP linter**
  Verify all files compile successfully.

- [ ] **Step 5: Commit changes**
  Run: `git add application/controllers/Laporan.php application/models/M_Laporan.php application/views/Backend/v_Laporan.php application/controllers/Tunggakan.php application/views/Backend/Tunggakan/ application/controllers/StudentArea.php application/views/v_student_area.php; git commit -m "refactor: realign reports, tunggakan, and student dashboard"`

---

### Task 8: Integration and Verification Check

**Files:**
- Test: `http://localhost:8000/`

**Interfaces:**
- Consumes: Browser dev session
- Produces: Successful verified page flows.

- [ ] **Step 1: Check codebase syntax**
  Run a full recursive linter check:
  ```powershell
  Get-ChildItem -Path "c:\Angga\Projects\sim-sekolah\application" -Filter "*.php" -Recurse | Where-Object { $_.FullName -notmatch "third_party" } | ForEach-Object { php -l $_.FullName } | Where-Object { $_ -notmatch "No syntax errors" }
  ```
  Expected: Empty output (meaning no errors).

- [ ] **Step 2: Run verification navigations in browser**
  Use `browser_subagent` to login, browse through Beranda, Guru, Kelas, Siswa, Transaksi, Tagihan, Lainnya, Pengeluaran, Gaji, Tunggakan, and Laporan pages to verify no SQL queries or page rendering crash.

- [ ] **Step 3: Remove setup script**
  Run: `Remove-Item -Force seed_new_schema.php`

- [ ] **Step 4: Final commit**
  Run: `git add -A; git commit -m "chore: finalize database restructure and refactoring"`
