<?php
$host = "localhost";
$user = "root";       // default XAMPP
$pass = "";           // kosongkan jika belum diatur password
$db   = "atm_smanawa";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
