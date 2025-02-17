<?php
include('koneksi.php');

if (isset($_POST['update'])) {
    $ID = $_POST['ID'];
    $Nama_Produk = $_POST['Nama_Produk'];
    $Deskripsi = $_POST['Deskripsi'];

    if (isset($_FILES['Gambar']) && $_FILES['Gambar']['error'] == 0) {
        $gambarPath = "uploads/" . $_FILES['Gambar']['name'];
        move_uploaded_file($_FILES['Gambar']['tmp_name'], $gambarPath);
    } else {
        $gambarPath = $_POST['oldGambar'];
    }

    $sql = "UPDATE Produk SET Nama_Produk = :Nama_Produk, Deskripsi = :Deskripsi, Gambar = :Gambar WHERE ID = :ID";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':Nama_Produk', $Nama_Produk, PDO::PARAM_STR);
    $stmt->bindValue(':Deskripsi', $Deskripsi, PDO::PARAM_STR);
    $stmt->bindValue(':Gambar', $gambarPath, PDO::PARAM_STR);
    $stmt->bindValue(':ID', $ID, PDO::PARAM_INT);
    $stmt->execute();
    
    header("Location: homeadmin.php");
    exit();
}
?>