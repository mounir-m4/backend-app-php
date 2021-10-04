<?php
// Welcome to members page 
// here you can add delete update a user 
session_start();
$pageTitle = "title";
if (isset($_SESSION['Username'])) {
    // Include The Important Files
    include 'init.php';
    $pdo = pdoConnectMysql();
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if ($do == "manage") {
    } else if ($do == 'add') {
    } else if ($do == 'insert') {
    } else if ($do == 'edit') {
    } elseif ($do == 'update') {
    } else if ($do == 'activate') {
    } else if ($do == 'delete') {
    }
    include $template . 'footer.php';
} else {
    header('Location: index.php');
    exit;
}
