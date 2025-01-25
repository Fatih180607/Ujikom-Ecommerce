<?php
include('koneksi.php');

if (isset($_GET['ID'])) {
    $ID = $_GET['ID'];

    if (is_numeric($ID)) {
        $sql = "SELECT * FROM Produk WHERE ID = :ID";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ID', $ID, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            echo "Produk tidak ditemukan.";
            exit();
        }
    } else {
        echo "ID tidak valid.";
        exit();
    }
} else {
    echo "ID produk tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="icon" type="image/png" href="gambar/removebg.png" />
    <link rel="stylesheet" href="editproduk.css">
</head>
<body>
    <div class="EditProduct">
        <h1>Edit Produk</h1>
        <form action="updateproduk.php" method="POST" enctype="multipart/form-data" id="updateForm" autocomplete="off">
    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($product['ID']); ?>">
    <input type="hidden" name="oldGambar" value="<?php echo htmlspecialchars($product['Gambar']); ?>">
    
    <label for="Nama_Produk">Nama Produk:</label>
    <input type="text" name="Nama_Produk" value="<?php echo htmlspecialchars($product['Nama_Produk']); ?>" required>
    
    <label for="Deskripsi">Deskripsi:</label>
    <textarea name="Deskripsi" required><?php echo htmlspecialchars($product['Deskripsi']); ?></textarea>
    
    <label for="Harga">Harga:</label>
    <input type="number" name="Harga" value="<?php echo htmlspecialchars($product['Harga']); ?>" required>
    
    <label for="Gambar">Gambar Produk:</label>
    <input type="file" name="Gambar" id="gambarInput" onchange="previewImage(event)">
    
    <img src="<?php echo htmlspecialchars($product['Gambar']); ?>" alt="Gambar Produk" width="100" id="gambarPreview">
    
    <a href="homeadmin.php">
        <input type="button" value="Cancel" style="cursor:pointer">
    </a>
    
    <input type="submit" name="update" value="Update Produk" style="cursor: pointer;">
</form>
    </div>
    <script>function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('gambarPreview');
        output.src = reader.result; // Menampilkan gambar baru yang dipilih
    };
    reader.readAsDataURL(event.target.files[0]);
}</script>
</body>
</html>