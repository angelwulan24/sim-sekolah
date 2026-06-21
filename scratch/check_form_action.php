<?php
// Mock server vars to request Laporan page
$_SERVER['REQUEST_URI'] = '/sim-sekolah/Laporan';
$_SERVER['PATH_INFO'] = '/Laporan';
$_SERVER['SCRIPT_NAME'] = '/sim-sekolah/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Mock argv/argc for CLI routing
$argv = array('index.php', 'Laporan');
$argc = 2;
$_SERVER['argv'] = $argv;
$_SERVER['argc'] = $argc;
$GLOBALS['argv'] = $argv;
$GLOBALS['argc'] = $argc;

ob_start();
require 'index.php';
$html = ob_get_clean();

// Extract the form tag with id="form-print"
if (preg_match('/<form[^>]+id\s*=\s*["\']form-print["\'][^>]*>/i', $html, $matches)) {
    echo "Found form tag:\n" . $matches[0] . "\n";
} else {
    echo "form tag with id='form-print' not found!\n";
    // Let's print any form tags
    if (preg_match_all('/<form[^>]*>/i', $html, $matches_all)) {
        echo "Found other form tags:\n";
        print_r($matches_all[0]);
    } else {
        echo "No form tags found at all!\n";
    }
}
