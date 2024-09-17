<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="../css/common.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <div id="registerStatus"></div>
    <div class="container">
        <div class="box">
            <h1>REGISTRATION</h1>
            <form action="" method="post" id="registerForm">
                <hr>
                <div class="row">
                    <div class="column">
                        <h3>User Information</h3>
                        <label for="accType">Account Type</label>
                        <select name="accType" id="accType">
                            <option value=""></option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                        </select><br>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br>
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required><br>
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender" required>
                            <option value=""></option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="none">Rather not say</option>
                        </select><br>
                        <label for="birthday">Birthday:</label>
                        <input type="date" id="birthday" name="birthday" onchange="calculateAge()" required><br>
                        <label for="age">Age:</label>
                        <input type="number" id="age" name="age" required readonly><br>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br>
                        <label for="contact_number">Contact Number:</label>
                        <input type="tel" id="contact_number" name="contact_number" placeholder="63+" pattern="[0-9]{11}" required><br>
                        <label for="pass">Password</label>
                        <input type="password" id="pass" name="pass" required><br>
                        <label for="repass">Retype Password:</label>
                        <input type="password" id="repass" name="repass" required><br>
                    </div>
                    <div class="column">
                        <h3>Bank Details</h3>
                        <label for="bank_name">Bank Name:</label>
                        <input type="text" id="bank_name" name="bank_name" required><br>
                        <label for="bank_account_number">Bank Account Number:</label>
                        <input type="text" id="bank_account_number" name="bank_account_number" required><br>
                        <label for="card_holder_name">Card Holder's Name:</label>
                        <input type="text" id="card_holder_name" name="card_holder_name" required><br>
                        <h3>Tax Information</h3>
                        <label for="tin_number">TIN Number:</label>
                        <input type="text" id="tin_number" name="tin_number" required><br>
                        <h3>Employment Details</h3>
                        <label for="company_name">Company Name:</label>
                        <input type="text" id="company_name" name="company_name" required><br>
                        <label for="company_address">Company Address:</label>
                        <input type="text" id="company_address" name="company_address" required><br>
                        <label for="company_phone">Company Phone Number:</label>
                        <input type="tel" id="company_phone" name="company_phone" required><br>
                        <label for="position">Position:</label>
                        <input type="text" id="position" name="position" required><br>
                        <label for="monthly_earnings">Monthly Earnings:</label>
                        <input type="number" id="monthly_earnings" name="monthly_earnings" required><br>
                    </div>
                </div>
                <hr>
                <br>
                <h3>Uploads (Required)</h3>
                <label for="proof_of_billing">Proof of Billing:</label>
                <input type="file" id="proofOfBilling" name="proof_of_billing" required accept="image/*"><br>
                <img id="previewProofOfBilling" src="#" alt="Preview" style="display: none; max-width: 200px;"><br>
                <label for="valid_id">Valid ID</label>
                <input type="file" id="validId" name="valid_id" required accept="image/*"><br>
                <img id="previewValidId" src="#" alt="Preview" style="display: none; max-width: 200px;"><br>
                <label for="coe">COE (Certificate of Employment)</label>
                <input type="file" id="coe" name="coe" required accept="image/*"><br>
                <img id="previewCoe" src="#" alt="Preview" style="display: none; max-width: 200px;"><br>
                <input type="button" id="signupButton" name="register" value="Register">
                <a href="../index.php">login</a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./assets/regScript.js"></script>
</body>
</html>
