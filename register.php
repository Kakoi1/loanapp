<?php

include 'config/database.php';
include 'controller/accountControl.php';

$registerController = new RegisterController($db);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $password = $_POST['pass'];
    $retype_password = $_POST['repass'];
    $bank_name = $_POST['bank_name'];
    $bank_account_number = $_POST['bank_account_number'];
    $card_holder_name = $_POST['card_holder_name'];
    $tin_number = $_POST['tin_number'];
    $company_name = $_POST['company_name'];
    $company_address = $_POST['company_address'];
    $company_phone = $_POST['company_phone'];
    $position = $_POST['position'];
    $monthly_earnings = $_POST['monthly_earnings'];

    $proof_of_billing_file = $_FILES['proof_of_billing'];
    $valid_id_file = $_FILES['valid_id'];
    $coe_file = $_FILES['coe'];
    $accType = $_POST['accType'];
  

    $registerController->registerUser(
        $name,
        $address,
        $gender,
        $birthday,
        $age,
        $email,
        $contact_number,
        $password,
        $retype_password,
        $bank_name,
        $bank_account_number,
        $card_holder_name,
        $tin_number,
        $company_name,
        $company_address,
        $company_phone,
        $position,
        $monthly_earnings,
        $proof_of_billing_file,
        $valid_id_file, 
        $coe_file,
        $accType
    );
  

    exit;
}

include 'view/registerForm.php';
?>