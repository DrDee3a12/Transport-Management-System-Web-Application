<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'db.php';

// Create new order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $customer = $_POST['customer_name'];
  $destination = $_POST['destination'];
  $cargo = $_POST['cargo_details'];
  $driver_id = $_POST['driver_id'] ?: 'NULL';
  $vehicle_id = $_POST['vehicle_id'] ?: 'NULL';

  $query = "INSERT INTO orders (customer_name, destination, cargo_details, driver_id, vehicle_id)
            VALUES ('$customer', '$destination', '$cargo', $driver_id, $vehicle_id)";
  $conn->query($query);

  if ($_POST['vehicle_id']) {
    $conn->query("UPDATE vehicles SET status='assigned' WHERE vehicle_id=$vehicle_id");
  }
}

// Delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $order_id = $_POST['order_id'];

  // Before deletion, release the vehicle (if assigned)
  $res = $conn->query("SELECT vehicle_id FROM orders WHERE order_id=$order_id");
  $row = $res->fetch_assoc();
  if ($row['vehicle_id']) {
    $conn->query("UPDATE vehicles SET status='available' WHERE vehicle_id={$row['vehicle_id']}");
  }

  // Now delete the order
  $conn->query("DELETE FROM orders WHERE order_id = $order_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders</title>
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
      text-align: center;
      color: #003366;
    }
    input, select, button {
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
    .delete-btn {
      background-color: #cc0000;
    }
    .delete-btn:hover {
      background-color: #a00000;
    }
    .back-btn {
      margin-top: 20px;
      background-color: #888;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Manage Orders</h2>

    <form method="POST">
      <input name="customer_name" placeholder="Customer Name" required>
      <input name="destination" placeholder="Destination" required>
      <input name="cargo_details" placeholder="Cargo Details" required>

      <select name="driver_id">
        <option value="">-- Assign Driver (optional) --</option>
        <?php
        $drivers = $conn->query("SELECT * FROM drivers WHERE driver_id NOT IN (SELECT driver_id FROM orders WHERE driver_id IS NOT NULL)");
        while ($row = $drivers->fetch_assoc()) {
          echo "<option value='{$row['driver_id']}'>{$row['name']}</option>";
        }
        ?>
      </select>

      <select name="vehicle_id">
        <option value="">-- Assign Vehicle (optional) --</option>
        <?php
        $vehicles = $conn->query("SELECT * FROM vehicles WHERE status = 'available'");
        while ($row = $vehicles->fetch_assoc()) {
          echo "<option value='{$row['vehicle_id']}'>{$row['vehicle_number']} ({$row['vehicle_type']})</option>";
        }
        ?>
      </select>

      <button name="create" type="submit">Create Order</button>
    </form>

    <h3>All Orders</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Destination</th>
        <th>Cargo</th>
        <th>Driver</th>
        <th>Vehicle</th>
        <th>Action</th>
      </tr>
      <?php
      $query = "SELECT o.*, d.name AS driver_name, v.vehicle_number AS vehicle_num 
                FROM orders o
                LEFT JOIN drivers d ON o.driver_id = d.driver_id
                LEFT JOIN vehicles v ON o.vehicle_id = v.vehicle_id";
      $res = $conn->query($query);
      while ($row = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['destination']}</td>
                <td>{$row['cargo_details']}</td>
                <td>" . ($row['driver_name'] ?: 'Unassigned') . "</td>
                <td>" . ($row['vehicle_num'] ?: 'Unassigned') . "</td>
                <td>
                  <form method='POST' style='margin:0'>
                    <input type='hidden' name='order_id' value='{$row['order_id']}'>
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
