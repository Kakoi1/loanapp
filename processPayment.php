<?php
require_once 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedRepayments = $_POST['repayments'];
    $amDue = $_POST['total_due'];
    $udi = $_POST['userId'];
    $loanId = $_POST['lonId'];
    $odue = $_POST['origDue'];
    $reqId = $_POST['reqId'];

    $registerController->userPay($selectedRepayments,$amDue,$udi,$loanId,$odue,$reqId);

    exit;
}
?>
