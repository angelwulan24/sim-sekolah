# Spec: Refactor Dead Code and Sync Database Foreign Keys

## Objective
Refactor and clean up all legacy/unused controllers, models, and views from the CodeIgniter application that were superseded by the consolidated `tagihan` billing system. Synchronize `schema.sql` to represent the active MariaDB database schema, including type alignments and foreign key relationships.

---

## 1. Scope of Work

### A. Code Deletion & Cleanup
The following files are obsolete and will be deleted:

*   **Controllers** (`application/controllers/`):
    *   `Baju.php`
    *   `Buku.php`
    *   `MigrateBuku.php`
    *   `Pendaftaran.php`
    *   `Piutang.php`
    *   `Seeder.php`
    *   `StudentAuthGen.php`
    *   `SyncAccounts.php`
    *   `Tanggal.php`
    *   `TestWa.php`
    *   `Ujian.php`

*   **Models** (`application/models/`):
    *   `M_Baju.php`
    *   `M_Buku.php`
    *   `M_Pendaftaran.php`
    *   `M_Tanggal.php`

*   **Views** (`application/views/`):
    *   `Backend/Baju/` (Directory)
    *   `Backend/Buku/` (Directory)
    *   `Backend/Pendaftaran/` (Directory)
    *   `Backend/Tanggal/` (Directory)
    *   `Backend/Ujian/` (Directory)
    *   `v_Menu_1.php`
    *   `v_VerifyOtp.php`
    *   `v_LoginWa.php`

### B. Database Schema Revisions (`schema.sql`)
The database definition file `schema.sql` will be updated to match the active schema:
1.  **Column Types**: Update foreign key columns (`gaji.id_guru` and `siswa.kelas`) from `tinyint(4)` to `int(11)` to match their parent table primary keys (`guru.id` and `kelas.id`).
2.  **Constraints**: Add the three correct foreign key definitions in their respective table structures:
    *   `fk_gaji_guru` (`gaji.id_guru` references `guru.id`)
    *   `fk_siswa_kelas` (`siswa.kelas` references `kelas.id`)
    *   `fk_tagihan_siswa` (`tagihan.id_siswa` references `siswa.id`)
3.  **Indices**: Add index declarations supporting these foreign keys.

---

## 2. Risk Mitigation & Verification Plan

### A. Autoload and Routing Check
*   Ensure none of the deleted controllers/models are configured to automatically load in CodeIgniter's `config/autoload.php`.
*   Ensure none of the deleted files are configured in custom routes in `config/routes.php`.

### B. Verification Steps
1.  **PHP Syntax/Linting**:
    Run a linter check across all remaining controllers and models to verify that no broken class calls or imports exist.
    ```bash
    # Command to run in project directory
    find application/ -name "*.php" -exec php -l {} \;
    ```
2.  **Runtime Validation**:
    Log in to the application and walk through the main flows (Data Master, Kas Masuk, Kas Keluar, Info Tunggakan, Laporan, Student Area) using a browser subagent to ensure everything functions normally.
3.  **Schema Verification**:
    Ensure that the updated `schema.sql` can be successfully validated by a SQL linter or database parser.
