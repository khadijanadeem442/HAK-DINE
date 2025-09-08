<?php
$conn = new mysqli('localhost', 'root', '', 'hak_dine');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
