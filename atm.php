<?php 
session_start(); 
include 'koneksi.php'; 

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Ambil saldo terbaru dari database
$result = $conn->query("SELECT saldo FROM users WHERE id=$user_id");
$user = $result->fetch_assoc();
$saldo = $user["saldo"];

// Proses transaksi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["aksi"])) {
        $aksi = $_POST["aksi"];

        // SETOR
        if ($aksi === "setor") {
            $jumlah = (int) $_POST["jumlah"];
            if ($jumlah > 0) {
                $conn->query("UPDATE users SET saldo = saldo + $jumlah WHERE id = $user_id");
                $conn->query("INSERT INTO transaksi (user_id, jenis, jumlah) VALUES ($user_id, 'Setor', $jumlah)");
            }
        }

        // TARIK
        if ($aksi === "tarik") {
            $jumlah = (int) $_POST["jumlah"];
            if ($jumlah > 0 && $jumlah <= $saldo) {
                $conn->query("UPDATE users SET saldo = saldo - $jumlah WHERE id = $user_id");
                $conn->query("INSERT INTO transaksi (user_id, jenis, jumlah) VALUES ($user_id, 'Tarik', $jumlah)");
            }
        }

        // TRANSFER
        if ($aksi === "transfer") {
            $penerima = $_POST["penerima"];
            $jumlah = (int) $_POST["jumlah"];

            // Cek user target
            $cek = $conn->query("SELECT * FROM users WHERE username='$penerima'");
            if ($cek->num_rows === 1) {
                $user_tujuan = $cek->fetch_assoc();
                $penerima_id = $user_tujuan['id'];

                if ($jumlah > 0 && $jumlah <= $saldo) {
                    // Kurangi saldo pengirim
                    $conn->query("UPDATE users SET saldo = saldo - $jumlah WHERE id = $user_id");
                    // Tambah saldo penerima
                    $conn->query("UPDATE users SET saldo = saldo + $jumlah WHERE id = $penerima_id");
                    // Simpan riwayat transfer
                    $conn->query("INSERT INTO transfer (pengirim_id, penerima_id, jumlah) VALUES ($user_id, $penerima_id, $jumlah)");
                }
            }
        }

        // Refresh saldo terbaru
        $saldo = $conn->query("SELECT saldo FROM users WHERE id=$user_id")->fetch_assoc()['saldo'];
    }
}

// Filter riwayat
$dari = $_GET['dari'] ?? null;
$sampai = $_GET['sampai'] ?? null;

if ($dari && $sampai) {
    $riwayat = $conn->query("
        SELECT * FROM transaksi 
        WHERE user_id = $user_id AND DATE(tanggal) BETWEEN '$dari' AND '$sampai' 
        ORDER BY tanggal DESC
    ");

    $riwayat_transfer = $conn->query("
        SELECT * FROM transfer 
        WHERE (pengirim_id = $user_id OR penerima_id = $user_id) 
        AND DATE(tanggal) BETWEEN '$dari' AND '$sampai' 
        ORDER BY tanggal DESC
    ");
} else {
    $riwayat = $conn->query("SELECT * FROM transaksi WHERE user_id=$user_id ORDER BY tanggal DESC");
    $riwayat_transfer = $conn->query("SELECT * FROM transfer WHERE pengirim_id=$user_id OR penerima_id=$user_id ORDER BY tanggal DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATM - <?= htmlspecialchars($username) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="atm-container">
        <h2>Selamat datang, <?= htmlspecialchars($username) ?> 👋</h2>
        <p>Saldo Anda: <strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong></p>

        <div class="actions">
            <form method="POST">
                <h3>Setor Tunai</h3>
                <input type="number" name="jumlah" placeholder="Jumlah setoran" min="1" required>
                <button type="submit" name="aksi" value="setor">Setor</button>
            </form>

            <form method="POST">
                <h3>Tarik Tunai</h3>
                <input type="number" name="jumlah" placeholder="Jumlah penarikan" min="1" required>
                <button type="submit" name="aksi" value="tarik">Tarik</button>
            </form>

            <form method="POST">
                <h3>Transfer</h3>
                <input type="text" name="penerima" placeholder="Username tujuan" required>
                <input type="number" name="jumlah" placeholder="Jumlah transfer" min="1" required>
                <button type="submit" name="aksi" value="transfer">Transfer</button>
            </form>
        </div>

        <form method="GET">
            <h3>Filter Riwayat</h3>
            <label>Dari: </label>
            <input type="date" name="dari" required>
            <label>Sampai: </label>
            <input type="date" name="sampai" required>
            <button type="submit">Filter</button>
        </form>

        <h3>Riwayat Transaksi</h3>
        <div class="riwayat">
            <?php
            if ($riwayat->num_rows == 0) {
                echo "<p>- Tidak ada transaksi -</p>";
            } else {
                echo "<ul>";
                while ($row = $riwayat->fetch_assoc()) {
                    echo "<li>{$row['tanggal']} — {$row['jenis']}: Rp " . number_format($row['jumlah'], 0, ',', '.') . "</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>

        <h3>Riwayat Transfer</h3>
        <div class="riwayat">
            <?php
            if ($riwayat_transfer->num_rows === 0) {
                echo "<p>- Tidak ada riwayat transfer -</p>";
            } else {
                echo "<ul>";
                while ($t = $riwayat_transfer->fetch_assoc()) {
                    // Tentukan arah transaksi
                    $arah = ($t['pengirim_id'] == $user_id) ? "Mengirim ke" : "Menerima dari";
                    // Cari username lawan transaksi
                    $target_id = ($t['pengirim_id'] == $user_id) ? $t['penerima_id'] : $t['pengirim_id'];
                    $nama_target = $conn->query("SELECT username FROM users WHERE id=$target_id")->fetch_assoc()['username'];
                    echo "<li>{$t['tanggal']} — $arah <b>$nama_target</b>: Rp " . number_format($t['jumlah'], 0, ',', '.') . "</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>

        <a href="logout.php" class="logout-btn">Keluar</a>
    </div>
</body>
</html>
