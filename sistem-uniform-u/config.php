<?php
// Mendapatkan protocol (http/https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Mendapatkan host (localhost atau domain)
$host = $_SERVER['HTTP_HOST'];

// Ubah sesuai nama folder project di htdocs
$projectFolder = '/sistem-uniform-u';

// Gabungkan jadi base URL
$base_url = $protocol . '://' . $host . $projectFolder . '/';
?>