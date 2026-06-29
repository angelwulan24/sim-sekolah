# Delete Previous Alumni on Promotion Design Spec

Design specification for automatically deleting previous alumni records (including their user accounts and photo files) when a new batch of 6th-grade students is promoted to alumni (graduated).

## Goal

- When promoting a class to "lulus" (Alumni status) via the class promotion process, automatically delete all existing alumni from the database.
- Clean up associated assets for the deleted alumni: delete their profile photos from `assets/images/siswa/` and their user accounts from the `users` table.

## Proposed Changes

### Kelas Controller

Modify `ProsesKenaikan()` in [Kelas.php](file:///c:/Angga/Projects/sim-sekolah/application/controllers/Kelas.php):

- Check if the target class is `'lulus'`.
- If yes, query all current alumni in the database.
- For each alumnus:
  - Delete their photo file if it exists.
  - Delete their associated `users` table record using `id_users`.
- Delete the alumni records from `siswa` table.
- Proceed with updating the current class's students to `'Alumni'` status.

## Verification Plan

### Automated Tests
N/A

### Manual Verification
1. Create a dummy alumnus student in the database (status = 'Alumni').
2. Upload/mock a dummy photo file for this alumnus, and verify a user account is created.
3. Perform a class promotion of a class to 'lulus'.
4. Verify that the previous dummy alumnus student, their photo, and their user account are deleted.
5. Verify that the new batch of promoted students successfully receives 'Alumni' status.
