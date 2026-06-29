# Dropdown Academic Year in Transaksi Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** In the Transaksi view Add modal, replace the academic year text input with a dropdown selection displaying 5 choices starting from the current academic year to 5 years into the future.

**Architecture:** We will modify `v_Transaksi.php` to replace the `tahun_ajaran` text input with a select dropdown populated via a dynamic loop. In the `Tambah()` Javascript function, we will reset the value of the dropdown to the active academic year.

**Tech Stack:** HTML, Javascript, PHP, CodeIgniter 3

## Global Constraints

- Generate exactly 5 options starting from the current year of `current_school_year()`.
- Use `<select name="tahun_ajaran">` to keep form POST compatibility.

---

### Task 1: Update Transaksi View File

**Files:**
- Modify: `application/views/Backend/Transaksi/v_Transaksi.php`

**Interfaces:**
- Consumes: PHP `current_school_year()` helper
- Produces: Dropdown selection in Transaksi Add modal

- [ ] **Step 1: Replace input with select in v_Transaksi.php**

Open [v_Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Transaksi/v_Transaksi.php) and look at lines 57-61. Replace the text input with a select element populated by a loop.

Use the following implementation:
```html
                <div class="form-group">
                    <label class="control-label"> Tahun Ajaran</label>
                    <div>
                        <select name="tahun_ajaran" required="" class="form-control">
                            <?php
                            $current_sy = current_school_year();
                            $years = explode('/', $current_sy);
                            $start_year = (int)$years[0];
                            for ($i = 0; $i < 5; $i++) {
                                $y = $start_year + $i;
                                $val = $y . '/' . ($y + 1);
                                echo '<option value="' . $val . '">' . $val . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
```

- [ ] **Step 2: Ensure default value is set in Tambah() function**

Ensure the `Tambah()` Javascript function in [v_Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Transaksi/v_Transaksi.php) correctly sets the default value of the `tahun_ajaran` select element after form reset:
```javascript
        $('[name="tahun_ajaran"]').val('<?=current_school_year()?>');
```
*(This is already present in our previous change, but double check to verify it is intact).*

- [ ] **Step 3: Commit the changes**

Run commands to commit:
```bash
git add application/views/Backend/Transaksi/v_Transaksi.php
git commit -m "feat: use select dropdown for academic year in Transaksi form"
```

- [ ] **Step 4: Verify manually**

Open the Add Tagihan modal and verify the dropdown contains 5 options starting from the current academic year, and that selecting different options works correctly.
