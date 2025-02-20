<?php
session_start();
$db = new PDO('sqlite:db/db.sqlite3');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Anda harus login terlebih dahulu!"]);
    exit();
}

$username = $_SESSION['username'];

// Hapus semua barang dari cart user yang login
$sql = "DELETE FROM Cart WHERE Username = :username";
$stmt = $db->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();

echo json_encode(["status" => "success", "message" => "Cart berhasil dikosongkan"]);
?>
