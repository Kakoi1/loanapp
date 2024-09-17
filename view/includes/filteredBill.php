<?php
session_start();
require_once '../../config/database.php';

$userId = $_SESSION['userId'];
$month = isset($_GET['month']) ? $_GET['month'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "
    SELECT lr.repayment_id, lr.loan_id, lr.origDue, lq.request_date, lr.req_id, lr.terms, lr.user_id, acc.acc_name, acc.acc_type, lr.due_date, lr.interest_amount, lr.penalty, lr.total_due, lr.loaned, lr.ammountHand, lr.amount_paid, lr.status, lr.created_at, lr.updated_at 
    FROM loan_repayments lr
    LEFT JOIN accounts acc ON lr.user_id = acc.acc_id
    LEFT JOIN loan_requests lq ON lr.req_id = lq.request_id
    WHERE lr.user_id = :user_id";

if ($status !== 'all') {
    $query .= " AND lr.status = :status";
}

if ($month) {
    $query .= " AND MONTH(lr.due_date) = :month";
}

$query .= " ORDER BY lr.due_date ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

if ($status !== 'all') {
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
}

if ($month) {
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
}

$stmt->execute();
$repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($repayments):
    foreach ($repayments as $repayment):
?>
        <div class="repayment">
            <input type="checkbox" name="repayments[]" value="<?php echo $repayment['repayment_id']; ?>" 
                   data-total-due="<?php echo $repayment['total_due']; ?>" onclick="calculateTotal()" <?php echo $repayment['status'] == 'paid' ? 'hidden' : ''; ?> />
            <div class="repayment <?php echo $repayment['status'] == 'overdue' ? 'overdue' : ''; ?>">
                <p>Request Date: <?php echo date('F d Y', strtotime($repayment['request_date'])); ?></p>
                <p>Name: <?php echo htmlspecialchars($repayment['acc_name']); ?></p>
                <p>Account Type: <?php echo htmlspecialchars($repayment['acc_type']); ?></p>
                <p>Amount Loaned: <?php echo htmlspecialchars($repayment['loaned']); ?></p>
                <p>Term: <?php echo htmlspecialchars($repayment['terms']); ?></p>
                <p>Interest Amount: <?php echo htmlspecialchars($repayment['interest_amount']); ?></p>
                <p>Received: <?php echo htmlspecialchars($repayment['ammountHand']); ?></p>
                <input type="hidden" name="userId[]" value="<?php echo htmlspecialchars($userId); ?>">
                <input type="hidden" name="lonId" value="<?php echo htmlspecialchars($repayment['loan_id']); ?>">
                <input type="hidden" name="origDue[]" value="<?php echo htmlspecialchars($repayment['origDue']); ?>">
                <input type="hidden" name="reqId[]" value=" <?php echo $repayment['req_id'];?>">
                <hr>
                <p>Due Date: <?php echo date('F d Y', strtotime($repayment['due_date'])); ?></p>
                <input type="hidden" name="total_due[]" value="<?php echo htmlspecialchars($repayment['origDue']); ?>">
                <p>Penalty: <?php echo htmlspecialchars($repayment['penalty']); ?></p>
                <p>Total Due: <?php echo htmlspecialchars($repayment['origDue']); ?></p>
                <p>Status: <?php echo htmlspecialchars($repayment['status']); ?></p>
            </div>
        </div>
        <hr>
<?php
    endforeach;
else:
?>
    <p>No repayments for this period.</p>
<?php
endif;
?>
