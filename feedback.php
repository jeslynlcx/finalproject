<?php
require('header.php');
$user_id = $_SESSION['user']['id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - Money Manager</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="theme.css" />
    <script>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style>
        body {
            position: absolute;
            top: 20%;
            left: 28%;
            width: 100%;
            max-width: 650px;
            padding: 15px;
            box-sizing: border-box;
            text-align:center;
        }
        .card{
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        textarea{
            height:150px ;
            resize: none ;
        }
    </style>
</head>
<body>
    <div class="card mb-2 p-4">
        <?php if($_SESSION['user']['role'] == "admin" ): ?>
            <a href="manage-feedback.php" class="text-black"><i class="bi bi-info-circle-fill"></i></a>
        <?php endif; ?>
        <h2 class="fw-bold fs-3">Give Feedback</h2>
        <form method="POST" id="addComment">
            <div class="mb-3">
                    <label for="comment" class="form-label fw-bold">Your Comment (Anonymous):</label>
                    <textarea type="text" class="form-control" id="comment" name="comment" placeholder="Help us improve, share your thoughts or report issues..."></textarea>
            </div>
                <button type="submit" class="btn-dark">Submit Feedback</button>

        </form>
    </div>
    <div class="text-center gap-3 mx-auto pt-1">
        <a href="index.php" class="text-decoration-none fw-bold text-black"><i class="bi bi-arrow-left-circle"></i> Back to dashboard</a>
    </div>
    <script src="theme.js"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$('#addComment').on('submit', (function(event){
          event.preventDefault();
          console.log("Comment submitted");
          $.ajax({
            url: "http://localhost/finalproject/backend/index.php",
            type: "POST",
            data: {
              action: "addComment",
              comment: $('#comment').val(),
              user_id: "<?= $user_id ?>",
            },
          success: function(response){
                console.log(response);
                alert("Successfully Added!")
                window.location.href = "http://localhost/finalproject/index.php";

            },
            error: function(xhr, status, error){
                console.log("Error: ", error)
                console.log("Error: ", xhr)
                console.log("Error: ", status)
            }
          })
      }))
</script>
<script src="theme.js"></script>

</body>
</html>
