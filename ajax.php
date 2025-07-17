<?php
ob_start();
include 'admin_class.php';

$action = $_GET['action'] ?? '';
$crud = new Action();

switch ($action) {
    case 'login':
        echo $crud->login();
        break;

    case 'logout':
        echo $crud->logout();
        break;

    default:
        echo 'Invalid action';
}

ob_end_flush();
?>

