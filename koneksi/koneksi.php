<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "";

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->autocommit(TRUE);
$conn->set_charset("utf8mb4");
