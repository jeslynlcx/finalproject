<?php
require("header.php");
$query = "SELECT * FROM users ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll();
    
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
    <div class="container mx-auto my-5" style="max-width: 900px;">
      <div class="d-flex justify-content-between align-items-center pb-2">
        <h1 class="h1 p-3 pb-0 fs-3 fw-bold">Manage Users</h1>
        <a href="manage-user-add.php" class="btn btn-dark btn-sm text-end">Add New User</a>
      </div>
      <div class="card mb-2 p-4">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Role</th>
              <th scope="col" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($users as $user): ?>
              <?php
                $role_badge = "";
                switch($user['role']){
                  case'user';
                  $role_badge = "role-background";
                  break;
                  case'admin';
                  $role_badge = "role-background";
                  break;
                }
              ?>
            <tr>
              <th scope="row"><?= $user['id']?></th>
              <td><?= $user['name']?></td>
              <td><?= $user['email']?></td>
              <td><span class="badge <?= $role_badge ?>"><?=$user['role']?></span></td>
              <td class="text-end">
                <div class="buttons">
                  <a href="manage-user-edit.php?id=<?=$user['id']?>"class="btn btn-dark btn-sm me-2"><i class="bi bi-pencil-square"></i></a>
                  <a href="manage-user-changepwd.php?id=<?=$user['id']?>" class="btn btn-dark btn-sm me-2"><i class="bi bi-key"></i></a>
                  <form method="POST" action="http://localhost/finalproject/backend/index.php?action=deleteUser" class="d-inline">
                  <button class="btn btn-dark btn-sm" type="submit"><i class="bi bi-trash"></i></button>
                  <input type="hidden" name="id" value="<?= $user['id']?>">
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="text-center">
        <a href="index.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a
        >
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
