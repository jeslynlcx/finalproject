<?php
require('header.php');

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $deleteQuery = "DELETE FROM expenses WHERE id=:id";
    $stmt = $db->prepare($deleteQuery);
    $stmt->execute([
        ":id"=>$id
    ]);
}

$query = "SELECT expenses.*,categories.category_name, payments.payment_name FROM b18_finalproject.expenses
LEFT JOIN categories
ON expenses.category_id = categories.id
LEFT JOIN payments
ON expenses.payment_method_id = payments.id";

$stmt = $db->prepare($query);
$stmt->execute([]);
$expenses = $stmt->fetchAll();
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
      .transaction-meta {
            font-size: 0.75rem;
            color: #94a3b8;
        }
    </style>
  </head>
  <body>
    <div class="container mx-auto my-5" style="max-width: 900px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Manage expense</h1>
      </div>


      <div class="card mb-2 p-4">
        <table class="table"> 
          <thead>
            <tr>
              <th scope="col">Title</th>
              <th scope="col">Amount</th>
              <th scope="col">Category</th>
              <th scope="col">Payment</th>
              <th scope="col">Date</th>
              <th scope="col" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($expenses as $expense): ?>
              <div class="justify-content-between">
              <tr>
                <td><?= $expense['title']?></td> 
                <td>RM<?= $expense['amount']?></td> 
                <td><?= $expense['category_name']?></td> 
                <td><?= $expense['payment_name']?></td> 
                <td><?= $expense['expense_date']?></td> 
                <td class="text-end">
                  <div class="buttons">
                    <a
                      href="post.php?id=<?=$expense['id']?>"
                      target="_self"
                      class="btn btn-primary btn-sm me-2"
                      ><i class="bi bi-eye"></i></a>
                    <a href="manage-expenses-edit.php?id=<?=$expense['id']?>"
                      class="btn btn-secondary btn-sm me-2"><i class="bi bi-pencil"></i></a>
                      <form method="POST" class="d-inline">
                    <button class="btn btn-danger btn-sm" type="submit" ><i class="bi bi-trash"></i></button>
                    <input type="hidden" name="id" value="<?= $expense['id']?>">
                    </form>
                  </div>
                </td>
              </tr>
            </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
  
      <div class="text-center">
        <a href="index.php" class="btn-sm text-black fw-bold text-decoration-none"
          ><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
