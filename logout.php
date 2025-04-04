<?php
session_start();
session_destroy();
header("Location: index.php"); // To redirect to login page
exit;
?>