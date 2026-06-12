<?php

$name = isset($_POST['name']) ?$_POST['name'] : null;
$email = isset($_POST['email']) ?$_POST['email'] : null;
$password = isset($_POST['password']) ?$_POST['password'] : null;

$db = new PDO("mysql:host=localhost;dbname=b18_finalproject","root","");

$query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";

$stmt = $db->prepare($query);
$stmt->execute(array(
    ':name'=>$name,
    ':email'=>$email,
    ':password'=>password_hash($password, PASSWORD_BCRYPT),
    ':role'=>'user'
));


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User registered</title>
</head>
<body>
    <h1>User has been successfully registered.</h1>
    <h2><a href="./login-form.php">Click here to redirect to login page</a></h2>

</body>
</html>