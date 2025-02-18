<?php
session_start();
header('Content-Type: application/json');

$db = new PDO('sqlite:db/db.sqlite3');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Anda harus login terlebih dahulu!"]);
    exit();
}

require_once 'db/midtrans-php-master/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-sDRcHQQ37yB-CDwwMCl6EdmK';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$username = $_SESSION['username'];
$sql = "SELECT Cart.ID, Produk.Nama_Produk, Cart.Size, Cart.Quantity, SizeProduct.Harga
        FROM Cart
        JOIN Produk ON Cart.ID_Product = Produk.ID
        JOIN SizeProduct ON Cart.Size = SizeProduct.Size_Produk AND Cart.ID_Product = SizeProduct.ID_Product
        WHERE Cart.Username = :username";
$stmt = $db->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo json_encode(["status" => "error", "message" => "Keranjang kosong"]);
    exit();
}

$total_price = 0;
$item_details = [];
foreach ($cart_items as $item) {
    $total_price += $item['Harga'] * $item['Quantity'];
    $item_details[] = [
        "id" => $item['ID'],
        "price" => $item['Harga'],
        "quantity" => $item['Quantity'],
        "name" => $item['Nama_Produk']
    ];
}

$transaction_details = [
    "order_id" => "ORDER-" . time(),
    "gross_amount" => $total_price
];

$customer_details = [
    "username" => $username
];

$transaction = [
    "transaction_details" => $transaction_details,
    "customer_details" => $customer_details,
    "item_details" => $item_details
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    echo json_encode(["snapToken" => $snapToken]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
