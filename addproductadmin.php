<?php
try {
    // Koneksi ke database SQLite
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Periksa apakah form telah disubmit
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product</title>
    <link rel="icon" type="gambar" href="gambar/removebg.png"/>
    <link rel="stylesheet" href="addproductadmin.css" />
  </head>
  <body>
    <h1 class="h1AddProduct">Add your product</h1>
    <form action="addproductadmin.php" method="POST" autocomplete="off" enctype="multipart/form-data">
    <label for="Nama_Produk">Nama Produk:</label><br>
    <input type="text" id="Nama_Produk" placeholder="Nama Produk" name="Nama_Produk" required><br><br>

    <label for="Deskripsi">Deskripsi:</label><br>
    <textarea id="Deskripsi" name="Deskripsi" placeholder="Deskripsi Produk" style="resize: none;" required cols="80" rows="10"></textarea><br><br>

    <label for="Harga">Harga:</label><br>
    <input type="text" id="Harga" placeholder="Harga" name="Harga" required><br><br>

    <label for="Gambar">Upload Gambar:</label><br>
    <input type="file" id="Gambar" name="Gambar" accept="image/*" required><br><br>

    <img id="preview" style="max-width: 300px; max-height: 300px; display: none; border: 1px solid #ddd; padding: 5px;">
    <button type="submit">Tambah Produk</button>
    <button class="cancelbutton" type="submit"><a href="homeadmin.php">Cancel</a></button>
</form>

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
