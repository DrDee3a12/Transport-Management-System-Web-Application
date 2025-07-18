<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'db.php';

$error = ""; // to hold delete error message

// Add Vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $number = $_POST['vehicle_number'];
  $type = $_POST['vehicle_type'];
  $conn->query("INSERT INTO vehicles (vehicle_number, vehicle_type) VALUES ('$number', '$type')");
}

// Delete Vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $id = $_POST['vehicle_id'];
  try {
    $conn->query("DELETE FROM vehicles WHERE vehicle_id = $id");
  } catch (mysqli_sql_exception $e) {
    if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
      $error = "❌ This vehicle is currently assigned to an order and cannot be deleted.";
    } else {
      $error = "❌ Error deleting vehicle: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Vehicles</title>
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
    input, button, select {
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
    .back-btn {
      margin-top: 20px;
      background-color: #888;
    }
    .delete-btn {
      background-color: #cc0000;
    }
    .delete-btn:hover {
      background-color: #a00000;
    }
    .error {
      color: red;
      margin: 10px 0;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Manage Vehicles</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input name="vehicle_number" placeholder="Vehicle Number" required>
      <input name="vehicle_type" placeholder="Vehicle Type (e.g., Truck, Van)" required>
      <button name="add" type="submit">Add Vehicle</button>
    </form>

    <h3>All Vehicles</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Number</th>
        <th>Type</th>
        <th>Action</th>
      </tr>
      <?php
      $res = $conn->query("SELECT * FROM vehicles");
      while ($row = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$row['vehicle_id']}</td>
                <td>{$row['vehicle_number']}</td>
                <td>{$row['vehicle_type']}</td>
                <td>
                  <form method='POST' style='margin: 0;'>
                    <input type='hidden' name='vehicle_id' value='{$row['vehicle_id']}'>
                    <button name='delete' class='delete-btn' type='submit'>Delete</button>
                  </form>
                </td>
              </tr>";
      }
      ?>
    </table>

    <form action="dashboard.php">
      <button class="back-btn" type="submit">← Back to Dashboard</button>
    </form>
  </div>
</body>
</html>
