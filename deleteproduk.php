<?php
include('koneksi.php');

if (isset($_GET['ID'])) {
    $ID = $_GET['ID'];

    if (is_numeric($ID)) {
        $sql = "DELETE FROM Produk WHERE ID = :ID";
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':ID', $ID, PDO::PARAM_INT);

        if ($stmt->execute()) {
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
