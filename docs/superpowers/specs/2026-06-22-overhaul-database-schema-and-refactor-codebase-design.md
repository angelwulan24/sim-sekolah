# Spec: Reconstruct Database Schema and Overhaul Codebase Naming

## Objective
Update the school management database schema to implement proper foreign key relations, string primary keys (`nis_siswa` for students, `NUPTK` for teachers, `kode_tagihan` for fee types), and cleaner normalized relationships. Refactor the CodeIgniter application's controllers, models, and views to use these new table structures and column names while introducing active school year logic (July - June) and hiding past years' unpaid bills.

---

## 1. Scope of Work

### A. Database Overhaul
Drop all current database tables and re-initialize the schema with the following tables:
1.  **`users`**: `id_users` (PK), `email`, `password`, `nama_users`, `role`, `gambar`. (Dropped `telpon` and `active` fields).
2.  **`guru`**: `NUPTK` (PK), `nama_guru`, `jk_guru`, `agama_guru`, `bidang_studi`, `alamat_guru`, `status_guru`, `telp_guru`, `foto_guru`.
3.  **`kelas`**: `id_kelas` (PK), `nama_kelas`, `NUPTK` (FK to guru), `ket_kelas`.
4.  **`siswa`**: `nis_siswa` (PK), `nama_siswa`, `jk_siswa`, `agama_siswa`, `status_siswa`, `ortu_wali`, `tempat_lahirsiswa`, `tgl_lahirsiswa`, `alamat_ssiwa`, `telp_siswa`, `id_kelas` (FK to kelas), `foto_siswa`, `tgl_masuk`, `thn_ajaran`, `id_users` (FK to users).
5.  **`gaji`**: `id_gaji` (PK), `NUPTK` (FK to guru), `periode`, `jam`, `nominal_gaji`, `tgl_gaji`, `tanggal`.
6.  **`jenis_tagihan`**: `kode_tagihan` (PK), `nama_tagihan`, `nominal_tagihan`, `tenggat_waktu`, `tahun_ajaran`, `id_kelas` (FK to kelas). (Dropped `tipe` field).
7.  **`pemasukan`**: `id_pemasukan` (PK), `sekarang`, `tgl_pemasukan`, `ket_pemasukan`, `nominal_pemasukan`, `tanggal`.
8.  **`tagihan_siswa`**: `id_tagihan` (PK), `id_pemasukan` (FK to pemasukan, nullable), `nis_siswa` (FK to siswa), `kode_tagihan` (FK to jenis_tagihan), `status`, `tgl_pembayaran`.
9.  **`pengeluaran`**: `id_pengeluaran` (PK), `nominal_pengeluaran`, `sekarang`, `tgl_pengeluaran`, `ket_pengeluaran`, `tanggal`, `bukti`.
10. **`laporan`**: `id` (PK), `saldo_awal`, `kas_masuk`, `kas_keluar`, `tanggal`.

### B. Application Refactoring
1.  **Auth & Users**: Clean up queries to match `id_users`, `nama_users`, and remove `active`/`telpon` fields from validation and registration code.
2.  **Master Data Pages**: Rewrite controllers/models (`Guru.php`, `Kelas.php`, `Siswa.php`) and their view files to map input fields to the new columns. Make sure class assignment in `Siswa` saves the class's integer `id_kelas` rather than its name.
3.  **Financial Transactions**:
    *   **Inflow**: Update `Transaksi.php`, `Tagihan.php`, and `Lainnya.php` to target `jenis_tagihan`, `tagihan_siswa`, and `pemasukan` tables respectively.
    *   **Outflow**: Update `Gaji.php` and `Pengeluaran.php` to target `gaji` and `pengeluaran`.
4.  **School Year Helper**: Add `current_school_year()` in `system_helper.php` to calculate the active year based on date (July - June).
5.  **Unpaid Bill Visibility**: Update `StudentArea.php` and `Tunggakan.php` queries to filter out unpaid SPP bills where `tahun_ajaran` is less than `current_school_year()`.
6.  **Daily Cash Flow Reports**: Re-calculate reports in `M_Laporan.php` and `M_General.php` as:
    *   `kas_masuk` = SUM(`pemasukan.nominal_pemasukan`) + SUM(`tagihan_siswa.nominal_tagihan` WHERE status = 'Lunas') on date.
    *   `kas_keluar` = SUM(`pengeluaran.nominal_pengeluaran`) + SUM(`gaji.jam` * `gaji.nominal_gaji`) on date.

---

## 2. Risk Mitigation & Verification Plan

### A. Data Seeding Script
We will write a comprehensive PHP CLI seeder script `c:\Angga\Projects\sim-sekolah\seed_new_schema.php` that:
1. Re-imports the new `schema.sql`.
2. Inserts default admin user, teachers, classes, students, bills, and cash logs.
This guarantees we can verify every single page immediately after refactoring.

### B. Verification Strategy
1.  **Step-by-Step Linter**: Perform syntax checks on each component as it gets refactored.
2.  **Integration Testing**: Verify student dashboard displays only current/unpaid SPP, and admin dashboard accurately reflects daily balances.
