<?php
session_start();

if (isset($_SESSION['role'])) {
  if($_SESSION["role"] <> "Admin"){
  header("Location: home.php");
  exit;  
  }
} else{
  header("Location:index.php");
}

try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = $db->query("SELECT ID,Nama_Produk, Deskripsi, Harga, Gambar FROM Produk");
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Admin</title>
    <link rel="stylesheet" href="homeadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="gambar/logoitem.png" alt="Logo">
        </div>
        <ul>
            <li><a href="homeadmin.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="addproductadmin.php"><i class="fas fa-plus"></i> Tambah Produk</a></li>
            <li><a href="kategori_produk.php"><i class="fa-solid fa-table-list"></i> Kategori</a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="content">
        <header>
            <h1>Daftar Produk</h1>
        </header>

        <button class="btn-add">
            <a href="addproductadmin.php"><i class="fas fa-plus"></i> Tambah Produk</a>
        </button>

        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr onclick="openPopup(
                        '<?= htmlspecialchars($product['Nama_Produk']) ?>', 
                        '<?= htmlspecialchars($product['Deskripsi']) ?>', 
                        '<?= number_format($product['Harga'], 0, ',', '.') ?>', 
                        '<?= htmlspecialchars($product['Gambar']) ?>'
                    )">
                        <td><?= htmlspecialchars($product['ID']) ?></td>
                        <td><?= htmlspecialchars($product['Nama_Produk']) ?></td>
                        <td><?= htmlspecialchars($product['Deskripsi']) ?></td>
                        <td>Rp <?= number_format($product['Harga'], 0, ',', '.') ?></td>
                        <td class='actions'>
                            <a href='editproduk.php?ID=<?= htmlspecialchars($product['ID']) ?>' class='btn-edit'><i class='fas fa-edit'></i> Edit</a>
                            <a href='deleteproduk.php?ID=<?= htmlspecialchars($product['ID']) ?>' class='btn-delete' onclick='return confirm("Apakah Anda yakin ingin menghapus produk ini?")'><i class='fas fa-trash'></i> Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="popupDetail" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2 id="popupNama"></h2>
            <img id="popupGambar" src="" alt="Gambar Produk">
            <p id="popupDeskripsi"></p>
            <p><strong>Harga:</strong> Rp <span id="popupHarga"></span></p>
        </div>
    </div>

    <script>
    function openPopup(nama, deskripsi, harga, gambar) {
        document.getElementById('popupNama').textContent = nama;
        document.getElementById('popupDeskripsi').textContent = deskripsi;
        document.getElementById('popupHarga').textContent = harga;
        document.getElementById('popupGambar').src = gambar;
        document.getElementById('popupDetail').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('popupDetail').style.display = 'none';
    }
    </script>
</body>
</html>
