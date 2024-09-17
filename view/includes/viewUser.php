
<?php
// Include database connection code
include_once '../../config/database.php';
include_once '../../model/users.php';
$transacTable = new User($db);
// Check if noteId is provided in the GET request
if (isset($_GET['accId'])) {
    // Get the note ID from the GET request
    $accId = $_GET['accId'];
    $adminAction = $_GET['action'];

    $repayments = $transacTable->getUserbills($accId);
    $save = $transacTable->getSavings($accId);
    $loanData = $transacTable->getLoan($accId);

    try {
        // Connect to the database
       

        // Prepare and execute a query to fetch the note content based on noteId
        $stmt = $db->prepare("SELECT * FROM accounts WHERE acc_id = ?");
        $stmt->execute([$accId]);

        // Fetch the note data
        $acc = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the note was found
        if ($acc) {
            // Output the note content
            echo'<div class = "detailCont">
            <span class="close" onclick="closeAcc()" >&times;</span>
            <br>
            <div class = "userDeatails">';
           
            
            echo '<label for="">name:</label>';
            echo "<p>" . $acc["acc_name"] . "</p><br>";
            echo '<label for="">Account Type:</label>';
            echo "<select  name='account_type' id='account_type' onchange='updateAccountType(this.value,".$acc['acc_id'].")'>
            <option value='basic'" . ($acc["acc_type"] == 'basic' ? ' selected' : '') . ">basic</option>
            <option value='premium'" . ($acc["acc_type"] == 'premium' ? ' selected' : '') . ">premium</option>
        </select><br>";
            echo '<label for="">Address:</label>';
            echo "<p>" . $acc["acc_address"] . "</p>";
            echo '<label for="">Gender:</label>';
            echo "<p>" . $acc["acc_gender"] . "</p>";
            echo '<label for="">Date of Birth:</label>';
            echo "<p>" . $acc["acc_dob"] . "</p>";
            echo '<label for="">Age:</label>';
            echo "<p>" . $acc["acc_age"] . "</p>";
            echo '<label for="">Email:</label>';
            echo "<p>" . $acc["acc_email"] . "</p>";
            echo '<label for="">Phone no.::</label>';
            echo "<p>" . $acc["acc_pnum"] . "</p>";
            echo '<label for="">Bank name:</label>';
            echo "<p>" . $acc["bnk_name"] . "</p>";
            echo '<label for="">Bank Account no.:</label>';
            echo "<p>" . $acc["bnk_accnum"] . "</p>";
            echo '<label for="">Holder:</label>';
            echo "<p>" . $acc["bnk_holder"] . "</p>";
            echo '<label for="">name:</label>';
            echo "<p>" . $acc["acc_tin"] . "</p>";
            echo '<label for="">Company name:</label>';
            echo "<p>" . $acc["com_name"] . "</p>";
            echo '<label for="">Company Phone no.:</label>';
            echo "<p>" . $acc["com_add"] . "</p>";
            echo '<label for="">Position:</label>';
            echo "<p>" . $acc["com_pos"] . "</p>";
            echo '<label for="">Earnings:</label>';
            echo "<p>" . $acc["com_earnig"] . "</p><br>";
            echo '<label for="">Account Status:</label>';
            echo "<p>" . $acc["acc_status"] . "</p>";
            echo '</div> <hr>';
           
            echo '<div class = "userImage">';

            echo '<label for="">Billing Proof:</label><br>';
              echo "<img src='data:image/jpeg;base64," . base64_encode($acc['acc_billing']) . "' alt='Billing Proof' onclick='showImage(this.src)'><br>";
              echo '<label for="">Valid ID:</label><br>';
            echo "<img src='data:image/jpeg;base64," . base64_encode($acc['acc_valid_id']) . "' alt='Valid ID' onclick='showImage(this.src)'><br>";
            echo '<label for="">COE:</label><br>';
            echo "<img src='data:image/jpeg;base64," . base64_encode($acc['acc_coe']) . "' alt='COE' onclick='showImage(this.src)'><br>";
          
            echo '</div>';
           
if($adminAction == 'accRespon'){
            echo '
            <form class="responseForm">
                <input type="hidden" name="userId" value="'.$acc["acc_id"].'">
                <input type="hidden" name="email" value="'.$acc["acc_email"].'">
                <input type="button" class="responseButton" data-action="approve" value="Approve">
                <input type="button" class= "submitRejection" id="submitRejection" value="Reject">
                <div id="popupOverlay" class="popupOverlay">
                <div id="popupDiv" class="popupDiv">
                    <h3>Rejection Reason</h3>
                    <textarea id="rejectionReason" name = "reason" rows="4" cols="50"></textarea><br>
                    <input type="button" class="responseButton" data-action="reject" value="Reject">
                    <input type="button" onclick = "closeRes()" id="cancelRejection" class="cancelRejection" value="Cancel">
                </div>
                </div>
                <button onclick="closeAcc()">Close</button>
            </form>';
        }
        
        else if($adminAction == 'accAct'){
            $buttons = "";
            $button = "";
            if($acc["acc_status"] === 'active'){
                $buttons = 'hidden';
            }else if($acc["acc_status"] ==='deactivate'){
                $button = 'hidden';
            }
            else {
                $buttons = '';
                $button = '';
            }

            ?>

            <?php if ($acc["acc_type"] !== 'premium'|| empty($save)){ 

            $hide = 'hidden';

            } else{
            $hide ="";
            }
            ?>
<hr>
<div class = "userLoansave">
    <div>
<h2>Loan</h2>
        <label for="">Loan Amount</label>
        <h3><?php echo htmlspecialchars($loanData['loan_amount']); ?></h3>
        <label for="">Loan Term</label>
        <h4><?php echo htmlspecialchars($loanData['loan_term']); ?></h4>
        </div>
<hr>

            <div class="forsave" <?php echo $hide;?>>
            <h2>Savings</h2>
            <label for="">Savings Balance</label>
            <h3><?php echo htmlspecialchars($save['balance']); ?></h3>
            <hr>
            <button id="wdBtn">Withdraw</button>
            <button id="depositer">Deposit</button>
            </div>
</div>
<hr>
<div class ='userbill'>
            <h1>User Bills</h1>
            
            <label for="monthFilter">Select Month:</label>
            <select id="monthFilter" name="monthFilter" onchange="filterRepayments()">
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
            
            <label>
                    <input type="radio" name="statusFilter" value="all" onclick = "filterRepayments()"> all
                </label>
            <label>
                    <input type="radio" id = 'paid' name="statusFilter" value="paid" onclick = "filterRepayments()"> Paid
                </label>
                <label>
                    <input type="radio" name="statusFilter" value="pending" onclick = "filterRepayments()"> Unpaid
                </label>
                
            
            <div id="repaymentsContainer">
            <?php $hide ='hidden'; if ($repayments): ?>
                <?php foreach ($repayments as $repayment): ?>
                    <?php if ($repayment['status'] != 'paid'): ?>
                        <div class="repayment">
            
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
                            <hr>
                            <p>Due Date: <?php echo date('F d Y', strtotime($repayment['due_date']));?></p>
                            <input type="hidden" name="total_due[]" value="<?php echo $repayment['total_due']; ?>">
            
                            <p >Penalty: <?php echo $repayment['penalty']; ?></p>
                            <p >Total Due: <?php echo $repayment['total_due']; ?></p>
                            
                            <p>Status: <?php echo $repayment['status']; ?></p>
                            </div>
                        </div>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
                
            <?php else: ?>
                <p>No repayment For this Month.</p>
            <?php endif; ?>
            </div>
            
            
                        <?php

            echo '
            <form class="responseForm">
            <input type="hidden" name="userId" value="'.$acc["acc_id"].'">
            <input type="button" class="responseButton"  '.$buttons.' data-action="activate" value="Activate">
            <input type="button" class= "submitRejection" '.$button.' id="submitRejection" value="Deactivate">
            <div id="popupOverlay" class="popupOverlay">
            <div id="popupDiv" class="popupDiv">
                <h3>Deactivate Reason</h3>
                <textarea id="rejectionReason" name = "reason" rows="4" cols="50"></textarea><br>
                <input type="button" class="responseButton" data-action="deact" value="Deactivate">
                <input type="button" onclick = "closeRes()" id="cancelRejection" class="cancelRejection" value="Cancel">
            </div>
            </div>
            <button onclick="closeAcc()">Close</button>
        </form>';

 


        }
        else{
            echo '<button onclick="closeAcc()">Close</button>';
        }
    } 

    } catch (PDOException $e) {
        // Handle database error
        echo $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
} else {
    // If noteId is not provided in the GET request, output an error message
    echo "Acc ID not provided.";
}

?>
</div>
</div>
