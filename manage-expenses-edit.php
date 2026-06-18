<?php
if(!isset($_SESSION['user'])){
    header("Location: manage-expenses.php");
    exit;
}
$id = $_GET['id'];
$user_id = $_SESSION['user']['id'];
$expense = file_get_contents("http://localhost/finalproject/backend/index.php?action=getOneExpense&id=$id&user_id=$user_id");
$expense = json_decode($expense, true);
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
    <link rel="stylesheet" href="theme.css" />
    <script>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style type="text/css">
      body { background: #f1f1f1; }
      .form-label {
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
        <h1 class="h1 p-3 pb-0 fs-3 fw-bold">Edit Expenses</h1>
      </div>
        <div class="card mb-2 p-4">
            <form method="POST" id='editExpenseForm'>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= ($expense['title'] ?? '') ?>" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= ($expense['description'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="text" class="form-control amount" id="amount" name="amount" value="<?= ($expense['amount'] ?? '')?>" required/>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="payment_method_id" class="form-label">Payment Method</label>
                <select name="payment_method_id" id="payment_method_id" class="form-select">
                  <option value="">Select an option</option>
                  <option value="1" <?= (($expense['payment_method_id'] ?? 0) === 1) ? 'selected' : '' ?>>Cash</option>
                  <option value="2" <?= (($expense['payment_method_id'] ?? 0) === 2) ? 'selected' : '' ?>>Debit Card</option>
                  <option value="3" <?= (($expense['payment_method_id'] ?? 0) === 3) ? 'selected' : '' ?>>Touch & Go</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="expense_date" class="form-label">Date</label>
                <input type="date" class="form-control date" id="expense_date" name="expense_date" value="<?= ($expense['expense_date'] ?? '') ?>">
              </div>
            </div>
            <div class="mb-3 ">
                <label for="category_id" class="form-label">All Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">Select an option</option>
                  <option value="1" <?= (($expense['category_id'] ?? 0) === 1) ? 'selected' : '' ?>>Food</option>  <!--??Get category_id or use 0, === 1 Check if it is Food, ? : If yes → "selected", no → ""-->
                  <option value="2" <?= (($expense['category_id'] ?? 0) === 2) ? 'selected' : '' ?>>Transport</option>
                  <option value="3" <?= (($expense['category_id'] ?? 0) === 3) ? 'selected' : '' ?>>Entertainment</option>
                  <option value="4" <?= (($expense['category_id'] ?? 0) === 4) ? 'selected' : '' ?>>Education</option>
                  <option value="5" <?= (($expense['category_id'] ?? 0) === 5) ? 'selected' : '' ?>>Investment</option>
                  <option value="6" <?= (($expense['category_id'] ?? 0) === 6) ? 'selected' : '' ?>>Utility</option>
                  <option value="7" <?= (($expense['category_id'] ?? 0) === 7) ? 'selected' : '' ?>>Shopping</option>
                  <option value="8" <?= (($expense['category_id'] ?? 0) === 8) ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-dark">Update</button>
            </div>
            </form>
        </div>
      
      <div class="text-center">
        <a href="manage-expenses.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Back to All</a>
      </div>
    </div>
    <script src="theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
      $('#editExpenseForm').on('submit', (function(event){
        event.preventDefault();
        $.ajax({
          url: "http://localhost/finalproject/backend/index.php",
          type: "POST", 
          data: {
            action: "editExpense",
            title: $('#title').val(),
            description: $('#description').val(),
            amount: $('#amount').val(),
            category_id: $('#category_id').val(),
            payment_method_id: $('#payment_method_id').val(),
            expense_date: $('#expense_date').val(),
            id: <?= ($id) ?>
          },
          success: function(response){
              alert("Successfully Edited!");
              window.location.href = "http://localhost/finalproject/manage-expenses.php";
          },
          error: function(xhr, status, error){
                console.log("Error: ", error)
          }
        });
      }));
    </script>
  </body>
</html>