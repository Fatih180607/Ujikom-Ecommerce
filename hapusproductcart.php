<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $cart_id = intval($_GET['id']);
        $username = $_SESSION['username'];

        // Hapus item dari keranjang berdasarkan ID dan username
        $stmt = $db->prepare("DELETE FROM Cart WHERE ID = :cart_id AND Username = :username");
        $stmt->execute([
            ':cart_id' => $cart_id,
            ':username' => $username
        ]);
    }

    // Redirect kembali ke cart.php setelah penghapusan berhasil
    header("Location: cart.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>
