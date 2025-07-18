<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - TMS by Zalan</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('assets/login.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .title {
      font-size: 48px;
      color: #ffffff;
      font-weight: bold;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.6);
      margin-bottom: 40px;
    }
    .login-box {
      background: rgba(255,255,255,0.95);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
    }
    h1 {
      text-align: center;
      color: #003366;
      margin-bottom: 20px;
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    button {
      background-color: #007acc;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #005f99;
    }
    .error {
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="title">Transport Management System</div>

  <div class="login-box">
    <h1>Login</h1>
    <form method="POST" action="dashboard.php">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <?php
    if (isset($_GET['error'])) {
      echo "<p class='error'>Invalid username or password</p>";
    }
    ?>
  </div>
</body>
</html>
