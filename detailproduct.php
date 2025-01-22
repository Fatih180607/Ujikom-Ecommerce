<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $namaProduk = urldecode($_GET['id']); // Pastikan URL di-decode
        $stmt = $db->prepare("SELECT Nama_Produk, Deskripsi, Harga, Gambar FROM Produk WHERE Nama_Produk = :nama");
        $stmt->bindParam(':nama', $namaProduk, PDO::PARAM_STR);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            header("Location: home.php"); 
            exit;
        }
    } else {
        header("Location: home.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link rel="stylesheet" href="detail.css">
</head>
<body>
    <div class="product-detail">
        <img src="<?= htmlspecialchars($product['Gambar']) ?>" alt="<?= htmlspecialchars($product['Nama_Produk']) ?>" class="product-image">
        <h1><?= htmlspecialchars($product['Nama_Produk']) ?></h1>
        <p><?= htmlspecialchars($product['Deskripsi']) ?></p>
        <h3>Harga: Rp <?= number_format($product['Harga'], 0, ',', '.') ?></h3>
        <a href="home.php" class="back-button">Kembali ke Home</a>
    </div>
</body>
</html>