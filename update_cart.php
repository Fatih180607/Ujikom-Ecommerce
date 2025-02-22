<?php
session_start();
$db = new PDO('sqlite:db/db.sqlite3');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['username'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = 'index.php';
    </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    if ($quantity < 1) {
        echo "<script>
            alert('Jumlah tidak boleh kurang dari 1.');
            window.history.back();
        </script>";
        exit();
    }
    
    $sql = "UPDATE Cart SET Quantity = :quantity WHERE ID = :cart_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    $stmt->execute();

    echo "<script>
        alert('Jumlah berhasil diperbarui!');
        window.location.href = 'cart.php';
    </script>";
}
?>
