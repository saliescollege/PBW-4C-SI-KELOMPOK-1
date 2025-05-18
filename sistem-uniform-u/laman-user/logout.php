<?php
session_start();
session_destroy();
header("Location: laman-masuk/login.php");
exit();
