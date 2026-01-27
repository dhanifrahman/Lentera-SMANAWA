<?php
session_start();
include 'koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $pin = $_POST["pin"];

    $sql = "SELECT * FROM users WHERE username=? AND pin=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        header("Location: atm.php");
        exit;
    } else {
        $error = "Username atau PIN salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATM SMANAWA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2>👤 Login ke ATM SMANAWA</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="pin" placeholder="PIN" required maxlength="6">
            <button type="submit">Masuk</button>
        </form>
        <a href="index.html" class="back-home">← Kembali ke Home</a>
    </div>
</body>
</html>