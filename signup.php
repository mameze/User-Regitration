<?php
include('./db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Culture MiniLibrary System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow">
          <h3 class="text-center mb-3">Sign Up</h3>
          <form action="signup_user.php" method="POST" id="signupForm">
            
            <div class="mb-3">
              <label for="firstname">First Name</label>
              <input type="text" name="firstname" id="firstname" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="lastname">Last Name</label>
              <input type="text" name="lastname" id="lastname" class="form-control">
            </div>

            <div class="mb-3">
              <label for="username">Username</label>
              <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="password">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="confirm_password">Confirm Password</label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Create Account</button>

            <p class="text-center mt-3"><a href="login.php">Back to login</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("signupForm").addEventListener("submit", function (e) {
        const firstname = document.getElementById("firstname").value.trim();
        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm_password").value;

        if (!firstname || !username || !email || !password || !confirmPassword) {
            e.preventDefault();
            Swal.fire("Error", "Please fill in all required fields.", "error");
            return;
        }

        if (!email.match(/^\S+@\S+\.\S+$/)) {
            e.preventDefault();
            Swal.fire("Error", "Please enter a valid email address.", "error");
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            Swal.fire("Error", "Password must be at least 6 characters.", "error");
            return;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            Swal.fire("Error", "Passwords do not match.", "error");
            return;
        }
    });
  </script>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <script>
      Swal.fire({
          title: "Account Created!",
          text: "You can now log in.",
          icon: "success",
          confirmButtonText: "OK"
      }).then(() => {
          window.location.href = "login.php";
      });
  </script>
  <?php endif; ?>

  <?php if (isset($_GET['limit_reached']) && $_GET['limit_reached'] == 1): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'User Limit Reached',
      text: 'Only 10 user accounts are allowed. Redirecting to login...',
      timer: 5000,
      showConfirmButton: false,
      willClose: () => {
        window.location.href = "login.php";
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>
