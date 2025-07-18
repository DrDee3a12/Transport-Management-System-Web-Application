<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['username'];
  $pass = $_POST['password'];

  $res = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");
  if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $_SESSION['username'] = $user;
    $_SESSION['role'] = $row['role'];
  } else {
    header("Location: login.php?error=1");
    exit;
  }
}

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('assets/background.webp') no-repeat center center fixed;
		background-size: cover;

      margin: 0;
      padding: 0;
      color: #333;
      min-height: 100vh;
    }
    .dashboard {
      background: white;
      max-width: 600px;
      margin: 40px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    h1 {
      color: #003366;
      text-align: center;
    }
    .menu button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      background-color: #007acc;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    .menu button:hover {
      background-color: #005f99;
    }
    form {
      margin: 0;
    }
    .logout {
      text-align: center;
      margin-top: 20px;
    }
    .logout button {
      background-color: #cc0000;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <div class="menu">
      <form action="manage_vehicles.php"><button type="submit">🚚 Manage Vehicles</button></form>
      <form action="manage_drivers.php"><button type="submit">👷 Manage Drivers</button></form>
      <form action="manage_orders.php"><button type="submit">📦 Manage Orders</button></form>
      <form action="view_orders.php"><button type="submit">📄 View Orders</button></form>
      <form action="view_available.php"><button type="submit">✅ Available Drivers & Vehicles</button></form>

      <?php if ($_SESSION['role'] === 'superadmin') { ?>
        <form action="manage_admins.php"><button type="submit">👤 Manage Admins</button></form>
      <?php } ?>
    </div>

    <div class="logout">
      <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>
</body>
</html>
