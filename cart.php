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
    <link rel="icon" type="gambar" href="gambar/jerseyonly_logo.png">
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
                    <?php $total_cart = 0; ?>
                    <?php foreach ($cart_items as $item): 
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
                            <td>
                                <form action="update_cart.php" method="POST" class="updatejumlah">
                                    <input type="hidden" name="cart_id" value="<?= $item['ID'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['Quantity'] ?>" min="1">
                                    <button class="buttonupdate" type="submit">Update</button>
                                </form>
                            </td>
                            <td>Rp <?= number_format($total_price, 0, ',', '.') ?></td>
                            <td>
                                <a href="hapusproductcart.php?id=<?= $item['ID'] ?>" class="remove-btn">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="checkout-popup" class="popup">
                <div class="popup-content">
                    <h2>Isi Detail Pemesanan</h2>
                    <form id="checkout-form">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required>

                        <label for="telp">No. Telp</label>
                        <input type="text" id="telp" name="telp" required>

                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" required></textarea>

                        <label for="kode_pos">Kode Pos</label>
                        <input type="text" id="kode_pos" name="kode_pos" required>

                        <button type="submit">Lanjut ke Pembayaran</button>
                        <button type="button" onclick="closePopup()">Batal</button>
                    </form>
                </div>
            </div>

            <div class="cart-summary">
                <h2>Total: Rp <?= number_format($total_cart, 0, ',', '.') ?></h2>
                <a href="#" id="checkout-button" class="checkout-btn">Checkout</a>
            </div>
        <?php endif; ?>

        <a href="home.php" class="back-btn">Lanjut Belanja</a>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-34RL4XHkHmRnFzEj"></script>
    <script>
        document.getElementById("checkout-button").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("checkout-popup").style.display = "flex";
        });

        function closePopup() {
            document.getElementById("checkout-popup").style.display = "none";
        }

        document.getElementById("checkout-form").addEventListener("submit", async function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            const response = await fetch("checkout.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();
            if (result.snapToken) {
                closePopup();
                window.snap.pay(result.snapToken, {
    onSuccess: function(result) {
        alert("Pembayaran berhasil!");
        fetch("clear_cart.php") // Hapus cart setelah pembayaran berhasil
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Keranjang berhasil dikosongkan!");
                    window.location.href = "home.php"; // Redirect ke halaman utama
                } else {
                    alert("Gagal mengosongkan keranjang!");
                }
            });
    },
    onPending: function(result) {
        alert("Pembayaran tertunda. Selesaikan pembayaran segera.");
    },
    onError: function(result) {
        alert("Pembayaran gagal!");
    }
});

            } else {
                alert("Gagal mendapatkan token pembayaran!");
            }
        });
    </script>
</body>
</html>
