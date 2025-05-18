<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "community web app";

// Create a connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Failed to connect to database: " . mysqli_connect_error());
}
?>
