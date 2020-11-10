<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header('Content-Type: text/html; charset=utf-8');

    if (isset($_POST['source']) && !empty($_POST['source'])) {
        require_once 'parse.php';
        header("Location: ./?parse=true");
        exit;
    }