
<?php
session_start();
require_once '../../config/database.php';
$userId = $_SESSION['userId'];
// Fetch the filter values from the GET request
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$subFilter = isset($_GET['subFilter']) ? $_GET['subFilter'] : '';
$dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : '';

// Construct the base query
$query = "SELECT t.`transaction_id`, t.`acc_id`, acc.acc_name ,t.`transaction_type`, t.`sub_type`, t.`amount`, t.`status`, t.`created_at` ,t.note FROM `transactions` t 
LEFT JOIN accounts acc ON t.acc_id = acc.acc_id WHERE 1=1 and t.acc_id = :uid";

// Add conditions based on the filters
if ($filter !== 'all') {
    $query .= " AND transaction_type = :filter";
}
if ($subFilter) {
    $query .= " AND sub_type = :subFilter";
}
if ($dateFilter === 'this_month') {
    $query .= " AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
} elseif ($dateFilter === 'this_year') {
    $query .= " AND YEAR(created_at) = YEAR(CURRENT_DATE())";
}else{
    $query .= "";
}


$stmt = $db->prepare($query);

if ($filter !== 'all') {
    $stmt->bindParam(':filter', $filter);
}
if ($subFilter) {
    $stmt->bindParam(':subFilter', $subFilter);
}

$stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate the HTML table rows
foreach ($transactions as $transaction) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($transaction['transaction_type']) . '</td>';
    echo '<td>' . htmlspecialchars($transaction['sub_type']) . '</td>';
    echo '<td>' . htmlspecialchars($transaction['amount']) . '</td>';
    echo '<td>' . htmlspecialchars($transaction['status']) . '</td>';
    ?>
    <td><?php echo date('F d Y', strtotime($transaction['created_at'])); ?></td>
    <?php
    echo '<td>' . htmlspecialchars($transaction['note']) . '</td>';
    echo '</tr>';
}

if (empty($transactions)) {
    echo '<tr><td colspan="6">No transactions found.</td></tr>';
}
?>
