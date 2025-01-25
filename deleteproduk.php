<?php
// Koneksi ke database SQLite
include('koneksi.php');

if (isset($_GET['ID'])) {
    // Ambil ID dari URL
    $ID = $_GET['ID'];

    // Pastikan ID yang diterima adalah angka untuk keamanan
    if (is_numeric($ID)) {
        // Query untuk menghapus produk berdasarkan ID
        $sql = "DELETE FROM Produk WHERE ID = :ID";
        $stmt = $db->prepare($sql);
        
        // Binding parameter ID sebagai integer
        $stmt->bindValue(':ID', $ID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Jika berhasil menghapus, arahkan kembali ke homeadmin.php
            header("Location: homeadmin.php");
            exit();
        } else {
            echo "Gagal menghapus produk.";
        }
    } else {
        echo "ID yang diberikan tidak valid.";
    }
} else {
    echo "ID produk tidak ditemukan.";
}
?>
