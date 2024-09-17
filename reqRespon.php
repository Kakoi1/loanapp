<?php

include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $email = $_POST['email'];
    $action = $_POST['action']; 
    $reason = $_POST['reason'];

    if($action == "approve"){
        $response = "active";
    }else if($action == "reject"){
        $response = "rejected";
    }elseif ($action == "deact") {
        $response = "deactivated";
    }
    else{
        $response = "activate";
    }

    $registerController->adminResponse($userId, $response, $reason,$email);



    exit;
}

include './view/dashboard.php';
?>
