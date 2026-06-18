<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=b18_finalproject","root","");
    $action = isset($_REQUEST['action']) ?$_REQUEST['action'] : null; /* If the key exists, $action gets the value*/

switch ($action){
    // GET
    case 'getAllExpenses':
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
    $query = "SELECT expenses.*, categories.category_name, payments.payment_name 
          FROM expenses
          LEFT JOIN categories ON expenses.category_id = categories.id
          LEFT JOIN payments ON expenses.payment_method_id = payments.id
          WHERE expenses.user_id = :user_id
          ORDER BY expenses.id DESC";
    $stmt =$db->prepare($query); /*Prepare a database query, help prevent SQL crash */ /*ready to run*/
    $stmt->execute([":user_id" => $user_id]); /*fills in the user_id,*/ /*run the query*/
    $expenses =$stmt->fetchAll(); /*gets all matching expense records*/ /*get the return*/
    echo json_encode($expenses); /*outputs them as JSON*/
    break;

    case 'getAllComment':
    $query = "SELECT * FROM comments ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([]);
    $comment = $stmt->fetchALL(PDO::FETCH_ASSOC);
    echo json_encode($comment);
    break;

    case 'getOneUser':
    $id = isset($_REQUEST['id']) ?$_REQUEST['id'] : null;
    $query = "SELECT * FROM users WHERE id=:id"; /*replaces :id with the value stored in $id*/
    $stmt =$db->prepare($query);
    $stmt->execute([':id' =>$id]);
    $user =$stmt->fetch(PDO::FETCH_ASSOC); /*returned as an associative array.*/
    echo json_encode($user);
    break;

    case 'getOneExpense':
    $id = isset($_REQUEST['id']) ?($_REQUEST['id']) : null;
    $user_id = isset($_REQUEST['user_id']) ?($_REQUEST['user_id']) : null;
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


    case'getPaymentSum':
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
    $paymentQuery = "SELECT payments.id AS payment_id,payments.payment_name, 
            IFNULL(( SELECT SUM(expenses.amount) FROM expenses WHERE expenses.payment_method_id = payments.id AND expenses.user_id = :user_id), 0) AS total_payment_amount,
            IFNULL(budgets.payment_amount, 1000) AS payment_limit
            FROM payments
            LEFT JOIN budgets ON payments.id = budgets.payment_id AND budgets.user_id = :user_id
            GROUP BY payments.id, payments.payment_name";
    $stmt = $db->prepare($paymentQuery);
    $stmt->execute([':user_id' => $user_id]);
    $payment_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($payment_results);
    break;

    case'getCategorySum':
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST ['user_id'] : null;
    $categoryQuery = "SELECT categories.id AS category_id, categories.category_name, 
            IFNULL((SELECT SUM(expenses.amount) FROM expenses WHERE expenses.category_id = categories.id AND expenses.user_id = :user_id), 0) AS total_category_amount,
            IFNULL(budgets.budget_amount, 1000) AS budget_limit
            FROM categories
            LEFT JOIN budgets ON categories.id = budgets.category_id AND budgets.user_id = :user_id
            GROUP BY categories.id, categories.category_name";
    $stmt = $db->prepare($categoryQuery);
    $stmt->execute([':user_id' => $user_id]);
    $category_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($category_results);
    break;

    // EDIT
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
    $updateQuery = "UPDATE budgets SET payment_amount=:payment_amount WHERE payment_id=:payment_id AND user_id=:user_id";

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
    $user_id = $_SESSION['user']['id'];
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

    // ADD
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
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
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

    case 'addComment':
    $comment =$_POST['comment'];
    $user_id =$_POST['user_id'];

    $query = "INSERT INTO comments (comment, user_id) VALUES (:comment, :user_id)";

    $stmt =$db->prepare($query);
    $stmt->execute([
        ":comment" =>$comment,
        ":user_id" =>$user_id
    ]);
    echo json_encode(["status" => "success"]);
    
    exit(); 
    break;
        
    //DELETE
    case'deleteExpense':
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
    $id = isset($_REQUEST['id']) ?$_REQUEST['id'] : null;
    if ($id && $user_id) {
    $deleteQuery = "DELETE FROM expenses WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($deleteQuery);
    $stmt->execute([
        ":id" => $id,
        ":user_id" => $user_id
    ]);
    
    echo json_encode(['status' => 'success']);
    exit();
    break;
}

    case 'deleteUser':
    if (isset($_POST['id'])) {
        $stmt = $db->prepare("DELETE FROM users WHERE id=:id");
        $stmt->execute([
            ":id" => $_POST['id']
        ]);
    }
    header("Location: http://localhost/finalproject/manage-user.php");
    exit();
    break;
    

}