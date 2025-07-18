<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: login.php");
  exit;
}
include 'db.php';

$error = "";

// Add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $user = $_POST['username'];
  $pass = $_POST['password'];

  $exists = $conn->query("SELECT * FROM admin WHERE username='$user'");
  if ($exists->num_rows > 0) {
    $error = "❌ Username already exists.";
  } else {
    $conn->query("INSERT INTO admin (username, password, role) VALUES ('$user', '$pass', 'admin')");
  }
}

// Delete admin (not superadmin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $id = $_POST['admin_id'];
  $check = $conn->query("SELECT * FROM admin WHERE id=$id AND role='superadmin'");
  if ($check->num_rows > 0) {
    $error = "❌ Cannot delete superadmin account.";
  } else {
    $conn->query("DELETE FROM admin WHERE id = $id");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Admins</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('assets/background.webp') no-repeat center center fixed;
		background-size: cover;
      padding: 40px;
    }
    .card {
      background: white;
      padding: 25px;
      max-width: 700px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      color: #003366;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
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
    .delete-btn {
      background-color: #cc0000;
    }
    .delete-btn:hover {
      background-color: #a00000;
    }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }
    .error {
      color: red;
      text-align: center;
      font-weight: bold;
    }
    .back-btn {
      margin-top: 20px;
      background-color: #888;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Manage Admins</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input name="username" placeholder="Admin Username" required>
      <input name="password" placeholder="Password" required>
      <button name="add" type="submit">Add Admin</button>
    </form>

    <h3>All Admins</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
      <?php
      $res = $conn->query("SELECT * FROM admin");
      while ($row = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['role']}</td>
                <td>";
        if ($row['role'] !== 'superadmin') {
          echo "<form method='POST' style='margin:0'>
                  <input type='hidden' name='admin_id' value='{$row['id']}'>
                  <button name='delete' class='delete-btn' type='submit'>Delete</button>
                </form>";
        } else {
          echo "-";
        }
        echo "</td></tr>";
      }
      ?>
    </table>

    <form action="dashboard.php">
      <button class="back-btn" type="submit">← Back to Dashboard</button>
    </form>
  </div>
</body>
</html>
