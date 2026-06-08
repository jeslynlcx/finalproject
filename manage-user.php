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
    <style type="text/css">
      body {
        background: #f1f1f1;
      }
    </style>
  </head>
  <body>
    <div class="container mx-auto my-5" style="max-width: 700px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Manage Users</h1>
        <div class="text-end">
          <a href="manage-user-add.php" class="btn btn-primary btn-sm"
            >Add New User</a
          >
        </div>
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
                // if($user['role'] == "user"){
                //   $role_badge = "bg-success";
                // }else if ($user['role'] == 'editor'){
                //   $role_badge = "bg-info";
                // }else if ($user['role'] == 'admin'){
                //   $role_badge = "bg-primary";
                // }
                switch($user['role']){
                  case'user';
                  $role_badge = "bg-success";
                  break;
                  case'editor';
                  $role_badge = "bg-info";
                  break;
                  case'admin';
                  $role_badge = "bg-primary";
                  break;
                }
              ?>
            <tr>
              <th scope="row"><?= $user['id']?></th>
              <td><?= $user['name']?></td>
              <td><?= $user['email']?></td>
              <td><span class="badge <?= $role_badge ?>"><?=ucwords($user['role'])?></span></td>
              <td class="text-end">
                <div class="buttons">
                  <a href="manage-user-edit.php?id=<?=$user['id']?>"
                    class="btn btn-success btn-sm me-2"
                    ><i class="bi bi-pencil"></i></a>
                  <a
                    href="manage-user-changepwd.php?id=<?=$user['id']?>"
                    class="btn btn-warning btn-sm me-2"
                    ><i class="bi bi-key"></i></a>
                  <form method="POST" class="d-inline">
                  <button class="btn btn-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
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
        <a href="index.php" class="btn btn-link btn-sm"
          ><i class="bi bi-arrow-left"></i> Back to Dashboard</a
        >
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>

  </body>
</html>
