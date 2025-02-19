<?php
session_start();

// Clear any existing sessions first
if (!isset($_POST['login'])) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Start a new session
    session_start();
}

include 'includes/database.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Add basic input sanitization
    $username = htmlspecialchars(strip_tags($username));
    
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = query($query);
    $row = $result->fetch_assoc();

    if ($result->num_rows > 0) {
        // Set session variables
        $_SESSION['user'] = $username;
        $_SESSION['id'] = $row['ID'];
        $_SESSION['logged_in'] = true;
        
        // Redirect to dashboard
        header("Location: Dashboard.php");
        exit();
    } else {
        $_SESSION['ERROR'] = 'Invalid Username or Password! Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>To do App</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <!-- Add Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <?php if (isset($_SESSION['ERROR'])) { ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?= $_SESSION['ERROR'] ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php unset($_SESSION['ERROR']); } ?>

                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success'] ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php unset($_SESSION['success']); } ?>

                    <h2 style="text-align: center;">
                      <i class="fas fa-user-circle mb-3"></i><br>Welcome Back</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                placeholder="Enter your username" required>
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" 
                                    placeholder="Enter your password" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary input-group-text" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mb-3" name="login">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </button>
                        <button type="reset" class="btn btn-secondary btn-block" name="reset">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </form>
                    <hr>
                    <p class="text-center copyright">&copy; 2025 Todo App. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      // Toggle between password and text type
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });

    function showLoading() {
        document.getElementById('loading-overlay').classList.add('active');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.remove('active');
    }

    // Add loading to form submission
    document.querySelector('form').addEventListener('submit', function() {
        showLoading();
    });

    // Add loading to reset button
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        showLoading();
        setTimeout(hideLoading, 500); // Hide after 500ms for reset
    });

    // Add loading to password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        showLoading();
        setTimeout(hideLoading, 300); // Hide after 300ms for password toggle
    });
    </script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>