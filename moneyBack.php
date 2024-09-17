<?php
require_once 'config/database.php';
    $query = "
    SELECT SUM(total_income) as total
    FROM (
        SELECT req_id, interest_amount + SUM(penalty) as total_income
        FROM loan_repayments
        WHERE status = 'paid'
        AND YEAR(created_at) = YEAR(CURDATE())
        GROUP BY req_id
    ) as subquery
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalIncome = $result['total'];

    $distributionAmount = $totalIncome * 0.02;

    $query = "SELECT acc_id FROM accounts WHERE acc_type = 'premium'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $premiumUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $premiumUserCount = count($premiumUsers);

    if ($premiumUserCount > 0) {
        // Calculate the amount per premium user
        $amountPerUser = $distributionAmount / $premiumUserCount;

        // Begin a transaction
        $db->beginTransaction();

        try {
            foreach ($premiumUsers as $user) {
                $accId = $user['acc_id'];

                $updateQuery = "UPDATE savings_accounts SET balance = balance + :amount WHERE user_id = :acc_id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':amount', $amountPerUser, PDO::PARAM_STR);
                $updateStmt->bindParam(':acc_id', $accId, PDO::PARAM_INT);
                $updateStmt->execute();

                // Insert a transaction record for the user
                $insertQuery = "
                    INSERT INTO transactions (acc_id, transaction_type, sub_type, amount, status)
                    VALUES (:acc_id, 'savings', 'distribution', :amount, 'completed')
                ";
                $insertStmt = $db->prepare($insertQuery);
                $insertStmt->bindParam(':acc_id', $accId, PDO::PARAM_INT);
                $insertStmt->bindParam(':amount', $amountPerUser, PDO::PARAM_STR);
                $insertStmt->execute();
            }

            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
        }
    }




