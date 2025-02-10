<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $search_query = "%%";
    if(isset($_GET['Search_Query'])){
      $search_query = "%{$_GET['Search_Query']}%";
    }
    $sql = "SELECT * FROM Produk WHERE Nama_Produk LIKE :Nama_Produk";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':Nama_Produk',$search_query, PDO::PARAM_STR);
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="icon" type="image/png" href="gambar/jerseyfy_logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="home.css"/>
  </head>
  <body>
    <div class="Navbar">
    <img class="LogoNavbar" src="gambar/jerseyfy_logo.png" alt="Logo" />
      <ul>
        <li class="Home">Home</li>
        <li><a href="historyorder.php">History Order</a></li>
      </ul>
      <div class="kiri">
        <form action="">
      <input type="search" id="searchproduk" autocomplete="off" name="Search_Query" placeholder="Search">
      <button type="submit">Search</button>
      </form>
      <i class="fa-solid fa-cart-shopping"></i>
      <a class="LogoutButton" href="logout.php">Log Out</a>
      </div>
    </div>
    <h1>Daftar Produk</h1>
    <div class="product-container">
      <?php
      if ($products) {
          foreach ($products as $product)   {
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