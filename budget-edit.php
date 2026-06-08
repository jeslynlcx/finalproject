<?php
require('header.php');

$displayAmount = isset($expenses['amount']) ? abs($expenses['amount']) : '';
$id = $_GET['id'];
$expenses = file_get_contents("http://localhost/finalproject/backend/index.php?action=getOnePost&id=$id");
$expenses = json_decode($expenses, true);
// if(isset($_GET['id'])){
//     $id = $_GET['id'];
// $query = "SELECT * FROM expenses WHERE id=:id";

// $stmt = $db->prepare($query);
// $stmt->execute([
//     ':id' =>$id
// ]);
// $expenses = $stmt->fetchAll();
// }
// if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['amount']) && isset($_POST['id'])){
//     $title = $_POST['title'];
//     $description = isset($_POST['description']) ? $_POST['description'] : "review";
//     $amount = $_POST['amount'];
//     $expenseDate = $_POST['expenseDate'];
//     $id = $_POST['id'];

//     $query = "UPDATE expenses SET title=:title, description=:description, amount=:amount, expenseDate=:expenseDate WHERE id=:id";
//     $stmt = $db->prepare($query);
//     $stmt->execute([
//         ":title"=>$title,
//         ":description"=>$description,
//         ":amount"=>$amount,
//         ":expenseDate"=>$expenseDate,
//         ":id"=>$id
//     ]);
//     header("Location: manage-post.php");
// }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple CMS</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
    <style type="text/css">
      body {
        background: #f1f1f1;
      }
      .form-control,.form-select{
        border-radius: 20px;
        background-color: #f8f9fa5e;
        border-color: #4f8cff;
        box-shadow: 0 0 0 3px rgba(79,140,255,.15);
      }
      .form-label{
        padding-left: 10px;
        margin-bottom: 0px;
        font-family: bold;
        font-weight: bold;
        font-size: 18px;
      }
      
    </style>
  </head>
  <body>
    <div class="container mx-auto my-5" style="max-width: 700px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Edit Expenses</h1>
      </div>
        <div class="card mb-2 p-4">
            <form method="POST" id='editPostForm'>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $expenses['title']?>"required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= $expenses['description']?>" required/>
            </div>
             <div class="mb-3">
          <label for="amount" class="form-label">Amount</label>
          <input type="text" class="form-control amount" id="amount" name="amount" value="<?= $expenses['amount']?>" required/>
          </div>
          <div class="row">
          <div class="col-md-6 mb-3">
          <label for="payment_method" class="form-label">Payment Method</label>
          <select name="payment_method" id="payment_method" class="form-select" value="<?= $expenses['payment_name']?>">
            <option value="cash">Cash</option>
            <option value="credit card">Credit Card</option>
            <option value="TNG">Touch & Go</option>
          </select>
          </div>
          <div class="col-md-6 mb-3">
          <label for="expense_date" class="form-label">Date</label>
          <input type="date" class="form-control date" id="expense_date" name="expense_date" value="<?= $expenses['expense_date']?>">
          </div>
          </div>
          <div class="mb-3 ">
          <label for="category" class="form-label">All Category</label>
          <select name="category" id="category" class="form-select" value="<?= $expenses['category_id']?>">
            <option value="">Select an option</option>
            <option value="food">Food</option>
            <option value="transport">Transport</option>
            <option value="entertaiment">Entertaiment</option>
            <option value="education">Education</option>
            <option value="investment">Investment</option>
            <option value="utility">Utility</option>
            <option value="income">Income</option>
            <option value="other">Other</option>
          </select>
          </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
      <div class="text-center">
        <a href="manage-post.php" class="btn btn-link btn-sm"
          ><i class="bi bi-arrow-left"></i> Back to Posts</a
        >
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script>
        $('#editPostForm').on('submit', (function(event){
          event.preventDefault();
          console.log("Form submitted");
          $.ajax({
            url: "http://localhost/finalproject/backend/index.php",
            type: "POST", 
            data: {
              action: "editPost",
              title: $('#title').val(),
              description: $('#description').val(),
              amount: $('#amount').val(),
              category_id: $('#category_id').val(),
              expense_date: $('#expense_date').val(),
              id: <?= $id ?>
            },
            success: function(response){
                console.log(response);
                alert("Successfully Edited!")
                window.location.href = "http://localhost/finalproject/manage-post.php";

            },
            error: function(xhr, status, error){
                console.log("Error: ", error)
                console.log("Error: ", xhr)
                console.log("Error: ", error)
            }
          })
        }))
      </script>
  </body>
</html>
