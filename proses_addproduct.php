<?php
include('koneksi.php');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Nama_Produk = trim($_POST['Nama_Produk']);
        $Deskripsi = $_POST['Deskripsi'];
        $Kategori = $_POST['Kategori'];

        if (empty($Nama_Produk) || empty($Deskripsi) || empty($Kategori)) {
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
                $gambarUrl = $destPath;

                // Simpan data produk ke tabel Produk
                $sql = "INSERT INTO Produk (Nama_Produk, Deskripsi, Gambar, Kategori) 
                        VALUES (:Nama_Produk, :Deskripsi, :Gambar, :Kategori)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':Nama_Produk', $Nama_Produk);
                $stmt->bindParam(':Deskripsi', $Deskripsi);
                $stmt->bindParam(':Gambar', $gambarUrl);
                $stmt->bindParam(':Kategori', $Kategori);
                $stmt->execute();

                $productId = $db->lastInsertId();

                // Simpan data ukuran ke tabel SizeProduct
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

                header("Location: homeadmin.php?success=1");
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
