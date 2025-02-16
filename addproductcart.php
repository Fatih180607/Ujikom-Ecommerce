<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php"); 
    exit(); 
}

try {
    $db = new PDO('sqlite:db/db.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_SESSION['username'];
        $id_product = intval($_POST['produk_id']);
        $size = trim($_POST['size']);
        $quantity = intval($_POST['jumlah']);

        if ($quantity <= 0) {
            die("Jumlah harus lebih dari 0.");
        }

        $checkQuery = "SELECT ID FROM Cart WHERE Username = :username AND ID_Product = :id_product AND Size = :size";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([
            ':username' => $username,
            ':id_product' => $id_product,
            ':size' => $size
        ]);
        $existingCart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCart) {
            $updateQuery = "UPDATE Cart SET Quantity = Quantity + :quantity WHERE ID = :cart_id";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([
                ':quantity' => $quantity,
                ':cart_id' => $existingCart['ID']
            ]);
        } else {
            $insertQuery = "INSERT INTO Cart (Username, ID_Product, Size, Quantity) VALUES (:username, :id_product, :size, :quantity)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute([
                ':username' => $username,
                ':id_product' => $id_product,
                ':size' => $size,
                ':quantity' => $quantity
            ]);
        }

        header("Location: cart.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>
