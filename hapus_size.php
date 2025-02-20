<?php
include('koneksi.php');

if (isset($_POST['size_id'])) {
    $sizeID = $_POST['size_id'];

    $stmt = $db->prepare("DELETE FROM SizeProduct WHERE ID = :ID");
    $stmt->bindValue(':ID', $sizeID, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
