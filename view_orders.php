<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'db.php';

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_term'])) {
  $search = trim($_POST['search_term']);
  $searchQuery = "WHERE o.customer_name LIKE '%$search%'";
} else {
  $searchQuery = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Orders</title>
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
    input, button {
      padding: 10px;
      margin: 8px 0;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
      width: 100%;
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
  </style>
</head>
<body>
  <div class="card">
    <h2>View Orders</h2>

    <form method="POST">
      <input type="text" name="search_term" placeholder="Search by customer name" value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit">Search</button>
    </form>

    <h3><?php echo $search ? "Search Results for \"$search\"" : "All Orders"; ?></h3>

    <table>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Destination</th>
        <th>Cargo</th>
        <th>Driver</th>
        <th>Vehicle</th>
      </tr>
      <?php
      $query = "SELECT o.*, d.name AS driver_name, v.vehicle_number AS vehicle_num 
                FROM orders o
                LEFT JOIN drivers d ON o.driver_id = d.driver_id
                LEFT JOIN vehicles v ON o.vehicle_id = v.vehicle_id
                $searchQuery";
      $res = $conn->query($query);
      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['order_id']}</td>
                  <td>{$row['customer_name']}</td>
                  <td>{$row['destination']}</td>
                  <td>{$row['cargo_details']}</td>
                  <td>" . ($row['driver_name'] ?: 'Unassigned') . "</td>
                  <td>" . ($row['vehicle_num'] ?: 'Unassigned') . "</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No orders found.</td></tr>";
      }
      ?>
    </table>

    <form action="dashboard.php">
      <button class="back-btn" type="submit">← Back to Dashboard</button>
    </form>
  </div>
</body>
</html>
