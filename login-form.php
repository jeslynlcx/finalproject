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
    <div class="container my-5 mx-auto" style="max-width: 500px;">
      <h1 class="h1 pt-5 mb-4 text-center fw-bold fs-3">Login</h1>

      <div class="card p-4">
        <form method="POST" action="login.php">
          <div class="mb-2">
            <label for="name" class="visually-hidden">Name</label>
            <input
              type="text"
              class="form-control"
              id="name"
              name="name"
              placeholder="Name"
              required
            />
          </div>
          <div class="mb-2">
            <label for="password" class="visually-hidden">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              placeholder="Password"
              required
            />
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-dark">Login</button>
          </div>
        </form>
      </div>

      <div class="text-center gap-3 mx-auto pt-3">
        <a href="register-form.php" class="text-decoration-none fw-bold text-black">Don't have an account? Sign up here<i class="bi bi-hand-index-thumb"></i></a>
      </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
    <script src="theme.js"></script>
  </body>
</html>
