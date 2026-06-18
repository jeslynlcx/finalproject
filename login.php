<?php
session_start();

$name = isset($_POST['name']) ?$_POST['name'] : null;
$password = isset($_POST['password']) ?$_POST['password'] : null;

if(isset($_POST['name'])){
$db = new PDO("mysql:host=localhost;dbname=b18_finalproject","root","");

$query = "SELECT * FROM users WHERE name=:name";

$stmt = $db->prepare($query);
$stmt->execute(array(
    ':name'=>$name
));
$user = $stmt->fetchAll();
$is_password_match = password_verify($password, $user[0]['password']);
echo $is_password_match ? "<h1>Correct password!</h1>" : "<h1>Error!</h1>";

if($is_password_match){
    $_SESSION['user'] = $user[0];
    header("Location: index.php");
    
}
}else{
    echo "<h1>User is already logged in!</h1>";
    print_r($_SESSION['user']);    
    header("Location: login-form.php");
}

echo "<h2><a href='./login-form.php?logout=true'>Name/Password does not match</a></h2>";

?>
