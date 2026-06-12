<?php
require('header.php');
$user_id = $_SESSION['user']['id'];

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $deleteQuery = "DELETE FROM expenses WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($deleteQuery);
    $stmt->execute([
        ":id" => $id,
        ":user_id" => $user_id
    ]);
    
    header("Location: manage-expenses.php");
    exit();
}

$query = "SELECT expenses.*, categories.category_name, payments.payment_name 
          FROM expenses
          LEFT JOIN categories ON expenses.category_id = categories.id
          LEFT JOIN payments ON expenses.payment_method_id = payments.id
          WHERE expenses.user_id = :user_id
          ORDER BY expenses.id DESC";

$stmt = $db->prepare($query);
$stmt->execute([":user_id" => $user_id]);
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
    <link rel="stylesheet" href="theme.css" />
<script>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>
    <style type="text/css">
      body {
        background: #f1f1f1;
      }
      .popup-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.6); 
          display: flex;
          justify-content: center;
          align-items: center;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.2s ease;
      }
      .popup-card{
          background: #fff;
          padding: 25px;
          border-radius: 15px;
          width: 100%;
          max-width: 450px;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      }
      .popup-overlay:target {
          opacity: 1;
          pointer-events: auto;
      }
    </style>
  </head>
  <body>
    <div class="container mx-auto my-3" style="max-width: 900px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1 p-3 pb-0 pt-0">Manage expense</h1>
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
                      <a href="#viewModal<?= $expense['id'] ?>" class="btn btn-primary btn-sm me-2"><i class="bi bi-eye"></i></a>

                      <div id="viewModal<?= $expense['id'] ?>" class="popup-overlay">
                        <div class="popup-card text-start" style="color: #333;">
                          
                          <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="fw-bold text-primary"><i class="bi bi-eye"></i> Details</span>
                            <a href="#" class="btn-close text-decoration-none" aria-label="Close"></a>
                          </div>
                          
                          <div style="font-size: 15px;">
                            <p class="mb-1"><strong>Title:</strong> <?= ($expense['title']) ?></p>
                            <p class="mb-1"><strong>Description:</strong> <?= ($expense['description']) ?></p>
                          </div>

                        </div>
                      </div>

                      <a href="manage-expenses-edit.php?id=<?=$expense['id']?>" class="btn btn-secondary btn-sm me-2"><i class="bi bi-pencil"></i></a>
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
        <a href="index.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
      </div>
    </div>

    <script src="theme.js"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
