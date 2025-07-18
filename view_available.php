<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Drivers & Vehicles</title>
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
      max-width: 800px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    h2 {
      color: #003366;
      text-align: center;
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
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
    }
    .back-btn:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Available Drivers</h2>
    <table>
      <tr>
        <th>Name</th>
        <th>License #</th>
        <th>Phone</th>
      </tr>
      <?php
      $query = "SELECT * FROM drivers WHERE driver_id NOT IN (SELECT driver_id FROM orders WHERE driver_id IS NOT NULL)";
      $res = $conn->query($query);
      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['name']}</td>
                  <td>{$row['license_number']}</td>
                  <td>{$row['phone']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='3'>No available drivers.</td></tr>";
      }
      ?>
    </table>

    <h2 style="margin-top: 40px;">Available Vehicles</h2>
    <table>
      <tr>
        <th>Number</th>
        <th>Type</th>
      </tr>
      <?php
      $query = "SELECT * FROM vehicles WHERE status = 'available'";
      $res = $conn->query($query);
      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['vehicle_number']}</td>
                  <td>{$row['vehicle_type']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='2'>No available vehicles.</td></tr>";
      }
      ?>
    </table>

    <form action="dashboard.php">
      <button class="back-btn" type="submit">← Back to Dashboard</button>
    </form>
  </div>
</body>
</html>
