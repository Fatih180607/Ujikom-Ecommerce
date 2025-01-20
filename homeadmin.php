<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = $db->query("SELECT Nama_Produk, Deskripsi, Harga, Gambar FROM Produk");
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="icon" type="image/png" href="gambar/removebg.png" />
    <link rel="stylesheet" href="homeadmin.css"/>
  </head>
  <body>
    <div class="Navbar">
      <img class="LogoNavbar" src="gambar/removebg.png" alt="Logo" />
      <ul>
        <li class="Home">Home</li>
        <li><a href="addproductadmin.php">Product</a></li>
      </ul>
      <a class="LogoutButton" href="index.php">Log Out</a>
    </div>

    <h1>Daftar Produk</h1>

    <div class="product-container">
      <?php
      if ($products) {
          foreach ($products as $product) {
              echo '<div class="product-card">';
              echo '<img src="' . htmlspecialchars($product['Gambar']) . '" alt="' . htmlspecialchars($product['Nama_Produk']) . '" class="product-image">';
              echo '<h2>' . htmlspecialchars($product['Nama_Produk']) . '</h2>';
              echo '<p class="Deskripsi">Description: ' . htmlspecialchars($product['Deskripsi']) . '</p>';
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
