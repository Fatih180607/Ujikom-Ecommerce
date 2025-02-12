<?php
try {
    session_start();
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $search_query = "%%";
    $kategori_query = "";

    if (isset($_GET['Search_Query'])) {
        $search_query = "%" . $_GET['Search_Query'] . "%";
    }

    if (!empty($_GET['Kategori'])) {
        $kategori_query = "AND Kategori = :Kategori";
    }

    $sql = "SELECT * FROM Produk WHERE Nama_Produk LIKE :Nama_Produk $kategori_query";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':Nama_Produk', $search_query, PDO::PARAM_STR);

    if (!empty($kategori_query)) {
        $stmt->bindValue(':Kategori', $_GET['Kategori'], PDO::PARAM_STR);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil daftar kategori unik
    $kategori_stmt = $db->query("SELECT DISTINCT Kategori FROM Kategori");
    $kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="icon" type="image/png" href="gambar/jerseyfy_logo.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="home.css"/>
  </head>
  <body>
    <div class="Navbar">
    <img class="LogoNavbar" src="gambar/jerseyfy_logo.png" alt="Logo" />
      <ul>
        <li class="Home">Home</li>
      </ul>
        <div class="content_navbar">
        <div class="searchbar">
        <form action="" method="get">
            <input type="search" id="searchproduk" autocomplete="off" name="Search_Query" placeholder="Search" value="<?= htmlspecialchars($_GET['Search_Query'] ?? '') ?>">
            <select class="dropdown_kategori" name="Kategori" id="kategori">
                <option value="">Semua Kategori</option>
                <?php
                foreach ($kategori_list as $kategori) {
                    $selected = (isset($_GET['Kategori']) && $_GET['Kategori'] == $kategori['Kategori']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($kategori['Kategori']) . "' $selected>" . htmlspecialchars($kategori['Kategori']) . "</option>";
                }
                ?>
            </select>

            <?php echo $_SESSION['username'];?>
            <button class="dropdown_kategori" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        </div>
      <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
      <a class="LogoutButton" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
      </div>
    </div>
    <h1>Daftar Produk</h1>
    <div class="product-container">
      <?php
      if ($products) {
          foreach ($products as $product) {
              echo '<a href="detailproduct.php?id=' . urldecode($product['Nama_Produk']) . '" class="product-card-link">';
              echo '<div class="product-card">';
              echo '<img src="' . htmlspecialchars($product['Gambar']) . '" alt="' . htmlspecialchars($product['Nama_Produk']) . '" class="product-image">';
              echo '<h2>' . htmlspecialchars($product['Nama_Produk']) . '</h2>';
              echo '<td>'. htmlspecialchars($product['Deskripsi']).'</td>';
              echo '<p class="Harga">Price: Rp ' . number_format($product['Harga'], 0, ',', '.') . '</p>';
              echo '</div>';
          }
      } else {
          echo '<p>Tidak ada produk yang tersedia.</p>';
      }
      ?>
    </div>   
  </body>
</html>
