<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $produkID = intval($_GET['id']);

        $stmt = $db->prepare("SELECT Nama_Produk, Deskripsi, Gambar FROM Produk WHERE ID = :id");
        $stmt->bindParam(':id', $produkID, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "<p>Produk tidak ditemukan.</p>";
            exit;
        }

        $stmtSize = $db->prepare("SELECT Size_Produk, Harga FROM SizeProduct WHERE ID_Product = :id ORDER BY Harga ASC");
        $stmtSize->bindParam(':id', $produkID, PDO::PARAM_INT);
        $stmtSize->execute();

        $sizes = $stmtSize->fetchAll(PDO::FETCH_ASSOC);
        $hargaTermurah = !empty($sizes) ? $sizes[0]['Harga'] : null;
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link rel="icon" type="gambar" href="gambar/jerseyonly_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="detailproduct.css">
</head>

<body>
<div class="Navbar">
    <a href="home.php">
        <img class="LogoNavbar" src="gambar/jerseyfy_logo.png" alt="Logo" />
    </a>
    <div class="content_navbar">
        <a href="#aboutus" class="nav-link" id="about-link">About Us</a>

        <div class="button-container">
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
        </div>

        <div class="profile-dropdown">
            <i class="fa-solid fa-user profile-icon"></i>
        </div>
    </div>
</div>
    <div class="product-container">
        <img class="product-image" src="<?= htmlspecialchars($product['Gambar']) ?>" alt="<?= htmlspecialchars($product['Nama_Produk']) ?>">

        <div class="product-details">
            <h1 class="detailnamaproduk"><?= htmlspecialchars($product['Nama_Produk']) ?></h1>
            <p class="detaildeskripsiproduk"><?= htmlspecialchars($product['Deskripsi']) ?></p>

            <p class="detailhargaproduk" id="harga-produk">
                <?php if ($hargaTermurah !== null): ?>
                    Harga: Rp <span id="harga"><?= number_format($hargaTermurah, 0, ',', '.') ?></span>
                <?php else: ?>
                    Harga: Tidak tersedia
                <?php endif; ?>
            </p>
            <?php if (!empty($sizes)): ?>
                <label for="size">Pilih Ukuran:</label>
                <select id="size" name="size" onchange="updatePrice()">
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= htmlspecialchars($size['Harga']) ?>">
                            <?= htmlspecialchars($size['Size_Produk']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <form action="addproductcart.php" method="post">
                <input type="hidden" name="produk_id" value="<?= htmlspecialchars($produkID); ?>">
                <input type="hidden" name="size" id="selected-size" value="<?= !empty($sizes) ? htmlspecialchars($sizes[0]['Size_Produk']) : '' ?>">

                <label for="jumlah">Jumlah:</label>
                <input type="number" name="jumlah" value="1" min="1">

                <button type="submit">Add to Cart</button>
            </form>

            <a href="home.php" class="back-button">Kembali ke Home</a>
        </div>
    </div>

    <script>
        function updatePrice() {
            const hargaElement = document.getElementById("harga");
            const sizeSelect = document.getElementById("size");
            const selectedSize = document.getElementById("selected-size");

            hargaElement.textContent = new Intl.NumberFormat('id-ID').format(sizeSelect.value);
            selectedSize.value = sizeSelect.options[sizeSelect.selectedIndex].text;
        }
    </script>

</body>

</html>