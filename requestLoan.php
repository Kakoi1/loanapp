<?php

include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['userID'], $_POST['loanID'], $_POST['loan_amount'], $_POST['loan_term'])) {
    $userid = $_POST['userID'];
    $loanId = $_POST['loanID'];
    $loanAmont = $_POST['loan_amount'];
    $loanTerm = $_POST['loan_term'];
    $balance = $_POST['balance'];

    $registerController->loanReq($userid, $loanId, $loanAmont, $loanTerm,$balance);
    }
    else{
        $userId = $_POST['userId'];
        $reqId = $_POST['reqId'];
        $action = $_POST['action']; 
        $reason = $_POST['reason'];
        $lonId = $_POST['loanId'];
        $amount = $_POST['amount'];
        $email = $_POST['email'];
        $term = $_POST['term'];

        if($action == "approve"){
            $response = "approved";
        }else if($action == "reject"){
            $response = "rejected";
        }
        $registerController->loanResponse($reqId,$userId, $response, $reason, $lonId, $amount, $term,$email);
    }
    exit;
}

// include_once 'view/userDash.php';