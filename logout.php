<?php
session_start();

if(isset($_GET['logout'])){
    if($_GET['logout'] == "true"){
        unset($_SESSION['user']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
</head>
<body>
    <script>
        alert("You are logged out!")
        window.location.href = "http://localhost/finalproject/login-form.php";
    </script>
</body>
</html>