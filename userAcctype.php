<?php 
include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['id'];
    $acctype = $_POST['account_type'];

    $registerController->accType($userId,$acctype);
}

?>