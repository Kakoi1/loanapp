<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/common.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="box">
            <h2>Login</h2>
            <div id="registerStatus"></div>
            <form action="login.php" method="post" id="loginForm">
                <input type="email" name="email" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="button" id="loging" value="Login">
                <a href="view/registerForm.php">Sign Up</a>
            </form>
        </div>    
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#loging").click(function () {
                var formData = $("#loginForm").serialize();
                $.ajax({
                    type: 'POST',
                    url: './index.php',
                    data: formData,
                    success: function(response){
                        if (response.trim() === "Admin Login successful!") {
                            alert(response);
                            window.location.href = "view/dashboard.php";
                        } else if(response.trim() === "Login successful!") {
                            alert(response);
                            window.location.href = "view/userDash.php";
                        } else {
                            $('#registerStatus').html(response);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
