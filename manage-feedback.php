<?php
require('header.php');

$comments = file_get_contents("http://localhost/finalproject/backend/index.php?action=getAllComment");
$comments = json_decode($comments, true);
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
    </style>
  </head>
  <body>
    <div class="container mx-auto py-4" style="max-width: 900px;">
      <div class="d-flex justify-content-between align-items-center mb-2 row p-4 pb-0 pt-0">
        <h1 class="h1 pb-0 pt-0 col-10 fs-3 fw-bold">Feedback</h1>
      </div>


      <div class="card mb-2 p-4">
        <table class="table"> 
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">User ID</th>
              <th scope="col">Comments</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($comments as $comment): ?>
              <div class="justify-content-between">
              <tr>
                <td><?= $comment['id']?></td> 
                <td><?= $comment['user_id']?></td> 
                <td><?= $comment['comment']?></td> 
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
