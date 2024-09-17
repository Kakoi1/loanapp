<?php

require_once 'config/database.php';

try {
    // Define the time period (30 days ago)
    $dateThreshold = new DateTime();
    $dateThreshold->modify('-30 days');
    $formatedDate = $dateThreshold->format('Y-m-d H:i:s');
    // Connect to the database
    $query = "DELETE FROM `accounts` WHERE `acc_status` = 'rejected' AND `rejection_date` < :dateThreshold";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':dateThreshold', $formatedDate);
    $stmt->execute();

    echo "Deleted " . $stmt->rowCount() . " rejected accounts.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    // Update overdue repayments
    $currentDate = (new DateTime())->format('Y-m-d');
    $updateQuery = "UPDATE loan_repayments SET status = 'overdue' WHERE due_date < :current_date AND status = 'pending'";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':current_date', $currentDate);
    $updateStmt->execute();

    $query = "SELECT * FROM loan_repayments WHERE due_date < :current_date AND status = 'overdue'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':current_date', $currentDate);
    $stmt->execute();
    $overdueRepayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($overdueRepayments as $repayment) {
        if ($repayment['status'] === 'overdue') {
            // Calculate penalty amount (2% of the total due amount)
            $penaltyAmount = $repayment['total_due'] * 0.02;

            $totalpenal = $repayment['penalty'] + $penaltyAmount;
            // Add penalty to the total due amount
            $totalDueWithPenalty = $repayment['total_due'] + $penaltyAmount;

            // Update the repayment with the new total due amount including penalty
            $updateQuery = "UPDATE loan_repayments 
                            SET total_due = :total_due, penalty = :penal
                            WHERE repayment_id = :repayment_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':total_due', $totalDueWithPenalty);
            $updateStmt->bindParam(':penal', $totalpenal);
            $updateStmt->bindParam(':repayment_id', $repayment['repayment_id'], PDO::PARAM_INT);
            $updateStmt->execute();
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

$interimPeriodDays = 1;

try {
$query = "
SELECT lr.user_id, MAX(lr.due_date) AS latest_due_date
FROM loan_repayments lr
LEFT JOIN accounts acc ON lr.user_id = acc.acc_id
WHERE lr.status != 'paid'
GROUP BY lr.user_id
HAVING latest_due_date < DATE_SUB(CURDATE(), INTERVAL :interimPeriodDays DAY)
";

// Prepare the SQL statement to prevent SQL injection
$stmt = $db->prepare($query);
$stmt->bindParam(':interimPeriodDays', $interimPeriodDays, PDO::PARAM_INT);
$stmt->execute();

// Fetch all users who have overdue repayments
$usersToDisable = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through each user and disable their account
foreach ($usersToDisable as $user) {
    $updateQuery = "UPDATE accounts SET acc_status = 'deactivate', rej_reason = 'User Didnt pay' WHERE acc_id = :userId";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':userId', $user['user_id'], PDO::PARAM_INT);
    $updateStmt->execute();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

    try {

        $query = "
            SELECT sa.user_id
            FROM savings_accounts sa
            LEFT JOIN accounts acc ON sa.user_id = acc.acc_id
            WHERE sa.balance = 0
              AND sa.last_transaction_date < DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
              AND acc.acc_type != 'basic'
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $usersToDowngrade = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usersToDowngrade as $user) {
            // Update user's account type to 'Basic'
            $updateQuery = "UPDATE accounts SET acc_type = 'basic' WHERE acc_id = :user_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
            $updateStmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }





?>
