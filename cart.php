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

$username = $_SESSION['username'];
$sql = "SELECT Cart.ID, Produk.Nama_Produk, Cart.Size, Cart.Quantity, SizeProduct.Harga, Produk.Gambar
        FROM Cart
        JOIN Produk ON Cart.ID_Product = Produk.ID
        JOIN SizeProduct ON Cart.Size = SizeProduct.Size_Produk AND Cart.ID_Product = SizeProduct.ID_Product
        WHERE Cart.Username = :username";
$stmt = $db->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link rel="icon" type="image/png" href="gambar/cart_icon.png"/>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <div class="cart-container">
        <h1>Keranjang Belanja</h1>

        <?php if (empty($cart_items)): ?>
            <p class="empty-cart">Keranjang Anda kosong.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Size</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_cart = 0;
                    foreach ($cart_items as $item):
                        $total_price = $item['Harga'] * $item['Quantity'];
                        $total_cart += $total_price;
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?= htmlspecialchars($item['Gambar']) ?>" alt="<?= htmlspecialchars($item['Nama_Produk']) ?>">
                                <span><?= htmlspecialchars($item['Nama_Produk']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($item['Size']) ?></td>
                        <td>Rp <?= number_format($item['Harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($item['Quantity']) ?></td>
                        <td>Rp <?= number_format($total_price, 0, ',', '.') ?></td>
                        <td>
                            <a href="hapusproductcart.php?id=<?= $item['ID'] ?>" class="remove-btn">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h2>Total Belanja: Rp <?= number_format($total_cart, 0, ',', '.') ?></h2>
                <a href="checkout.php" class="checkout-btn">Checkout</a>
            </div>
        <?php endif; ?>

        <a href="home.php" class="back-btn">Lanjut Belanja</a>
    </div>
</body>
</html>
