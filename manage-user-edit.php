<?php
require('header.php');
$id = $_GET['id'];
$user = file_get_contents("http://localhost/finalproject/backend/index.php?action=getOneUser&id=$id");
$user = json_decode($user, true);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple finalproject</title>
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
        <h1 class="h1 p-3 pb-0 fs-3 fw-bold">Edit User</h1>
      </div>
      <div class="card mb-2 p-4">

        <form method="POST" id="editUserForm">
          <div class="mb-3">
            <div class="row">
              <div class="col">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= ($user['name'] ?? '')?>" required/>
              </div>
              <div class="col">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= ($user['email'] ?? '')?>" />
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" id="role" name="role" required>
              <option value="">Select an option</option>
              <option value="user"<?= ($user['role'] ?? '') == 'user' ?'selected' :  ''?>>User</option>
              <option value="admin"<?= ($user['role'] ?? '') == 'admin' ?'selected' : ''?>>Admin</option>
            </select>
          </div>
          <input type="hidden" name="id" value="<?= $user['id']?>">
          <div class="d-grid">
            <button type="submit" class="btn btn-dark">Update</button>
          </div>
        </form>

      </div>
      <div class="text-center">
        <a href="manage-user.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Back to Users</a
        >
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
        $('#editUserForm').on('submit', (function(event){
          event.preventDefault();
          console.log("Form submitted");
          $.ajax({
            url: "http://localhost/finalproject/backend/index.php",
            type: "POST", 
            data: {
              action: "editUser",
              name: $('#name').val(),
              email: $('#email').val(),
              role: $('#role').val(),
              id: <?= $id ?>
            },
            success: function(response){
                console.log(response);
                alert("Successfully Edited!")
                window.location.href = "http://localhost/finalproject/manage-user.php";

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
