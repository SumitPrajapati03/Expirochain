<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Login | EXPIROCHAIN</title>

    <link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/register.css" />
    <link rel="stylesheet" href="css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  </head>

  <body>
    <header class="app-header">
      <div class="header-left">
        <img src="images/Logo.png" alt="EXPIROCHAIN Logo" />
      </div>
      <div class="header-right">
        <a href="register.php" class="login-btn">Register</a>
      </div>
    </header>

    <div class="page-title">
      <h2>Welcome Back</h2>
      <p>Sign in to continue</p>
    </div>

    <div class="page-wrapper">
      <div class="register-card">
        <form method="post" action="login_process.php">

          <div class="field">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter Email or Phone Number" required />
          </div>

          <div class="field password-field">
            <label>Password</label>
            <div class="password-wrapper">
              <input type="password" name="user_pass" id="password" placeholder="Enter Password" required />
              <span class="toggle-eye">
                <i class="fa-solid fa-eye"></i>
              </span>
            </div>
          </div>

          <div class="forgot-password">
            <a href="forgot_password.php">Forgot Password?</a>
          </div>

          <button class="register-btn">Login</button>
        </form>
      </div>
    </div>

    <script src="js/login.js"></script>
  </body>
</html>
