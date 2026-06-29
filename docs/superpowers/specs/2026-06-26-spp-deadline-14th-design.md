# SPP Deadline 14th Design Spec

Design specification for setting the SPP due date (tenggat waktu) to the 14th day of the corresponding month and year.

## Goal

Ensure that when school fees (SPP) are saved, the `tenggat_waktu` database column for each monthly SPP record is set to the 14th day of that specific month and year, rather than the placeholder `'Setiap Bulan'`.

## Proposed Changes

### Transaksi Controller

Modify `Simpan()` in [Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/controllers/Transaksi.php):

- Extract the year and month index for each generated month of the SPP.
- Calculate the correct calendar year depending on the academic year format `YYYY/YYYY` and month.
- Format the due date as `YYYY-MM-14` and assign it to the `tenggat_waktu` field.

## Verification Plan

### Automated Tests
N/A

### Manual Verification
1. Insert a new SPP tagihan for an academic year (e.g., `2025/2026`).
2. Verify that the generated database rows in `jenis_tagihan` have the correct `tenggat_waktu` values like `2025-07-14` for Juli and `2026-01-14` for Januari.
