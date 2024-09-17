<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once __DIR__ . '/..//vendor/autoload.php';
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register(
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
    ) {
        $salt = "codeflix";
        $hashedPassword = sha1($password . $salt);
        $proof_of_billing_content = file_get_contents($proof_of_billing_file['tmp_name']);
        $valid_id_content = file_get_contents($valid_id_file['tmp_name']);
        $coe_content = file_get_contents($coe_file['tmp_name']);

        $query = "INSERT INTO `accounts` (
            `acc_type`,
            `acc_name`,
            `acc_address`,
            `acc_gender`,
            `acc_dob`,
            `acc_age`,
            `acc_email`,
            `acc_pnum`,
            `acc_password`,
            `bnk_name`,
            `bnk_accnum`,
            `bnk_holder`,
            `acc_tin`,
            `com_name`,
            `com_add`,
            `com_pnum`,
            `com_pos`,
            `com_earnig`,
            `acc_billing`,
            `acc_valid_id`,
            `acc_coe`,
            `acc_status`,
            `rej_reason`
        ) VALUES (
            :acc_type,
            :acc_name,
            :acc_address,
            :acc_gender,
            :acc_dob,
            :acc_age,
            :acc_email,
            :acc_pnum,
            :acc_password,
            :bnk_name,
            :bnk_accnum,
            :bnk_holder,
            :acc_tin,
            :com_name,
            :com_add,
            :com_pnum,
            :com_pos,
            :com_earning,
            :proof_of_billing,
            :valid_id,
            :coe,
            'pending',
            ''

        )";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acc_type', $accType);
        $stmt->bindParam(':acc_name', $name);
        $stmt->bindParam(':acc_address', $address);
        $stmt->bindParam(':acc_gender', $gender);
        $stmt->bindParam(':acc_dob', $birthday);
        $stmt->bindParam(':acc_age', $age);
        $stmt->bindParam(':acc_email', $email);
        $stmt->bindParam(':acc_pnum', $contact_number);
        $stmt->bindParam(':acc_password', $hashedPassword);
        $stmt->bindParam(':bnk_name', $bank_name);
        $stmt->bindParam(':bnk_accnum', $bank_account_number);
        $stmt->bindParam(':bnk_holder', $card_holder_name);
        $stmt->bindParam(':acc_tin', $tin_number);
        $stmt->bindParam(':com_name', $company_name);
        $stmt->bindParam(':com_add', $company_address);
        $stmt->bindParam(':com_pnum', $company_phone);
        $stmt->bindParam(':com_pos', $position);
        $stmt->bindParam(':com_earning', $monthly_earnings);
        $stmt->bindParam(':proof_of_billing', $proof_of_billing_content, PDO::PARAM_LOB);
        $stmt->bindParam(':valid_id', $valid_id_content, PDO::PARAM_LOB);
        $stmt->bindParam(':coe', $coe_content, PDO::PARAM_LOB);

    
        return $stmt->execute();
    }
    

    public function login($email, $password) {

        $salt = "codeflix";
        $hashedPassword = sha1($password . $salt);
        $query = "SELECT * FROM accounts WHERE acc_email = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $hashedPassword == $user['acc_password']) {
            return $user;
        }
        return null;
    }

    public function isEmailBlocked($email) {
        $query = "SELECT blk_reason FROM blockemail WHERE blk_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
       if($row){
        return  $row['blk_reason'];
       }else{
        return false;
       }
    }
    public function usedEmail($email) {
        $query = "SELECT * FROM accounts WHERE acc_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    public function premiumAcc() {
        $query = "SELECT COUNT(*) AS premUser FROM accounts WHERE acc_type = 'premium'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row && isset($row['premUser'])) {
            return $row['premUser']; // Return the count of premium users
        } else {
            return 0; // No premium users found, return 0
        }
    }
    
    public function adminRespon($userId, $response,$reason) {
        if ($response === 'rejected') {
            $query = "UPDATE `accounts` SET `acc_status` = :res, `rej_reason` = :reason, `rejection_date` = NOW() WHERE `acc_id` = :uid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':res', $response);
            $stmt->bindParam(':reason', $reason);
        }else if ($response === 'deactivated') {
            $query = "UPDATE `accounts` SET `acc_status` = :res, `rej_reason` = :reason, `rejection_date` = NOW() WHERE `acc_id` = :uid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':res', $response);
            $stmt->bindParam(':reason', $reason);
        }
         else if($response === 'approve'){
            $query = "UPDATE `accounts` SET `acc_status` = :res, `rejection_date` = NULL WHERE `acc_id` = :uid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':res', $response);
        } else{
            $query = "UPDATE `accounts` SET `acc_status` = 'active', `rejection_date` = NULL WHERE `acc_id` = :uid";
            $stmt = $this->conn->prepare($query);
            // $stmt->bindParam(':res', $response);
        }
        $stmt->bindParam(':uid', $userId);
        if($stmt->execute()) {

            $query = "SELECT * FROM `accounts` WHERE acc_id = :lid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lid', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($response == 'active'){
            if($user['acc_type'] == 'premium'){
                $this->insertSaving($userId);
                return $response;
            }else{
                return $response;
            }
        }else{
            return $response;
        }
            
           
        } else {
            return 0;
        }
    }

    // public function userStatus($email) {
    //     $query = "SELECT acc_status, rej_reason FROM `accounts` WHERE acc_email = :uid";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(':uid', $email); 
    //     if($stmt->execute()) {
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         if ($result) {
    //             return $result; // Return the acc_status and rejectionreason
    //         } else {
    //             return null; // No record found
    //         }
    //     } else {
    //         return null; // Query execution failed
    //     }
    // }

    public function createLoan($userId){

        $query = "INSERT INTO `loans`( `acc_id`, `loan_amount`, `loan_term`) VALUES 
        (:uid,10000,12)";

         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':uid', $userId);
        $stmt->execute();

    }

    public function requestLoan($userId,$requestedAmount,$loanTerm,$loanId){

     $sql = "INSERT INTO loan_requests (user_id, loan_id, requested_amount, loan_term, req_status) 
            VALUES (:user_id,:lon_id, :requested_amount, :loan_term, 'pending')";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':lon_id', $loanId);
    $stmt->bindParam(':requested_amount', $requestedAmount);
    $stmt->bindParam(':loan_term', $loanTerm);
    if($stmt->execute()) {

        $query = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status) 
        VALUES (:acc_id, 'loan', 'request', :amount, 'pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acc_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $requestedAmount);
        $stmt->execute();

        return 1; 
    } else {
        return 0; 
    }

    }

    public function loanResp($reqId,$response,$reason,$amount,$lonId) {
        if ($response === 'rejected') {
            $query = "UPDATE `loan_requests` SET `req_status` = :res, `admin_reason` = :reason WHERE `request_id` = :reqid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':res', $response);
            $stmt->bindParam(':reason', $reason);
            $transac = 'rejected';
        }
         else if($response === 'approved'){
            $query = "UPDATE `loan_requests` SET `req_status` = :res WHERE `request_id` = :reqid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':res', $response);
            $transac = 'completed';
        } 
        
        $stmt->bindParam(':reqid', $reqId);
        if($stmt->execute()) {

            $query = "SELECT * FROM `loans` WHERE loan_id = :lid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lid', $lonId);
            $stmt->execute();
            $loanData = $stmt->fetch(PDO::FETCH_ASSOC);
            $uId = $loanData['acc_id'];

            $this->insertTransreq($uId,$amount,$transac,$reason);


            return $response;
        } else {
            return 0;
        }
    }

    public function createRepaymentSchedule($userId,$reqId,$loanId, $loanAmount, $loanTerm) {
        $Interest = ($loanAmount * 0.03);
        $loanRec = $loanAmount - $Interest;
        $origDue = $loanAmount / $loanTerm;
        // $monthlyPayment = ($loanRec / $loanTerm);
        // $penaltyAmount = $monthlyPayment * 0.02;
        $currentDate = new DateTime();
        $success = true;

        for ($i = 1; $i <= $loanTerm; $i++) {
            $dueDate = clone $currentDate;
            $dueDate->modify("+".($i * 28)." days");
            $formatedDate = $dueDate->format('Y-m-d');
            $totalDue = $origDue;
            
            $query = "INSERT INTO loan_repayments (loan_id, user_id, req_id, due_date, interest_amount, penalty,total_due, loaned, origDue,terms, ammountHand) VALUES (:loan_id, :uid, :rid,:due_date, :interest_amount, 0, :total_due, :loaned, :orig,:term,:hand)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':loan_id', $loanId);
            $stmt->bindParam(':uid', $userId);
            $stmt->bindParam(':rid', $reqId);
            $stmt->bindParam(':due_date', $formatedDate);
            $stmt->bindParam(':interest_amount', $Interest);
            $stmt->bindParam(':total_due', $totalDue);
            $stmt->bindParam(':loaned', $loanAmount);
            $stmt->bindParam(':orig', $origDue);
            $stmt->bindParam(':term', $loanTerm);
            $stmt->bindParam(':hand', $loanRec);
            
            if(!$stmt->execute()){
                 $success = false;
            }
        }

        return $success ? 1 : 0;

    }

    public function userPayment($payId, $amDue, $udi, $loanId, $odue, $reqId) {
        $returnLoan = 0;
        $processedLoans = [];
  
        try {
            foreach ($payId as $i => $repaymentId) {
                $updateQuery = "UPDATE loan_repayments SET status = 'paid', amount_paid = total_due, updated_at = NOW() WHERE repayment_id = :repayment_id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':repayment_id', $repaymentId, PDO::PARAM_INT);
                $updateStmt->execute();
    
                $returnLoan += $odue[$i];
    
                $query = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status) 
                          VALUES (:acc_id, 'loan', 'repayment', :amount, 'completed')";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':acc_id', $udi[$i], PDO::PARAM_INT);
                $stmt->bindParam(':amount', $amDue[$i]);
                $stmt->execute();
    
                // Add to processed loans if not already processed
                if (!in_array($reqId[$i], $processedLoans)) {
                    $processedLoans[] = $reqId[$i];
                }
            }
    
            // Check repayment histories and increase loans if necessary
            foreach ($processedLoans as $reqId) {
                $repaymentHistory = $this->checkRepaymentHistoryByLoan($reqId);
                if ($repaymentHistory['total_repayments'] == $repaymentHistory['on_time_repayments']) {
                    $this->increaseLoanByRequestId($reqId);
                }
            }
            $this->loanPlus($loanId, $returnLoan);
    
            return  1;
        } catch (PDOException $e) {
            error_log("Database Error in userPayment: " . $e->getMessage());
            return  0;
        }
    }
    
    public function getAllTransactions($uid) {
        try {
            $query = "SELECT t.`transaction_id`, t.`acc_id`, acc.acc_name ,t.`transaction_type`, t.`sub_type`, t.`amount`, t.`status`, t.`created_at` ,t.note FROM `transactions` t 
            LEFT JOIN accounts acc ON t.acc_id = acc.acc_id WHERE t.acc_id = :u_id AND DATE(t.created_at) = CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':u_id', $uid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in getAllTransactions: " . $e->getMessage());
            return [];
        }
    }

    public function getAllTransactionsadmin() {
        try {
            $query = "SELECT t.`transaction_id`, t.`acc_id`, acc.acc_name ,t.`transaction_type`, t.`sub_type`, t.`amount`, t.`status`, t.`created_at` ,t.note FROM `transactions` t 
            LEFT JOIN accounts acc ON t.acc_id = acc.acc_id WHERE DATE(t.created_at) = CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in getAllTransactions: " . $e->getMessage());
            return [];
        }
    }
     private function insertTransreq($uid,$amount,$transac,$reason) {

        $query = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status, note) 
        VALUES (:acc_id, 'loan', 'request', :amount, :res, :reson)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acc_id', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':res', $transac);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':reson', $reason);
        $stmt->execute();
        }
    

    private function insertSaving($userId){

        $query = "INSERT INTO savings_accounts (user_id, last_transaction_date) 
        VALUES (:acc_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acc_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function reqWithdraw($userId,$savId,$amount){

        $sql = "INSERT INTO wthdraw_request (user_id, sav_id, amount, status) 
        VALUES (:user_id,:lon_id, :requested_amount, 'pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':lon_id', $savId);
        $stmt->bindParam(':requested_amount', $amount);
        if($stmt->execute()) {

    $query = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status) 
    VALUES (:acc_id, 'savings', 'withdrawal', :amount, 'pending')";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':acc_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':amount', $amount);
    $stmt->execute();

    return 1; 
} else {
    return 0; 
}
    }

    public function getWithdraw(){

        $query = "SELECT wr.id, wr.user_id, acc_name, acc_email, wr.sav_id, wr.amount, wr.date, wr.status, wr.rejection FROM wthdraw_request wr 
        LEFT JOIN accounts ac ON wr.user_id = ac.acc_id
        where status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    
    public function withdrawRespon($id, $response, $reason){
        if($response == 'reject'){
        $stmt = $this->conn->prepare("UPDATE wthdraw_request SET status = 'Rejected', date = NOW(), rejection = :reason WHERE id = :id");
        
        $stmt->bindParam(':reason', $reason);
    }else{
        $stmt = $this->conn->prepare("UPDATE wthdraw_request SET status = 'Approved', date = NOW() WHERE id = :id");
    }

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if($stmt->execute()){
            return $response;
        }else{
            return $response;
        }
    }

    public function savingsTransac($uid,$amount,$transac,$category,$reason) {

        $query = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status, note) 
        VALUES (:acc_id, 'savings', :cat, :amount, :res, :reson)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':acc_id', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':res', $transac);
        $stmt->bindParam(':cat', $category);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':reson', $reason);
        $stmt->execute();
        
    }
    public function countWithdraw($userId){

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS daily_withdrawals FROM transactions WHERE acc_id = :user_id AND sub_type = 'withdrawal' AND transaction_type = 'savings' AND status = 'Completed' AND DATE(created_at) = CURDATE()");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if($stmt->execute()){
        
       $row = $stmt->fetch(PDO::FETCH_ASSOC);
       
       return $row['daily_withdrawals'];

        }else{

        return 0;

        }
    }

    public function depositMe($savId,$amount){

        $stmt = $this->conn->prepare("UPDATE savings_accounts SET balance = balance + :amount, last_transaction_date = NOW() WHERE sav_id = :sav_id");
        $stmt->bindParam(':sav_id', $savId, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);

        if($stmt->execute()){
        
        return 1;

        }else{

        return 0;

        }
    }
    public function withdrawMe($savId,$amount){

        $stmt = $this->conn->prepare("UPDATE savings_accounts SET balance = balance - :amount, last_transaction_date = NOW() WHERE sav_id = :sav_id");
        $stmt->bindParam(':sav_id', $savId, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);

        if($stmt->execute()){
        
        return 1;

        }else{

        return 0;

        }
    }
    public function loanMinus($lonid,$amount){

        $stmt = $this->conn->prepare("UPDATE loans SET loan_amount = loan_amount - :amount WHERE loan_id = :lon_id");
        $stmt->bindParam(':lon_id', $lonid, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);

        if($stmt->execute()){
        
        return 1;

        }else{

        return 0;

        }
    }
    private function loanPlus($lonid, $amount) {
        try {
            $stmt = $this->conn->prepare("UPDATE loans SET loan_amount = loan_amount + :amount WHERE loan_id = :lon_id");
            $stmt->bindParam(':lon_id', $lonid, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            error_log("Database Error in loanPlus: " . $e->getMessage());
            return 0;
        }
    }

public function sendEmail($useremail, $reason, $response){

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0;                      // Enable verbose debug output (0 = off)
    $mail->isSMTP();                           // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';    // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                  // Enable SMTP authentication
    $mail->Username   = 'lopezrolandshane@gmail.com'; // SMTP username
    $mail->Password   = 'gssjmdazxyyqioqq';   // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                   // TCP port to connect to

    // Recipients
    $mail->setFrom('lopezrolandshane@gmail.com','Loan Land');
    $mail->addAddress($useremail); // Add a recipient
    // $mail->addAddress('ellen@example.com');                   // Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $response;
    $mail->Body    = $reason;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
    }
public function acctypeChange($user_id, $type){
        $query = "UPDATE accounts SET acc_type = :type WHERE acc_id = :user_id";
        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);


        if ($stmt->execute()) {

            return $user_id;
        } else {

            return false;
        }
    }

    public function checkSave($user_id){
        $query = "SELECT * From savings_accounts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {
                
                return true;

            }else {
                $this->insertSaving($user_id);
                return false;
            }

        } else {

            return false;
        }
    }

    public function getUserbills($user_id){
        
        $currentMonth = date('m');
        $currentYear = date('Y');

        $query = "SELECT lr.`repayment_id`, lr.`loan_id`, lr.origDue,lq.request_date, lr.req_id,lr.terms,lr.`user_id`, acc.acc_name, acc.acc_type, lr.`due_date`, lr.`interest_amount`, lr.`penalty`, lr.`total_due`, lr.`loaned`, lr.`ammountHand`, lr.`amount_paid`, lr.`status`, lr.`created_at`, lr.`updated_at` 
        FROM loan_repayments lr
        LEFT JOIN accounts acc ON lr.user_id = acc.acc_id
        LEFT JOIN loan_requests lq ON lr.req_id = lq.request_id
        WHERE lr.user_id = :loan_id 
        AND lr.status != 'paid'
        AND MONTH(lr.due_date) = :currentMonth
        AND YEAR(lr.due_date) = :currentYear";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':loan_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
        $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmt->execute();
        
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function checkRepaymentHistoryByLoan($req_id) {
        $query = "
            SELECT COUNT(*) AS total_repayments,
                   SUM(CASE WHEN status = 'paid' AND updated_at <= due_date THEN 1 ELSE 0 END) AS on_time_repayments 
            FROM loan_repayments 
            WHERE req_id = :req_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':req_id', $req_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function increaseLoanByRequestId($reqId) {
        // Fetch the current loan details

        $loanQuery = "SELECT * FROM loan_requests WHERE request_id = :req_id";
        $reqStmt = $this->conn->prepare($loanQuery);
        $reqStmt->bindParam(':req_id', $reqId, PDO::PARAM_INT);
        $reqStmt->execute();
        $loanid =  $reqStmt->fetch(PDO::FETCH_ASSOC);
        
        $lonId = $loanid['loan_id'];

        $loanQuery = "SELECT * FROM loans WHERE loan_id = :lon_id";
        $loanStmt = $this->conn->prepare($loanQuery);
        $loanStmt->bindParam(':lon_id', $lonId, PDO::PARAM_INT);
        $loanStmt->execute();

        $currentLoan = $loanStmt->fetch(PDO::FETCH_ASSOC);
    
        if ($currentLoan) {
            $currentLoanAmount = $currentLoan['loan_amount'];
            $newLoanAmount = min($currentLoan['loan_amount'] + 5000, 50000);
            $newTerms = min($currentLoan['loan_term'] + 3, 32);
            $total =  $newLoanAmount - $currentLoanAmount;

            $updateLoanQuery = "UPDATE loans SET loan_amount = :loaned, loan_term = :terms, updated_at = NOW() WHERE loan_id = :loan_id";
            $updateLoanStmt = $this->conn->prepare($updateLoanQuery);
            $updateLoanStmt->bindParam(':loan_id', $lonId, PDO::PARAM_INT);
            $updateLoanStmt->bindParam(':loaned', $newLoanAmount, PDO::PARAM_INT);
            $updateLoanStmt->bindParam(':terms', $newTerms, PDO::PARAM_INT);
            $updateLoanStmt->execute();

            if ($currentLoanAmount <= 50000) {
                // Log the loan transaction
                $insertTransactionQuery = "INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status) 
                    VALUES (:acc_id, 'loan', 'increase', :amount, 'completed')";
                $insertTransactionStmt = $this->conn->prepare($insertTransactionQuery);
                $insertTransactionStmt->bindParam(':acc_id', $currentLoan['acc_id'], PDO::PARAM_INT);
                $insertTransactionStmt->bindParam(':amount', $total, PDO::PARAM_INT);
                $insertTransactionStmt->execute();
            }
        }
    }
    // public function getUsedemail($email) {
    //     try {
    //         $query = "SELECT acc_status, rej_reason FROM `accounts` WHERE acc_email = :emil";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':emil', $email, PDO::PARAM_INT);
    //         $stmt->execute();
    //         return $stmt->fetch(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         error_log("Database Error in getAllTransactions: " . $e->getMessage());
    //         return [];
    //     }
    // }

    public function getTotalearning(){
        $query = "
        SELECT SUM(total_income) as total
        FROM (
            SELECT req_id, interest_amount + SUM(penalty) as total_income
            FROM loan_repayments
            WHERE status = 'paid'
            GROUP BY req_id
        ) as subquery
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Calculate total income
      

        return $result['total'];
    }

    public function getSavings($userId){
        $sqli = "SELECT * FROM `savings_accounts` WHERE `user_id` = :acId";
        $stmte = $this->conn->prepare($sqli);
        $stmte->bindParam(':acId', $userId);
        $stmte->execute();
        return $stmte->fetch(PDO::FETCH_ASSOC);
    }
    public function getLoan($userId){
        $sqli = "SELECT * FROM `loans` WHERE `acc_id` = :acId";
        $stmts = $this->conn->prepare($sqli);
        $stmts->bindParam(':acId', $userId);
        $stmts->execute();
        return $stmts->fetch(PDO::FETCH_ASSOC);

    }
}


?>
