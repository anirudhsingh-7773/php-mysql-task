<?php
// checkSession.php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    header("Location: login.php?redirect=$currentPage");
    exit();
}
