# Lock UI Academic Year Design Spec

Design specification for locking the academic year input in the UI to prevent user modification and automatically set the current academic year as the default value.

## Goal

- Set the `tahun_ajaran` input field to `readonly` in the "Add Student" and "Add Bill Type" modals.
- Automatically populate this input field with the current academic year using `current_school_year()`.
- Ensure that resetting the form (e.g., when clicking "Add" again) restores the default academic year value.

## Proposed Changes

### Views

1. Modify `application/views/Backend/Siswa/v_Siswa.php`:
   - Set the `tahun_ajaran` input field to `readonly` and set its default value to `<?=current_school_year()?>`.
   - Update the Javascript `Tambah()` function to re-populate the `tahun_ajaran` input field with `<?=current_school_year()?>` after form reset.

2. Modify `application/views/Backend/Transaksi/v_Transaksi.php`:
   - Set the `tahun_ajaran` input field to `readonly` and set its default value to `<?=current_school_year()?>`.
   - Update the Javascript `Tambah()` function to re-populate the `tahun_ajaran` input field with `<?=current_school_year()?>` after form reset.

## Verification Plan

### Automated Tests
N/A

### Manual Verification
1. Open the "Tambah Siswa" modal and verify the "Tahun Ajaran" field is pre-populated with the current school year and is read-only.
2. Open the "Tambah Jenis Tagihan" modal and verify the "Tahun Ajaran" field is pre-populated with the current school year and is read-only.
