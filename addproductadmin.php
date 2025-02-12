<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $Nama_Produk = trim($_POST['Nama_Produk']);
        $Deskripsi = $_POST['Deskripsi'];
        $Harga = $_POST['Harga'];

        
        if (empty($Nama_Produk) || empty($Deskripsi) || empty($Harga)) {
            throw new Exception("Semua kolom wajib diisi.");
        }

        
        if (isset($_FILES['Gambar']) && $_FILES['Gambar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['Gambar']['tmp_name'];
            $fileName = $_FILES['Gambar']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Tipe file tidak valid. Hanya gambar yang diperbolehkan.");
            }

            
            $newFileName = uniqid() . '.' . $fileExtension;

            
            $uploadFolder = 'uploads/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true); 
            }

            
            $destPath = $uploadFolder . $newFileName;

            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                
                $gambarUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/ecommerce/' . $destPath;

                
                $sql = "INSERT INTO Produk (Nama_Produk, Deskripsi, Harga, Gambar) 
                        VALUES (:Nama_Produk, :Deskripsi, :Harga, :Gambar)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':Nama_Produk', $Nama_Produk);
                $stmt->bindParam(':Deskripsi', $Deskripsi);
                $stmt->bindParam(':Harga', var: $Harga);
                $stmt->bindParam(':Gambar', $gambarUrl);
                $stmt->execute();

                header("Location:homeadmin.php");
            } else {
                throw new Exception("Terjadi kesalahan saat mengunggah file.");
            }
        } else {
            throw new Exception("File gambar tidak ditemukan atau terjadi kesalahan.");
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="icon" type="image/png" href="gambar/removebg.png">
    <link rel="stylesheet" href="addproductadmin.css">
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
            <li><a href="kategori_produk.php"><i class="fas fa-table-list"></i> Kategori</a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="content">
        <h1 class="h1AddProduct">Add Your Product</h1>
        <form action="addproductadmin.php" method="POST" autocomplete="off" enctype="multipart/form-data">
            
            <label for="Nama_Produk">Nama Produk:</label>
            <input type="text" id="Nama_Produk" name="Nama_Produk" placeholder="Nama Produk" required>

            <label for="Deskripsi">Deskripsi:</label>
            <textarea id="Deskripsi" name="Deskripsi" placeholder="Deskripsi Produk" style="resize: none;" required cols="80" rows="10"></textarea>

            <label for="Harga">Harga:</label>
            <input type="text" id="Harga" name="Harga" placeholder="Harga" required>

            <label for="Gambar">Upload Gambar:</label>
            <input type="file" id="Gambar" name="Gambar" accept="image/*" required>

            <!-- Preview Gambar -->
            <img id="preview" style="max-width: 300px; max-height: 300px; display: none; border: 1px solid #ddd; padding: 5px;">

            <div class="button-container">
                <button type="submit" class="btn-submit">Tambah Produk</button>
                <a href="homeadmin.php" class="btn-cancel">Cancel</a>
            </div>
            
        </form>
    </div>
    <script>
        document.getElementById('Gambar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    </script>

</body>
</html>

