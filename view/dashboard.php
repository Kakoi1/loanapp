<?php
        include_once '../model/users.php';
        include_once '../config/database.php';
        $transacTable = new User($db);

        $sql = "SELECT * FROM `accounts` where acc_status = 'pending'";
        $stmt = $db->query($sql);

       $withdrawRequests = $transacTable->getWithdraw();

       $getEarning = $transacTable->getTotalearning();
        // Fetch results and display in the table
  
        
        $transactions = $transacTable->getAllTransactionsadmin();
        ?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Information</title>
 <link rel="stylesheet" href="assets/style.css">
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>

    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    
}

.container {
    width: 800px;
    margin: auto;
    padding: 50px;
    background: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    
    
}

.header {
    text-align: center;
    margin-bottom: 20px;
}

h1 {
    margin-bottom: 10px;
    color: #333;
}

table {
    width: 90%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

table th {
    background-color: #f2f2f2;
}

.table-section {
    margin-bottom: 40px;
}

.filter-group, .sub-filter-group {
    margin-bottom: 10px;
}

input[type="text"] {
    margin-top: 10px;
    margin-bottom: 20px;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
}

.popupOverlay, .popupOverlays {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
}

.popupDiv {
    background: white;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
}

#loadingSpinner {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.table-responsive-md {
            height: 450px;
            overflow-y: scroll;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
            background-color: transparent;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .border {
            border: 1px solid #dee2e6;
        }

        .bg-dark {
            background-color: #343a40 !important;
        }

        .text-light {
            color: #f8f9fa !important;
        }


</style>
<body>
    <div>
        <h2>Company Total Earnings:<?php echo  $getEarning;?></h2>
    </div>
<h1>Pending Accounts</h1>
    <table>
        <tr>
            <th>Account ID</th>
            <th>Account Type</th>
            <th>Name</th>
            <th>Account Status</th>
        </tr>

        <?php 
              while ($account = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr onclick="fetchAccContent('<?php echo $account['acc_id']?>','accRespon')">
                <?php
                echo "<td>{$account['acc_id']}</td>";
                echo "<td>{$account['acc_type']}</td>";
    
                echo "<td>{$account['acc_name']}</td>";
                echo "<td>{$account['acc_status']}</td>";
                echo "</tr>";
            }
        
        ?>
    </table>
    <br>
    <hr>
    <h1>Active Accounts</h1>
    <table>
        <tr>
            <th>Account ID</th>
            <th>Account Type</th>
            <th>Name</th>
            <th>Account Status</th>
        </tr>
        <?php

        $sql = "SELECT * FROM `accounts` where  acc_status != 'pending 'AND acc_type != 'admin' ";
        $stmt = $db->query($sql);

        // Fetch results and display in the table
        while ($account = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
               <tr onclick="fetchAccContent('<?php echo $account['acc_id'] ?>','accAct')">
            <?php
         
            echo "<td>{$account['acc_id']}</td>";
            echo "<td>{$account['acc_type']}</td>";

            echo "<td>{$account['acc_name']}</td>";
            echo "<td>{$account['acc_status']}</td>";
            echo "</tr>";
        }
        ?>
        
    </table>

    <br>
    <hr>
    <h1>Loan Request</h1>
    <table>
        <tr>
            <th>Account Name</th>
            <th>Request Amount</th> 
            <th>Loan term</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php

        $sql = "SELECT 
        accounts.acc_name,
        accounts.acc_email,
        loan_requests.request_id,
        loan_requests.user_id,
        loan_requests.loan_id,
        loan_requests.requested_amount,
        loan_requests.loan_term,
        loan_requests.request_date,
        loan_requests.req_status 
    FROM 
        loan_requests
    LEFT JOIN 
        accounts ON loan_requests.user_id = accounts.acc_id
    WHERE 
        loan_requests.req_status = 'pending'
    ";
        $stmt = $db->query($sql);

        // Fetch results and display in the table
        if(!empty($stmt)){
        while ($lonReq = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            // echo "<td>{$lonReq['request_id']}</td>";
            ?>

           <td onclick = "fetchAccContent('<?php echo $lonReq['user_id'] ?>','accView')"><?php echo $lonReq['acc_name']?></td>;

            <?php
            echo "<td>{$lonReq['requested_amount']}</td>";
            echo "<td>{$lonReq['loan_term']}</td>";
?>
            <td><?php echo date('F d Y', strtotime($lonReq['request_date'])); ?></td>
<?php
            echo "<td>{$lonReq['req_status']}</td>";
            echo '<td>
            <form action="" id = "loanRequest">
            <input type="hidden" name="reqId" value="'.$lonReq["request_id"].'">
            <input type="hidden" name="userId" value="'.$lonReq["user_id"].'">
            <input type="hidden" name="loanId" value="'.$lonReq["loan_id"].'">
            <input type="hidden" name="amount" value="'.$lonReq["requested_amount"].'">
            <input type="hidden" name="term" value="'.$lonReq["loan_term"].'">
            <input type="hidden" name="email" value="'.$lonReq["acc_email"].'">
            <input type="button" class="responseButton1" data-action="approve" value="Approve">
            <input type="button" class= "submitRejection1" id="submitRejection1" value="Reject">
            <div id="popupOverlay1" class="popupOverlay">
            <div id="popupDiv1" class="popupDiv">
                <h3>Deactivate Reason</h3>
                <textarea id="rejectionReason" name = "reason" rows="4" cols="50"></textarea><br>
                <input type="button" class="responseButton1" data-action="reject" value="Reject">
                <input type="button" onclick = "closeRes()" id="cancelRejection1" class="cancelRejection1" value="Cancel">
            </div>
            </div>
            </form>
            </td>';
            echo "</tr>";
        }
    }else{
        echo ' <tr>
        <td colspan="7">No withdrawal requests found.</td>
            </tr>';
    }
        ?>
        
    </table>
        <br>

        <h1>Withdraw Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($withdrawRequests): ?>
                <?php foreach ($withdrawRequests as $request): ?>
                    <tr>
    
                        <td><?php echo htmlspecialchars($request['acc_name']); ?></td>
 
                        <td><?php echo htmlspecialchars($request['amount']); ?></td>
                        <td><?php echo date('F d Y', strtotime($request['date'])); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <?php echo '<td>
                          <form action="" id = "withForm">
                          <input type="hidden" name="Id" value="'.$request["id"].'">
                          <input type="hidden" name="userId" value="'.$request["user_id"].'">
                          <input type="hidden" name="savId" value="'.$request["sav_id"].'">
                          <input type="hidden" name="amount" value="'.$request["amount"].'">
                          <input type="hidden" name="email" value="'.$request["acc_email"].'">

                          <input type="button" class="response" data-action="approve" value="Approve">
                          <input type="button" class= "submitRejection1" id="submitRejection1s" onclick ="openOver()" value="Reject">
                          <div id="popupOverlay1s" class="popupOverlays">
                          <div id="popupDiv1" class="popupDiv">
                              <h3>withDraw Reject Reason</h3>
                              <textarea id="rejectionReason1" name = "reason" rows="4" cols="50"></textarea><br>
                              <input type="button" class="response" data-action="reject" value="Reject">
                              <input type="button" onclick = " closeOver()" id="cancelRejection1s" class="cancelRejection1" value="Cancel">
                          </div>
                          </div>
                          </form>
                        
                        </td>'
                        ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No withdrawal requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

        <br>
        <h1>Transactions</h1>
        <form id="filterForm">
        <label>
            <input type="radio" name="filter" value="all" checked onclick="filterTransac()">
            All
        </label>
        <label>
            <input type="radio" name="filter" value="loan" onclick="filterTransac()">
            Loan
        </label>
        <label>
            <input type="radio" name="filter" value="savings" onclick="filterTransac()">
            Savings
        </label>
        <hr>
        <label>
            <input type="radio" name="dateFilter" value="all" onclick="filterTransac()">
            All
        </label>
        <label>
            <input type="radio" name="dateFilter" value="this_month" onclick="filterTransac()">
            This Month
        </label>
        <label>
            <input type="radio" name="dateFilter" value="this_year" onclick="filterTransac()">
            This Year
        </label>

        <!-- Placeholder for Loan sub-types -->
        <div id="loanSubTypes" style="display:none;">
            <label>
                <input type="radio" name="subFilter" value="request" onclick="filterTransac()">
                Loan Request
            </label>
            <label>
                <input type="radio" name="subFilter" value="repayment" onclick="filterTransac()">
                Repayment
            </label>
        </div>

        <!-- Placeholder for Savings sub-types -->
        <div id="savingsSubTypes" style="display:none;">
            <label>
                <input type="radio" name="subFilter" value="withdrawal" onclick="filterTransac()">
                Withdrawal
            </label>
            <label>
                <input type="radio" name="subFilter" value="deposit" onclick="filterTransac()">
                Deposit
            </label>
        </div>
    </form>

    <input type="text" id="searchBar" oninput="filterTransac()" style ='height: 30px; width: 300px;' placeholder="Search by Account Name">

        <div class="table-responsive-md">
            <table class="table">
                <thead>
                    <tr class="bg-dark">
                        <th>Account Name</th>
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
                                <td><?php echo htmlspecialchars($transaction['acc_name']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['sub_type']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                <td><?php echo date('F d Y', strtotime($transaction['created_at'])); ?></td>
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
        </div>
        
   
    <div class="overlay" id="overlay2">
    <div class="view" id = 'view'>
   
    </div>

    </div>

    
    <div id="loadingOverlay">
        <div id="loadingSpinner"></div>
        <p>Sending email, please wait...</p>
    </div>

<script src="assets/dash.js"></script>
<script>

</script>
</body>
</html>
