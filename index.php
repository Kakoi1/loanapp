<?php

include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $registerController->loginUser($email, $password);

    exit;
}

// Display login form by default
include 'view/login.php';

?>
