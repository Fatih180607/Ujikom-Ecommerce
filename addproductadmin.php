<?php
try {
    // Koneksi ke database SQLite
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Periksa apakah form telah disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $Nama_Produk = $_POST['Nama_Produk'];
        $Deskripsi = $_POST['Deskripsi'];
        $Harga = $_POST['Harga'];

        // Validasi input
        if (empty($Nama_Produk) || empty($Deskripsi) || empty($Harga)) {
            throw new Exception("Semua kolom wajib diisi.");
        }

        // Proses upload gambar
        if (isset($_FILES['Gambar']) && $_FILES['Gambar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['Gambar']['tmp_name'];
            $fileName = $_FILES['Gambar']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validasi tipe file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Tipe file tidak valid. Hanya gambar yang diperbolehkan.");
            }

            // Buat nama file unik
            $newFileName = uniqid() . '.' . $fileExtension;

            // Tentukan direktori upload
            $uploadFolder = 'uploads/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true); // Buat folder jika belum ada
            }

            // Tentukan path file tujuan
            $destPath = $uploadFolder . $newFileName;

            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Buat URL untuk file
                $gambarUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/formtesting/' . $destPath;

                // Simpan data ke database
                $sql = "INSERT INTO Produk (Nama_Produk, Deskripsi, Harga, Gambar) 
                        VALUES (:Nama_Produk, :Deskripsi, :Harga, :Gambar)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':Nama_Produk', $Nama_Produk);
                $stmt->bindParam(':Deskripsi', $Deskripsi);
                $stmt->bindParam(':Harga', $Harga);
                $stmt->bindParam(':Gambar', $gambarUrl);
                $stmt->execute();

                echo "Produk berhasil ditambahkan!";
            } else {
                throw new Exception("Terjadi kesalahan saat mengunggah file.");
            }
        } else {
            throw new Exception("File gambar tidak ditemukan atau terjadi kesalahan.");
        }
    }
} catch (Exception $e) {
    // Tampilkan pesan kesalahan
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
    <div class="Navbar">
      <img class="LogoNavbar" src="gambar/removebg.png" />
      <ul>
        <li><a href="homeadmin.php">Home</a></li>
        <li class="AddProduct">Product</li>
      </ul>
      <a class="LogoutButton" href="index.php">Log Out</a>
    </div>
    <h1 class="h1AddProduct">Add your product</h1>
    <form action="addproductadmin.php" method="POST" enctype="multipart/form-data">
    <label for="Nama_Produk">Nama Produk:</label><br>
    <input type="text" id="Nama_Produk" name="Nama_Produk" required><br><br>

    <label for="Deskripsi">Deskripsi:</label><br>
    <textarea id="Deskripsi" name="Deskripsi" required></textarea><br><br>

    <label for="Harga">Harga:</label><br>
    <input type="text" id="Harga" name="Harga" required><br><br>

    <label for="Gambar">Upload Gambar:</label><br>
    <input type="file" id="Gambar" name="Gambar" accept="image/*" required><br><br>

    <button type="submit">Tambah Produk</button>
</form>

  </body>
</html>
