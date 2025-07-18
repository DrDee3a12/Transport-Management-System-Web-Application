<?php
$conn = new mysqli('localhost', 'root', '', 'tms');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>
