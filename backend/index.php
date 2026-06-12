<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=b18_finalproject","root","");
$action = isset($_REQUEST['action']) ?$_REQUEST['action'] : null;

switch ($action){
    case 'getAllExpenses':
    $query = "SELECT expenses.*, categories.category_name, payment.payment_name 
          FROM expenses
          LEFT JOIN categories ON expenses.category_id = categories.id
          LEFT JOIN payment ON expenses.payment_method_id = payment.id
          WHERE expenses.user_id = :user_id
          ORDER BY expenses.id DESC";
    $stmt =$db->prepare($query);
    $stmt->execute([]);
    $expenses =$stmt->fetchAll();
    echo json_encode($expenses);
    break;

    case 'getOneUser':
    $id = isset($_REQUEST['id']) ?$_REQUEST['id'] : null;
    $query = "SELECT * FROM users WHERE id=:id";
    $stmt =$db->prepare($query);
    $stmt->execute([':id' =>$id]);
    $user =$stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user);
    break;

    case 'getOneExpense':
    $id = isset($_REQUEST['id']) ?($_REQUEST['id']) : null;
    $user_id = isset($_REQUEST['user_id']) ?($_REQUEST['user_id']) : null;
    if (!$id || !$user_id) {
        echo json_encode(["error" => "Missing parameters"]);
        break;
    }
    $query = "SELECT * FROM expenses WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $user_id
    ]);
    $expense = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($expense ? $expense : []);
    break;
    

    case 'getOneCategory':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($category);
        break;

    case 'editUser':
        if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['role']) && isset($_POST['id'])){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $id = $_POST['id'];

        $updateQuery = "UPDATE users SET name=:name, email=:email, role=:role WHERE id=:id";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([
            'id'=>$id,
            'name'=>$name,
            'email'=>$email,
            'role'=>$role,
        
        ]);
        echo json_encode(['status'=>'success']);
        exit();                                                                                 
    }
    break;

    case 'changePassword':
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        if($password == $confirm_password){
            $updateQuery = "UPDATE users SET password=:password WHERE id=:id";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([
                ":password"=>password_hash($password, PASSWORD_BCRYPT),
                ":id"=>$id
            ]);
            echo json_encode(["status"=>"Success"]);
            exit;
        }
        break;

    case 'editCategoryBudget':
    $user_id = $_SESSION['user']['id']; 
    $query = "INSERT INTO budgets (user_id, category_id, budget_amount) 
        VALUES (:user_id, :category_id, :amount)
        ON DUPLICATE KEY UPDATE budget_amount = :amount";
    if (isset($_POST['category_id']) && isset($_POST['amount'])) {
        $category_id = $_POST['category_id'];
        $amount = $_POST['amount'];
        $updateQuery = "UPDATE budgets SET budget_amount=:amount WHERE category_id=:category_id AND user_id=:user_id";
        
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([
            'amount' => $amount,
            'category_id' => $category_id,
            'user_id' => $user_id
        ]);

        echo json_encode(['status' => 'success']);
        exit();
    }
    break;

    case 'editPaymentBudget':
    $user_id = $_SESSION['user']['id']; 
    if (isset($_POST['payment_id']) && isset($_POST['payment_amount'])) {
        $payment_id = $_POST['payment_id'];
        $payment_amount = $_POST['payment_amount'];
        $upsertQuery = "INSERT INTO budgets (user_id, payment_id, payment_amount) 
                            VALUES (:user_id, :payment_id, :payment_amount)
                            ON DUPLICATE KEY UPDATE payment_amount = :payment_amount";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([
            'payment_amount' => $payment_amount,
            'payment_id' => $payment_id,
            'user_id' => $user_id
        ]);

        echo json_encode(['status' => 'success']);
        exit();
    }
    break;

    case 'editExpense': 
    if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['amount']) && isset($_POST['category_id']) && isset($_POST['expense_date']) && isset($_POST['payment_method_id']) && isset($_POST['id'])){
       $title = $_POST['title'];
       $description = $_POST['description'];
       $amount = $_POST['amount'];
       $category_id = $_POST['category_id'];
       $expense_date = $_POST['expense_date'];
       $payment_method_id = $_POST['payment_method_id'];
       $id = $_POST['id'];

       $updateQuery = "UPDATE expenses SET title=:title, description=:description, amount=:amount, category_id=:category_id, expense_date=:expense_date, payment_method_id=:payment_method_id WHERE id=:id AND user_id=:user_id";
       $stmt =$db->prepare($updateQuery);
       $stmt->execute([
            'title'=>$title,
            'description'=>$description,
            'amount'=>$amount,
            'category_id'=>$category_id,
            'expense_date'=>$expense_date,
            'payment_method_id'=>$payment_method_id,
            'id'=>$id,
            'user_id' => $user_id
        ]);
        echo json_encode(['status'=>'success']);
        exit();                                                                                 
        }
        break;

    case 'addNewUser':
        $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $name = isset($_POST['name']) ?$_POST['name'] : null;
        $email = isset($_POST['email']) ?$_POST['email'] : null;
        $password = isset($_POST['password']) ?$_POST['password'] : null;
        $confirm_password = isset($_POST['confirm_password']) ?$_POST['confirm_password'] : null;
        $role = isset($_POST['role']) ?$_POST['role'] : null;
            if ($password ==$confirm_password){
            $stmt =$db->prepare($query);
                $stmt->execute([
                    ":name"=>$name,
                    ":email" =>$email,
                    ":password" =>password_hash($password, PASSWORD_BCRYPT),
                    ":role" =>$role
                ]);
                echo json_encode(["status"=>"Success"]);
            }
            break;
    
    case 'addNewExpense':
        $title =$_POST['title'];
        $description =$_POST['description'];
        $amount =$_POST['amount'];
        $category_id =$_POST['category_id'];
        $payment_method_id =$_POST['payment_method_id'];
        $expense_date =$_POST['expense_date'];

        $query = "INSERT INTO expenses (title, description, amount, category_id, expense_date, payment_method_id, user_id) VALUES (:title, :description, :amount, :category_id, :expense_date, :payment_method_id, :user_id)";

        $stmt =$db->prepare($query);
        $stmt->execute([
            ":title" =>$title,
            ":description" =>$description,
            ":amount" =>$amount,
            ":category_id" =>$category_id,
            ":expense_date" =>$expense_date,
            ":payment_method_id" =>$payment_method_id,
            ":user_id" =>$user_id
        ]);
        echo json_encode(["status" => "success"]);
        
        exit(); 
        break;
}