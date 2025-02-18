<?php
session_start();
if (!isset($_SESSION['Username'])) {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

if (isset($_POST['produk_id']) && isset($_POST['jumlah'])) {
    $namaProduk = $_POST['produk_id'];
    $jumlah = intval($_POST['jumlah']);
    $username = $_SESSION['Username'];

    $stmt = $db->prepare("SELECT ID FROM Produk WHERE Nama_Produk = ?");
    $stmt->execute([$namaProduk]);
    $produk = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produk) {
        $produk_id = $produk['ID'];

        $stmt = $db->prepare("SELECT Jumlah FROM Cart WHERE Username = ? AND Produk_ID = ?");
        $stmt->execute([$username, $produk_id]);
        $existing_cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_cart) {
            $new_jumlah = $existing_cart['Jumlah'] + $jumlah;
            $update_stmt = $db->prepare("UPDATE Cart SET Jumlah = ? WHERE Username = ? AND Produk_ID = ?");
            $update_stmt->execute([$new_jumlah, $username, $produk_id]);
        } else {
            $insert_stmt = $db->prepare("INSERT INTO Cart (Username, Produk_ID, Jumlah) VALUES (?, ?, ?)");
            $insert_stmt->execute([$username, $produk_id, $jumlah]);
        }

        header("Location: cart.php");
        exit;
    } else {
        echo "Produk tidak ditemukan.";
    }
}
?>
