<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = $db->query("SELECT ID, Kategori FROM Kategori");
    $kategori_list = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->query("SELECT ID, Nama_Liga,Negara FROM Kategori_Liga");
    $kategori_liga = $query->fetchAll(PDO::FETCH_ASSOC);


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Nama_Produk = trim($_POST['Nama_Produk']);
        $Deskripsi = $_POST['Deskripsi'];
        $Harga = $_POST['Harga'];
        $Kategori = $_POST['Kategori'];
        $Kategori_Liga = $_POST['Kategori_Liga'];

        if (empty($Nama_Produk) || empty($Deskripsi) || empty($Harga) || empty($Kategori) ||empty($Kategori_Liga)) {
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

                $sql = "INSERT INTO Produk (Nama_Produk, Deskripsi, Harga, Gambar, Kategori,Kategori_Liga) 
                        VALUES (:Nama_Produk, :Deskripsi, :Harga, :Gambar, :Kategori, :Kategori_Liga)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':Nama_Produk', $Nama_Produk);
                $stmt->bindParam(':Deskripsi', $Deskripsi);
                $stmt->bindParam(':Harga', $Harga);
                $stmt->bindParam(':Gambar', $gambarUrl);
                $stmt->bindParam(':Kategori', $Kategori);
                $stmt->bindParam(':Kategori_Liga', $Kategori_Liga);
                $stmt->execute();

                $productId = $db->lastInsertId();


                if (isset($_POST['size']) && isset($_POST['size_price'])) {
                    $sizes = $_POST['size'];
                    $prices = $_POST['size_price'];


                    if (count($sizes) !== count($prices)) {
                        throw new Exception("Jumlah ukuran dan harga tidak sesuai.");
                    }


                    for ($i = 0; $i < count($sizes); $i++) {
                        $size = trim($sizes[$i]);
                        $price = trim($prices[$i]);


                        if (!empty($size) && !empty($price)) {
                            $sqlSize = "INSERT INTO SizeProduct (Size_Produk, Harga, ID_Product) 
                                        VALUES (:size, :price, :productId)";
                            $stmtSize = $db->prepare($sqlSize);
                            $stmtSize->bindParam(':size', $size);
                            $stmtSize->bindParam(':price', $price);
                            $stmtSize->bindParam(':productId', $productId);


                            if (!$stmtSize->execute()) {
                                throw new Exception("Gagal memasukkan data ukuran: " . implode(", ", $stmtSize->errorInfo()));
                            }
                        }
                    }
                }

                header("Location: homeadmin.php");
                exit();
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
        <form action="proses_addproduct.php" method="POST" autocomplete="off" enctype="multipart/form-data">
            <h3>Detail Produk</h3>

            <label for="Nama_Produk">Nama Produk:</label>
            <input type="text" id="Nama_Produk" name="Nama_Produk" placeholder="Nama Produk" required>

            <label for="Deskripsi">Deskripsi:</label>
            <textarea id="Deskripsi" name="Deskripsi" placeholder="Deskripsi Produk" required cols="80" rows="5"></textarea>

            <h3>Kategori</h3>
            <label for="Kategori">Kategori Produk:</label>
            <select class="dropdown_kategori" name="Kategori" id="kategori">
                <option value="">Semua Kategori</option>
                <?php
                foreach ($kategori_list as $kategori) {
                    $selected = (isset($_GET['Kategori']) && $_GET['Kategori'] == $kategori['ID']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($kategori['ID']) . "' $selected>" . htmlspecialchars($kategori['Kategori']) . "</option>";
                }
                ?>
            </select>

            <label for="Kategori_liga">Liga:</label>
            <select class="dropdown_kategoriliga" name="Kategori_liga" id="kategori_liga">
                <option value="">Semua Liga</option>
                <?php
                foreach ($kategori_liga as $liga) { // Ganti nama variabel iterasi biar tidak bentrok
                    $selected = (isset($_GET['Kategori_liga']) && $_GET['Kategori_liga'] == $liga['ID']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($liga['ID']) . "' $selected>" . htmlspecialchars($liga['Nama_Liga']) . " - " . htmlspecialchars($liga['Negara']) . "</option>";
                }
                ?>
            </select>


            <label for="Gambar">Upload Gambar:</label>
            <input type="file" id="Gambar" name="Gambar" accept="image/*" required>

            <h3>Ukuran dan Harga</h3>
            <div id="size-container">
                <div class="size-row">
                    <input type="text" name="size[]" placeholder="Ukuran (contoh: S, M, L)" required>
                    <input type="number" name="size_price[]" placeholder="Harga untuk ukuran ini" required>
                    <button type="button" onclick="removeSize(this)">Hapus</button>
                </div>
            </div>
            <button type="button" onclick="addSize()">Tambah Ukuran</button>

            <div class="button-container">
                <button type="submit" class="btn-submit">Tambah Produk</button>
                <a href="homeadmin.php" class="btn-cancel">Cancel</a>
            </div>
        </form>

        <script>
            function addSize() {
                const container = document.getElementById('size-container');
                const newRow = document.createElement('div');
                newRow.classList.add('size-row');
                newRow.innerHTML = `
            <input type="text" name="size[]" placeholder="Ukuran (contoh: S, M, L)" required>
            <input type="number" name="size_price[]" placeholder="Harga untuk ukuran ini" required>
            <button type="button" onclick="removeSize(this)">Hapus</button>
        `;
                container.appendChild(newRow);
            }

            function removeSize(button) {
                button.parentElement.remove();
            }
        </script>

</body>

</html>