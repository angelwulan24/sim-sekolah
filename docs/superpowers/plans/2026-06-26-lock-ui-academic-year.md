# Lock UI Academic Year Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Lock the academic year input field in the UI to prevent user modification and automatically set the current academic year as the default value.

**Architecture:** We will set the `tahun_ajaran` input element to `readonly` in `v_Siswa.php` and `v_Transaksi.php`. In their corresponding `Tambah()` Javascript handlers, we will explicitly re-populate the field with the result of the `current_school_year()` helper after form reset.

**Tech Stack:** HTML, Javascript, PHP, CodeIgniter 3

## Global Constraints

- Set `readonly` attribute on the HTML inputs, do not use `disabled` (as disabled inputs are not submitted by form POST).
- Re-populate using `<?=current_school_year()?>` dynamically.

---

### Task 1: Update Siswa View File

**Files:**
- Modify: `application/views/Backend/Siswa/v_Siswa.php`

**Interfaces:**
- Consumes: PHP `current_school_year()` helper
- Produces: Locked input field in frontend UI

- [ ] **Step 1: Set readonly and default value on Siswa input**

Open [v_Siswa.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Siswa/v_Siswa.php) and look at line 148. Change it to:
```html
                    <div><input type="text" required="" readonly="" value="<?=current_school_year()?>" placeholder="Contoh: 2023/2024" autocomplete="off" name="tahun_ajaran" class="form-control"></div>
```

- [ ] **Step 2: Update Tambah() Javascript function in v_Siswa.php**

Locate `Tambah()` in [v_Siswa.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Siswa/v_Siswa.php) (around line 417). Add code to set the value of `tahun_ajaran` right after form reset:
```javascript
	function Tambah(){
		label = 'simpan';
		$('#form')[0].reset();
        $('[name="tahun_ajaran"]').val('<?=current_school_year()?>');
...
```

---

### Task 2: Update Transaksi View File

**Files:**
- Modify: `application/views/Backend/Transaksi/v_Transaksi.php`

**Interfaces:**
- Consumes: PHP `current_school_year()` helper
- Produces: Locked input field in frontend UI

- [ ] **Step 1: Set readonly and default value on Transaksi input**

Open [v_Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Transaksi/v_Transaksi.php) and look at line 58. Change it to:
```html
                    <div><input type="text" readonly="" value="<?=current_school_year()?>" required="" placeholder="Cth: 2023/2024" autocomplete="off" name="tahun_ajaran" class="form-control"></div>
```

- [ ] **Step 2: Update Tambah() Javascript function in v_Transaksi.php**

Locate `Tambah()` in [v_Transaksi.php](file:///c:/Angga/Projects/sim-sekolah/application/views/Backend/Transaksi/v_Transaksi.php) (around line 313). Add code to set the value of `tahun_ajaran` right after form reset:
```javascript
    function Tambah(){
        label = 'simpan';
        $('#form')[0].reset();
        $('[name="tahun_ajaran"]').val('<?=current_school_year()?>');
...
```

---

### Task 3: Commit Changes

**Files:**
- Modify: N/A

- [ ] **Step 1: Commit work**

Run commands to commit:
```bash
git add application/views/Backend/Siswa/v_Siswa.php application/views/Backend/Transaksi/v_Transaksi.php
git commit -m "feat: lock academic year input to current school year in UI"
```
