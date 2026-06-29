# Auto Assign SPP & Bills for New Students Design Spec

Design specification for automatically assigning active academic year bills (with month-based filtering for SPP) when a new student is registered manually or imported via Excel.

## Goal

- Automatically assign existing bills (`jenis_tagihan`) of the student's academic year and class (or all classes) to the new student upon registration.
- For SPP (monthly school fee) bills, skip any month that has already passed before the student's admission date (`tgl_masuk`).
- If `tgl_masuk` is empty/invalid, assign all SPP bills.
- Apply this logic to both manual registration (`Siswa::Simpan`) and Excel imports (`Siswa::import`).

## Proposed Changes

### Siswa Controller

Modify `application/controllers/Siswa.php`:

- Correct array keys in `import()` to match the database columns (`alamat_ssiwa` instead of `alamat_siswa`, `thn_ajaran` instead of `tahun_ajaran`).
- Add private helper method `_assign_existing_bills($nis_siswa, $id_kelas, $thn_ajaran, $tgl_masuk)`.
- Invoke `_assign_existing_bills` after student insertion in `Simpan()` and `import()`.

## Verification Plan

### Automated Tests
N/A

### Manual Verification
1. Create a few SPP bills for class X in academic year `2025/2026` with due dates in July 2025 (`2025-07-14`), August 2025 (`2025-08-14`), and September 2025 (`2025-09-14`).
2. Register a new student for class X and academic year `2025/2026` with entry date `2025-08-10`.
3. Check the database `tagihan_siswa` table to verify they receive the August and September SPP bills, but NOT the July SPP bill.
