<?php 
session_start();
include_once '../config/database.php';
include_once '../model/users.php';
$emails = $_SESSION['userEmail'];

$transacTable = new User($db);

if($emails != false){
    
    try {
        $sql = "SELECT * FROM `accounts` WHERE `acc_email` = :emil";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':emil', $emails);
        $stmt->execute();
        
        $dataUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $dataUser['acc_id'];
        $usertype = $dataUser['acc_type'];

        $transactions = $transacTable->getAllTransactions($userId);

        $sqli = "SELECT * FROM `loans` WHERE `acc_id` = :acId";
        $stmts = $db->prepare($sqli);
        $stmts->bindParam(':acId', $userId);
        $stmts->execute();
        $loanData = $stmts->fetch(PDO::FETCH_ASSOC);
        $loanId = $loanData['loan_id'];

        $sqli = "SELECT * FROM `savings_accounts` WHERE `user_id` = :acId";
        $stmte = $db->prepare($sqli);
        $stmte->bindParam(':acId', $userId);
        $stmte->execute();
        $save = $stmte->fetch(PDO::FETCH_ASSOC);

        $repayments = $transacTable->getUserbills($userId);


    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();                 
    }
} else {
    echo "<script>alert('no data found');
    document.location.href = '../logout.php';
</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Loan Details</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modal styles */

    </style>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2, h3, h4, h5 {
            margin-bottom: 20px;
        }
        hr {
            margin: 20px 0;
        }
        label {
            font-weight: bold;
        }
        h3, h4 {
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
<body>
    <h1><?php echo htmlspecialchars($dataUser['acc_name']); ?></h1>
    <hr>
    <label for="">Loan Amount</label>
    <h3><?php echo htmlspecialchars($loanData['loan_amount']); ?></h3>
    <label for="">Loan Term</label>
    <h4><?php echo htmlspecialchars($loanData['loan_term']); ?></h4>



    <button id="loanBtn">Loan</button>
        <br>
        <hr>
        <?php if ($usertype !== 'premium'){ 

            $hide = 'hidden';

          } else{
            $hide ="";
          }
          ?>

            <div <?php echo $hide;?>>
            <label for="">Savings Balance</label>
        <h3><?php echo htmlspecialchars($save['balance']); ?></h3>
        <hr>
        <button id="wdBtn">Withdraw</button>
        <button id="depositer">Deposit</button>
            </div>
        <br>
        <h1>Transactions</h1>

        <form id="filterForm">
    
        <h5>For Date</h5>
        <label>
            <input type="radio" name="dateFilter" value="all" onclick="filterTransacUser()">
            All
        </label>
        <label>
            <input type="radio" name="dateFilter" value="this_month" onclick="filterTransacUser()">
            This Month
        </label>
        <label>
            <input type="radio" name="dateFilter" value="this_year" onclick="filterTransacUser()">
            This Year
        </label>
<hr>
        <label>
            <input type="radio" name="filter" value="all" checked onclick="filterTransacUser()">
            All
        </label>
        <label>
            <input type="radio" name="filter" value="loan" onclick="filterTransacUser()">
            Loan
        </label>
        <label>
            <input type="radio" name="filter" value="savings" onclick="filterTransacUser()">
            Savings
        </label>
        <hr>
        <!-- Placeholder for Loan sub-types -->
        <div id="loanSubTypes" style="display:none;">
            <label>
                <input type="radio" name="subFilter" value="request" onclick="filterTransacUser()">
                Loan Request
            </label>
            <label>
                <input type="radio" name="subFilter" value="repayment" onclick="filterTransacUser()">
                Repayment
            </label>
        </div>

        <!-- Placeholder for Savings sub-types -->
        <div id="savingsSubTypes" style="display:none;">
            <label>
                <input type="radio" name="subFilter" value="withdrawal" onclick="filterTransacUser()">
                Withdrawal
            </label>
            <label>
                <input type="radio" name="subFilter" value="deposit" onclick="filterTransacUser()">
                Deposit
            </label>
        </div>
    </form>

        <table>
        <thead>
            <tr>
                <th>Transaction Type</th>
                <th>Sub Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody id="transactionsTable">
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['sub_type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                        <td><?php echo date('F d Y', strtotime($transaction['created_at']));?></td>
                        <td><?php echo htmlspecialchars($transaction['note']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
        <br>
        <h1>Loan Repayments</h1>

        <label for="monthFilter">Select Month:</label>
    <select id="monthFilter" name="monthFilter" >
        <option value="">All</option>
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
<br>
<br>
    <label>
        <input type="radio" id = 'paid' name="statusFilter" value="paid"> Paid
    </label>
    <label>
        <input type="radio" name="statusFilter" value="pending"> Unpaid
    </label>
   
        <p>Total Amount to Pay: <span id="totalAmount">0.00</span></p>
    <form id="repaymentForm">
    <button type="button" onclick="makePayment()">Pay Selected</button>
    <div id="repaymentsContainer">
        <?php if ($repayments): ?>
            <?php foreach ($repayments as $repayment): ?>
                <?php if ($repayment['status'] != 'paid'): ?>
                    <div class="repayment">

                     <input type="checkbox" name="repayments[]" value="<?php echo $repayment['repayment_id']; ?>" 
                     data-total-due="<?php echo $repayment['total_due']; ?>" onclick="calculateTotal()" />
                     <div class="repayment <?php echo $repayment['status'] == 'overdue' ? 'overdue' : ''; ?>">
                     <p>Request Date: <?php echo date('F d Y', strtotime($repayment['request_date']));?></p>
                     <p>Name: <?php echo $repayment['acc_name']; ?></p>
                     <p>Account Type: <?php echo $repayment['acc_type']; ?></p>
                     <p>Amount Loanede: <?php echo $repayment['loaned']; ?></p>
                     <p>Term: <?php echo $repayment['terms']; ?></p>
                     <p>Interest Amount: <?php echo $repayment['interest_amount']; ?></p>

                     <p>Recieved: <?php echo $repayment['ammountHand']; ?></p>
                        <input type="hidden" name="userId[]" value=" <?php echo $userId ?>">
                        <input type="hidden" name="lonId" value=" <?php echo $loanId ?>">
                        <input type="hidden" name="origDue[]" value=" <?php echo $repayment['origDue'];?>">
                        <input type="hidden" name="reqId[]" value=" <?php echo $repayment['req_id'];?>">
                        <hr>
                        <p>Due Date: <?php echo date('F d Y', strtotime($repayment['due_date']));?></p>
                        <input type="hidden" name="total_due[]" value="<?php echo $repayment['origDue']; ?>">

                        <p >Penalty: <?php echo $repayment['penalty']; ?></p>
                        <p >Total Due: <?php echo $repayment['origDue']; ?></p>
                        
                        <p>Status: <?php echo $repayment['status']; ?></p>
                        </div>
                    </div>
                    <hr>
                <?php endif; ?>
            <?php endforeach; ?>
            
        <?php else: ?>
            <p>No repayment For this Month.</p>
        <?php endif; 
       $userLoanTerm = $loanData['loan_term']; // User's current loan term

       // Define the initial set of loan terms
       $initialLoanTerms = [1, 3, 6, 12];
       $loanTerms = $initialLoanTerms;
       
       // Add terms incrementally up to the user's current term if it's beyond the initial set
       for ($term = max($initialLoanTerms); $term <= $userLoanTerm; $term += 3) {
           if (!in_array($term, $loanTerms)) {
               $loanTerms[] = $term;
           }
       }
       
       // Ensure the current term is included if it doesn't fit the incremental pattern
       if (!in_array($userLoanTerm, $loanTerms)) {
           $loanTerms[] = $userLoanTerm;
       }
       
       sort($loanTerms); 
        ?>
        </div>
    </form>

    <!-- The Modal -->
    <div id="loanModal" class="modal">
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
            <h2>Loan Form</h2>
            <form  id = "loanForm">
                <label for="loan_amount">Loan Amount:</label>
                <input type="hidden" name="userID" id="uId" value = "<?php echo htmlspecialchars($dataUser['acc_id']);?>">
                <input type="hidden" name="loanID" id="lonId" value = "<?php echo htmlspecialchars($loanData['loan_id']);?>">
                <input type="hidden" name="balance" id="balance" value = "<?php echo htmlspecialchars($loanData['loan_amount']);?>">

                <input type="number" id="loan_amount" name="loan_amount" min="5000" max="<?php echo htmlspecialchars($loanData['loan_amount']); ?>" step="1000" required>
                <br>
                <label for="loan_term">Loan Term (months):</label>
                <select id="loan_term" name="loan_term" required>
        <?php foreach ($loanTerms as $term): ?>
            <option value="<?php echo $term; ?>" <?php echo $term == $userLoanTerm ? 'selected' : ''; ?>>
                <?php echo $term; ?> month<?php echo $term > 1 ? 's' : ''; ?>
            </option>
        <?php endforeach; ?>
    </select>
                <h3>Interest Rate 3%</h3>
                <br><br>
                <input type="button" name = "reqBut" id="reqBut" value = "Loan Request">
            </form>
        </div>
    </div>

    <div id="depModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closedep">&times;</span>
            <h2>Deposit </h2>
            <form id = 'depoForm'>
                <input type="hidden" name="userID" id="uId" value = "<?php echo htmlspecialchars($dataUser['acc_id']);?>">
                <input type="hidden" name="action" id="action" value = "deposit">
                <input type="hidden" name="saveID" id="saveID" value = "<?php echo htmlspecialchars($save['sav_id']);?>">
                <input type="hidden" name="balance" id="balance" value = "<?php echo $save['balance']?>">
                <label for="amount">Amount to Deposit:</label>
                <input type="number" id="amount" name="amount" min="100" max="1000" required>
                <button type="button" id="deposit">Deposit</button>
    </form>
        </div>
    </div>

    <div id="savModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closesav">&times;</span>
            <h2>Withdraw</h2>
            <form id = 'withForm'>
                <input type="hidden" name="userID" id="uId" value = "<?php echo htmlspecialchars($dataUser['acc_id']);?>">
                <input type="hidden" name="action" id="action" value = "withdraw">
                <input type="hidden" name="balance" id="balance" value = "<?php echo $save['balance']?>">
                <input type="hidden" name="saveID" id="saveID" value = "<?php echo htmlspecialchars($save['sav_id']);?>">
                <p>Balance: <?php echo $save['balance']?></p>
                <label for="amount">Amount to Withdraw:</label>
                <input type="number" id="amount" name="amount" min="500" max="5000" required>
                <button type="button" id = 'withReq'>Request Withdrawal</button>
    </form>
        </div>
    </div>

    <script src ="assets/dash.js">

    </script>
</body>
</html>
