<?php

include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])){
        $action = $_POST['action'];
    if( $action == 'withdraw'){
        $userId = $_POST['userID'];
        $savId = $_POST['saveID'];
        $amount = $_POST['amount'];
        $balance = $_POST['balance'];
    
        $registerController->reqWithdraw($userId,$savId,$amount,$balance);
    }else if($action == 'deposit'){
        $balance = $_POST['balance'];
        $userId = $_POST['userID'];
        $savId = $_POST['saveID'];
        $amount = $_POST['amount'];

        $registerController->depositer($userId,$savId,$amount,$balance);
    }
  
}
else{
    $userId = $_POST['userId'];
    $response = $_POST['response'];
    $savId = $_POST['savId'];
    $reason = $_POST['reason'];
    $witId = $_POST['Id'];
    $amount = $_POST['amount'];
    $email = $_POST['email'];


    $registerController->withRepon($witId,$response,$reason,$userId,$savId,$amount,$email);

}
    exit;
}