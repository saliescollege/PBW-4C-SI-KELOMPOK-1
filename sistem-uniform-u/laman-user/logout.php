<?php
session_start();
include '../koneksi.php';
include '../config.php';

session_destroy();
header("Location: ../laman-masuk/login.php");
exit();