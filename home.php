<?php
try {
    session_start();
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $liga_stmt = $db->query("SELECT ID, Nama_Liga FROM Kategori_liga");
    $liga_list = $liga_stmt->fetchAll(PDO::FETCH_ASSOC);

    $kategori_stmt = $db->query("SELECT ID, Kategori FROM Kategori");
    $kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

    $search_query = "%";
    $liga_query = "";
    $kategori_query = "";

    if (!empty($_GET['Search_Query'])) {
        $search_query = "%" . $_GET['Search_Query'] . "%";
    }

    if (!empty($_GET['Liga'])) {
        $liga_query = "AND Produk.Kategori_Liga = :Liga";
    }

    if (!empty($_GET['Kategori'])) {
        $kategori_query = "AND Produk.Kategori = :Kategori";
    }

    $sql = "SELECT Produk.ID, Produk.Nama_Produk, Produk.Deskripsi, Produk.Gambar, 
        Kategori.Kategori AS Nama_Kategori,
        (SELECT MIN(Harga) FROM SizeProduct WHERE SizeProduct.ID_Product = Produk.ID) AS Harga_Termurah,
        (SELECT MAX(Harga) FROM SizeProduct WHERE SizeProduct.ID_Product = Produk.ID) AS Harga_Termahal
    FROM Produk 
    JOIN Kategori ON Produk.Kategori = Kategori.ID
    WHERE Produk.Nama_Produk LIKE :Nama_Produk $liga_query $kategori_query";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':Nama_Produk', $search_query, PDO::PARAM_STR);

    if (!empty($_GET['Liga'])) {
        $stmt->bindValue(':Liga', $_GET['Liga'], PDO::PARAM_INT);
    }

    if (!empty($_GET['Kategori'])) {
        $stmt->bindValue(':Kategori', $_GET['Kategori'], PDO::PARAM_INT);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Home</title>
    <link rel="icon" type="image/png" href="gambar/jerseyonly_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./home.css">
</head>

<body>

<div class="Navbar">
    <a href="home.php">
        <img class="LogoNavbar" src="gambar/jerseyfy_logo.png" alt="Logo">
    </a>

    <div class="about-link-container">
        <a href="#aboutus" class="nav-link">About Us</a>
    </div>

    <div class="navbar-right">
        <form method="GET" class="searchbar">
            <input type="search" id="searchproduk" autocomplete="off" name="Search_Query" 
                placeholder="Search berdasarkan nama" 
                value="<?= htmlspecialchars($_GET['Search_Query'] ?? '') ?>">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <form method="GET" action="home.php">
            <select name="Kategori" id="kategori" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori_list as $kategori) {
                    $selected = (isset($_GET['Kategori']) && $_GET['Kategori'] == $kategori['ID']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($kategori['ID']) . "' $selected>" . htmlspecialchars($kategori['Kategori']) . "</option>";
                } ?>
            </select>

            <select name="Liga" id="liga" onchange="this.form.submit()">
                <option value="">Semua Liga</option>
                <?php foreach ($liga_list as $liga) {
                    $selected = (isset($_GET['Liga']) && $_GET['Liga'] == $liga['ID']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($liga['ID']) . "' $selected>" . htmlspecialchars($liga['Nama_Liga']) . "</option>";
                } ?>
            </select>
        </form>

        <div class="button-container">
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
</div>

<div class="logout-icon">
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
</div>

    </div>
</div>

<h1>Daftar Produk</h1>
<div class="product-container">
    <?php if ($products) {
        foreach ($products as $product) { ?>
            <a href="detailproduct.php?id=<?= urlencode($product['ID']) ?>" class="product-card-link">
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['Gambar']) ?>" alt="<?= htmlspecialchars($product['Nama_Produk']) ?>" class="product-image">
                    <h2><?= htmlspecialchars($product['Nama_Produk']) ?></h2>
                    <p class="Tag_Kategori_Card">Kategori: <?= htmlspecialchars($product['Nama_Kategori']) ?></p>
                    <p class="Harga">
                        <?= ($product['Harga_Termurah'] === $product['Harga_Termahal']) ? 
                            "Harga: Rp " . number_format($product['Harga_Termurah'], 0, ',', '.') : 
                            "Harga: Rp " . number_format($product['Harga_Termurah'], 0, ',', '.') . " - Rp " . number_format($product['Harga_Termahal'], 0, ',', '.') ?>
                    </p>
                </div>
            </a>
        <?php }
    } else {
        echo '<p>Tidak ada produk yang tersedia.</p>';
    } ?>
</div>

<div class="hero-section" id="aboutus">
    <img src="gambar/antonydua.jpg" alt="Banner" class="hero-image">
    <div class="overlay"></div>
    <div class="hero-text">
        <h2>About Us</h2>
        <p>Selamat datang di Jerseyfy! Kami menyediakan berbagai macam jersey berkualitas tinggi untuk para pecinta sepak bola.</p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".nav-link").addEventListener("click", function (event) {
            event.preventDefault();
            document.getElementById("aboutus").scrollIntoView({ behavior: "smooth", block: "start" });
        });

        const profileIcon = document.querySelector(".profile-icon");
        const dropdownContent = document.querySelector(".dropdown-content");

        profileIcon.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownContent.classList.toggle("active");
        });

        window.addEventListener("click", function (event) {
            if (!profileIcon.contains(event.target) && !dropdownContent.contains(event.target)) {
                dropdownContent.classList.remove("active");
            }
        });
    });
</script>

</body>
</html>
