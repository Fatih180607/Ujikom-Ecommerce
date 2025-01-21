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
  <div class="Daftar_Produk">
    <h1>Daftar Produk</h1>   
    <div class="Navbar">
      <img class="LogoNavbar" src="gambar/removebg.png" alt="Logo" />
      <ul>
        <li class="Home">Home</li>
        <li><a href="addproductadmin.php">Product</a></li>
      </ul>
      <a class="LogoutButton" href="index.php">Log Out</a>
</div>
    <table class="tabel_produk">
      <thead>
        <tr>
          <th scope="col">Gambar Produk</th>
          <th scope="col">Nama</th>
          <th scope="col">Deskripsi</th>
          <th scope="col">Harga</th>
        </tr>
      </thead>
      <tbody class="row_produk">
        <?php
      foreach ($products as $product) {
              echo '<tr>';
              echo '<td><img src="' . htmlspecialchars($product['Gambar']) . '" alt="' . htmlspecialchars($product['Nama_Produk']) . '" class="product-image"></td>';
              echo '<td class="Nama_Produk_Table">' . htmlspecialchars($product['Nama_Produk']) . '</td>';
              echo '<td>' . htmlspecialchars($product['Deskripsi']) . '</td>';
              echo '<td> Price: Rp ' . number_format($product['Harga'], 0, ',', '.') . '</td>';
              echo '</tr>';
          }?>
      </tbody>
    </table>
      </div>
  </body>
</html>
