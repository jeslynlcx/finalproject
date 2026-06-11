<?php
require('header.php');
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
      body {
        background: #f1f1f1;
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
        <h1 class="h1 p-3 pb-0">Add New</h1>
      </div>
      <div class="card mb-2 p-4">
        <form method="POST" id='addPostForm'>
        <input type="hidden" name="action" value="addNewExpense">
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required/>
          </div>
          <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" />
            </div>
          <div class="mb-3">
          <label for="amount" class="form-label">Amount</label>
          <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" placeholder="RM00.00" required/>
          </div>
          <div class="row">
          <div class="col-md-6 mb-3">
          <label for="payment_method_id" class="form-label">Payment Method</label>
          <select name="payment_method_id" id="payment_method_id" class="form-select" value="<?= $expenses['payment_name']?>" required>
            <option value="1">Cash</option>
            <option value="2">Credit Card</option>
            <option value="3">Touch & Go</option>
          </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" class="form-control date" id="expense_date" name="expense_date" required>
          </div>
          </div>
          <div class="mb-3 ">
          <label for="category_id" class="form-label">All Category</label>
          <select name="category_id" id="category_id" class="form-select" value="<?= $expenses['category_name']?>" required>
            <option value="">Select an option</option>
            <option value="1">Food</option>
            <option value="2">Transport</option>
            <option value="3">Entertaiment</option>
            <option value="4">Education</option>
            <option value="5">Investment</option>
            <option value="6">Utility</option>
            <option value="7">Income</option>
            <option value="8">Other</option>
          </select>
          </div>
          </div>
          <div class="mb-3">
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Add</button>
          </div>
        </form>
      </div>
      <div class="text-center">
        <a href="index.php" class="btn-sm text-black fw-bold text-decoration-none"
          ><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
      </div>
      
    </div>
    <script src="theme.js"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
      $('#addPostForm').on('submit', (function(event){
          event.preventDefault();
          console.log("Form submitted");
          $.ajax({
            url: "http://localhost/finalproject/backend/index.php",
            type: "POST",
            data: {
              action: "addNewExpense",
              title: $('#title').val(),
              description: $('#description').val(),
              amount: $('#amount').val(),
              category_id: $('#category_id').val(),
              payment_method_id: $('#payment_method_id').val(),
              expense_date: $('#expense_date').val(),
            },
          success: function(response){
                console.log(response);
                alert("Successfully Added!")
                window.location.href = "http://localhost/finalproject/manage-expenses.php";

            },
            error: function(xhr, status, error){
                console.log("Error: ", error)
                console.log("Error: ", xhr)
                console.log("Error: ", status)
            }
          })
      }))
    </script>
    
  </body>
</html>
