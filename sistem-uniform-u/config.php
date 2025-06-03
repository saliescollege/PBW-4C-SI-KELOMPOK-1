<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$projectFolder = '/PBW-4C-SI-KELOMPOK-1/sistem-uniform-u';
$base_url = $protocol . '://' . $host . $projectFolder . '/';
?>