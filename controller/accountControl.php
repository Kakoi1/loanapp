<?php
session_start();
include './model/users.php';
include 'userValidator.php';
class RegisterController {
    private $userModel;
    private $validates;

    public function __construct($db) {
        $this->userModel = new User($db);
        $this->validates = new Validator($db);
    }

    public function registerUser(
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
    ){

        $errors = $this->validates->validateRegistration( $name,
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
        $accType);

        if (count($errors) !== 0) { 
            echo json_encode(["errors" => $errors]);
    }else{
        if ($this->userModel->register(  $name,
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
        )) {
            echo json_encode(["message" => "Registration successful"]);
        } else {
            echo json_encode(["error" => "Registration failed"]);
        }
    }
    }
    public function loginUser($email, $password) {

        $errors = $this->validates->loginValidate($email, $password);
        $login = $this->userModel->login($email, $password);

    if (count($errors) === 0){
          
            if ($login) {
                $accStatus = $login['acc_status'];
                $rejectionReason = $login['rej_reason'];

                if($accStatus == "rejected"){
                    echo "Account Status: " . $accStatus . "<br>";
                    echo "Rejection Reason: " . $rejectionReason . "<br>";
                }else if($accStatus == 'pending'){
                echo "Account is still Pending for approval";
                }else if($accStatus == "deactivate"){
                    echo "account is disabled due to: ".$rejectionReason;
                }
                else{

                if($login['acc_type'] == "admin"){
                    echo "Admin Login successful!";
                }else{
                    echo "Login successful!";
                }
                $_SESSION['userEmail'] =  $login['acc_email'];
                $_SESSION['userId'] =  $login['acc_id'];
            }
            } else {

            echo "Login failed!";

    }
    }else{
        foreach ($errors as $error) {
            echo $error . "<br>";
    }
    }   
    }

public function adminResponse($userId, $response, $reason,$email) {
    $adminResp = $this->userModel->adminRespon($userId, $response,$reason);
    if ($adminResp) {
        if($adminResp == 'rejected'){
            echo "Account Rejected";
            $this->userModel->sendEmail($email, 'Youre Registration is Rejected Due to: '.$reason.'', 'User Rejection');
        }else if($adminResp == 'active'){

        $this->userModel->sendEmail($email, 'Youre Registration has been Approved', 'User Approval');

        echo "Account Approved";
        $this->userModel->createLoan($userId);
        }else if($adminResp == 'activate'){
            echo "Account Activated";

        }else if($adminResp == 'deactivated'){
                echo "Account Deactivated";
        }
         else{
            echo 'ok';
         }
    }
     else {
        echo "failed! ";
    }
}

public function loanReq($reqId,$loanId, $loanAmont, $loanTerm,$balance) {
    
        $errors = $this->validates->loanValidate($loanAmont,$balance);
        if (count($errors) === 0){
            if($this->userModel->requestLoan($reqId,$loanAmont,$loanTerm,$loanId)){
                echo "loan request Submitted";
            }else{
                echo "loan request Failed";
            }
        }else{
            foreach ($errors as $error) {
                echo $error ;
        }
        }
}

public function loanResponse($reqId,$userId, $response, $reason, $lonId, $amount, $term,$email) {
    $adminResp = $this->userModel->loanResp($reqId, $response,$reason, $amount,$lonId);
    if ($adminResp) {
        if($adminResp == 'rejected'){
            echo "Loan Rejected";
            $this->userModel->sendEmail($email, 'Youre Loan Request has been Rejected Due to: '.$reason.'', 'Loan Rejection');
        }else if($adminResp == 'approved'){

            if($this->userModel->createRepaymentSchedule($userId,$reqId,$lonId, $amount, $term)){

                $this->userModel->loanMinus($lonId,$amount);
            $this->userModel->sendEmail($email, 'Youre Loan Request Has Been Approved', 'Loan Approval');
        echo "Loan Approved";

        }else{
            echo "error";
        }
        }
         else{
            echo 'ok';
         }
    }
     else {
        echo "failed! ";
    }
}

public function userPay($selectedRepayments,$amDue,$udi,$loanId,$odue,$reqId){
    $res = $this->userModel->userPayment($selectedRepayments,$amDue,$udi,$loanId,$odue,$reqId);

        if($res){

            echo 'Payment Success'.$res;
        }else{
            echo $res;
        }

    
}
public function reqWithdraw($userId,$savId,$amount,$balance) {

    $errors = $this->validates->withdrawValidate($userId,$savId,$amount,$balance);
    if (count($errors) === 0){

        if($this->userModel->reqWithdraw($userId,$savId,$amount)){
            echo "Withdrawal Request Sent";
        }else{
            echo " Request Fail";
        }

    }else{
        foreach ($errors as $error) {
            echo $error ;
    }
    }
}
public function withRepon($Id,$reponse,$reason,$userId,$savId,$amount,$email) {
   $respon = $this->userModel->withdrawRespon($Id, $reponse, $reason);

    if($respon == 'approve'){
        echo 'withdrawal Approved';
    $this->userModel-> savingsTransac($userId,$amount,'completed','withdrawal',$reason);
    $this->userModel->sendEmail($email, 'Youre Withdrawal has been Approve', 'Withdrawal Approval');
    $this->userModel->withdrawMe($savId,$amount);

    }else if($respon == 'reject'){
        echo 'withdrawal Rejected';
        $this->userModel-> savingsTransac($userId,$amount,'rejected','withdrawal',$reason);
        $this->userModel->sendEmail($email, 'Youre Withdrawal has been Rejected Due to: '.$reason.'', 'Withdrawal Rejection');
    }else {
        echo "Failed";
    }
}

public function depositer($userId,$savId,$amount,$balance) {

    $errors = $this->validates->depositValidate($userId,$savId,$amount,$balance);
    if (count($errors) === 0){

        if($this->userModel->depositMe($savId,$amount)){
            $this->userModel->savingsTransac($userId,$amount,'completed','deposit',null);
            echo "Deposit Success";
        }else{
            echo " Deposit Fail";
        }

    }else{
        foreach ($errors as $error) {
            echo $error ;
    }
    }
}
public function accType($userId,$type) {

    $user = $this->userModel->acctypeChange($userId, $type);

        if($user){

            if($this->userModel->checkSave($userId)){
                echo "User Acccount type change";
            }else{
                echo "User Acccount type change";
            }

            
        }else{
            echo "Fail";
        }

    
}

}
?>
