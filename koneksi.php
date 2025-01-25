<?php
try {
    $db = new PDO('sqlite:db/db.sqlite3');  
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage(); 
}
?>
