<?php
include('koneksi.php');

if (isset($_POST['update'])) {
    $ID = $_POST['ID'];
    $nama = $_POST['Nama_Produk'];
    $deskripsi = $_POST['Deskripsi'];
    $kategori = $_POST['Kategori'];
    $kategori_liga = $_POST['Kategori_Liga'];
    $oldGambar = $_POST['oldGambar'];

    if (!empty($_FILES['Gambar']['name'])) {
        $gambar = $_FILES['Gambar']['name'];
        $gambarTmp = $_FILES['Gambar']['tmp_name'];
        $gambarPath = "gambar/" . $gambar;

        if (!empty($oldGambar) && file_exists("gambar/" . $oldGambar)) {
            unlink("gambar/" . $oldGambar);
        }

        move_uploaded_file($gambarTmp, $gambarPath);
    } else {
        $gambar = $oldGambar; 
    }

    $stmt = $db->prepare("UPDATE Produk SET Nama_Produk = ?, Deskripsi = ?, Kategori = ?, Kategori_Liga = ?, Gambar = ? WHERE ID = ?");
    $stmt->execute([$nama, $deskripsi, $kategori, $kategori_liga, $gambar, $ID]);

    if (!empty($_POST['delete_size'])) {
        foreach ($_POST['delete_size'] as $sizeID) {
            $stmt = $db->prepare("DELETE FROM SizeProduct WHERE ID = ?");
            $stmt->execute([$sizeID]);
        }
    }

    if (!empty($_POST['size']) && !empty($_POST['harga'])) {
        $sizes = $_POST['size'];
        $prices = $_POST['harga'];
        $sizeIDs = $_POST['size_id'];

        foreach ($sizes as $key => $size) {
            $harga = $prices[$key];
            $sizeID = $sizeIDs[$key] ?? null;

            if (!empty($sizeID)) {
                $stmt = $db->prepare("UPDATE SizeProduct SET Size_Produk = ?, Harga = ? WHERE ID = ?");
                $stmt->execute([$size, $harga, $sizeID]);
            } else {
                $stmt = $db->prepare("INSERT INTO SizeProduct (ID_Product, Size_Produk, Harga) VALUES (?, ?, ?)");
                $stmt->execute([$ID, $size, $harga]);
            }
        }
    }

    header("Location: homeadmin.php");
    exit();
}
?>
