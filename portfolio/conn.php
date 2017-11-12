<?php
$servername = "sql.njit.edu";
$username = "bx34";
$password = "Xx0SnUuM";
$dbname = "bx34";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>