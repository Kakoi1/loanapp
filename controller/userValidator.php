<?php
require_once __DIR__ . '/..//vendor/autoload.php';
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
// include './model/users.php';


class Validator {

    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }
    public function validateRegistration( $name,
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
    $accType) {

        $errors = [];
        $blockDetails = $this->userModel->usedEmail($email);
        $name_regex = "/^([a-zA-Z' ]+)$/";
        $bnkNum="/^[0-9]{9,18}$/";
        $tinPattern = "/^[1-9]([0-9]{1,10}$)/";
      
        if (empty($accType)) {
            $errors['accType'] = "Account type is required.";
            
        }else if( $this->userModel->premiumAcc() >= 50){
            $errors['accType'] = "Premium Accounts is Full";
        }
        if (empty($name)) {
            $errors['name'] = "Name is required.";
        } else if (!preg_match($name_regex, $name)){ 
            $errors['name'] = "Invaild Name Format";
        }
        if (empty($address)) {
            $errors['address'] = "Address is required.";
        }
        if (empty($gender)) {
            $errors['gender'] = "Gender is required.";
        }
        if (empty($birthday)) {
            $errors['birthday'] = "Birthday is required.";
        }
        if (empty($age)) {
            $errors['age'] = "Age is required.";
        }else if ($age <= 17){ 
            $errors['age'] = "Your not old Enough to loan";
        }
        if (empty($email)) {
            $errors['email'] = "Email is required.";
        } elseif ($blockDetails && $blockDetails['acc_status'] === 'deactivate') {
            $rejReason = isset($blockDetails['rej_reason']) ? $blockDetails['rej_reason'] : 'No reason provided';
            $errors['email'] = "Email address is blocked. Reason: " . $rejReason;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email address is invalid.";
        } elseif ($blockDetails) {
            $errors['email'] = "Email is already used.";
        }

        if (empty($password)) {
            $errors['pass'] = "Password is required.";
        }else if (strlen($password) < 8) {
            $errors['pass'] = "Password Must have 8 characters";
        }
        if (empty($retype_password)) {
            $errors['repass'] = "Retype password is required.";
        }else if ($password != $retype_password) {
            $errors['repass'] = "Password Do not Match";
        }
        if (empty($bank_name)) {
            $errors['bank_name'] = "Bank name is required.";
        }
        if (empty($bank_account_number)) {
            $errors['bank_account_number'] = "Bank account number is required.";
        }else if (!preg_match($bnkNum, $bank_account_number)) {
            $errors['bank_account_number'] = "Bank account number is Invalid.";
        }
        if (empty($card_holder_name)) {
            $errors['card_holder_name'] = "Card holder name is required.";
        }else if (!preg_match($name_regex, $card_holder_name)){ 
            $errors['card_holder_name'] = "Invaild Card Holder Name.";
        }
        if (empty($tin_number)) {
            $errors['tin_number'] = "TIN number is required.";
        }else if (!preg_match($tinPattern, $tin_number)){ 
            $errors['tin_number'] = "Invaild tin number.";
        }
        if (empty($company_name)) {
            $errors['company_name'] = "Company name is required.";
        }
        if (empty($company_address)) {
            $errors['company_address'] = "Company address is required.";
        }
        if (empty($position)) {
            $errors['position'] = "Position is required.";
        }
        if (empty($monthly_earnings)) {
            $errors['monthly_earnings'] = "Monthly earnings are required.";
        }
        if (empty($proof_of_billing_file['name'])) {
            $errors['previewProofOfBilling'] = "Proof of billing is required.";
        }
        if (empty($valid_id_file['name'])) {
            $errors['validId'] = "Valid ID is required.";
        }
        if (empty($coe_file['name'])) {
            $errors['coe'] = "Certificate of Employment is required.";
        }

        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $phoneNumberUtil->parse($contact_number, 'PH');
            $isValidPhoneNumber = $phoneNumberUtil->isValidNumber($phoneNumber);
        } catch (NumberParseException $e) {
            $errors['contact_number'] = "Invalid User phone number format.";
            $isValidPhoneNumber = false;
        }
        
        try {
            $compaNumber = $phoneNumberUtil->parse($company_phone, 'PH');
            $isValidcompaNumber = $phoneNumberUtil->isValidNumber($compaNumber);
        } catch (NumberParseException $e) {
            $errors['company_phone'] = "Invalid Company phone number format.";
            $isValidcompaNumber = false;
        }
    
        return $errors; 
    }
    

public function loginValidate($email, $password){
    $errors = [];
    $blockDetails = $this->userModel->isEmailBlocked($email);

    if (empty($email)|| empty($password) ) {
    $errors[] = "All fields are required.";

}else{
    if($blockDetails){
        $errors[] = "Account is Blocked Due to: ".$blockDetails."";
    }
}
    return $errors;
}

public function loanValidate($loanAmount,$balance){
    $errors = [];

    if (empty($loanAmount) || !filter_var($loanAmount, FILTER_VALIDATE_FLOAT) || $loanAmount < 5000 || $loanAmount > 10000 || $loanAmount % 1000 != 0) {
        $errors[] = "Invalid loan amount. It must be between 5000 and 10000, and in multiples of 1000.";
    }

    if ($loanAmount > $balance) {
        echo "Not Enough Loan balance";
        exit;
    }

    return $errors;
}

public function withdrawValidate($userId,$savId,$amount,$balance){
    $errors = [];
    $count = $this->userModel->countWithdraw($userId);

    if ($amount < 500 || $amount > 5000) {
        echo "Withdrawal amount must be between 500 and 5000.";
        exit;
    }
    if ($count >= 5) {
        echo "You can only make 5 withdrawals per day.";
        exit;
    }
    if ($amount > $balance) {
        echo "Not Enough savigns balance";
        exit;
    }

    return $errors;
}
public function depositValidate($userId,$savId,$amount,$balance){
    $errors = [];

    if ($amount < 100 || $amount > 1000) {
        echo "Deposit amount must be between 100 and 1000.";
        exit;
    }
  
    if ($balance + $amount > 100000) {
        echo "Cannot deposit, balance exceeds 100,000.";
        exit;
    }

    return $errors;
}

}
?>
