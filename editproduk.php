<?php
include('koneksi.php');

if (isset($_GET['ID'])) {
    $ID = $_GET['ID'];

    if (is_numeric($ID)) {
        $sql = "SELECT * FROM Produk WHERE ID = :ID";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ID', $ID, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "Produk tidak ditemukan.";
            exit();
        }

        $kategori_stmt = $db->query("SELECT ID, Kategori FROM Kategori");
        $kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

        $liga_stmt = $db->query("SELECT ID, Nama_Liga FROM Kategori_liga");
        $liga_list = $liga_stmt->fetchAll(PDO::FETCH_ASSOC);

        $size_stmt = $db->prepare("SELECT ID, Size_Produk, Harga FROM SizeProduct WHERE ID_Product = :ID");
        $size_stmt->bindValue(':ID', $ID, PDO::PARAM_INT);
        $size_stmt->execute();
        $sizes = $size_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "ID tidak valid.";
        exit();
    }
} else {
    echo "ID produk tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="editproduk.css">
</head>
<body>

<div class="EditProduct">
    <h1>Edit Produk</h1>
    <form action="updateproduk.php" method="POST" enctype="multipart/form-data" id="updateForm">
        <input type="hidden" name="ID" value="<?= htmlspecialchars($product['ID']); ?>">

        <label for="Nama_Produk">Nama Produk:</label>
        <input type="text" name="Nama_Produk" value="<?= htmlspecialchars($product['Nama_Produk']); ?>" required>

        <label for="Deskripsi">Deskripsi:</label>
        <textarea name="Deskripsi" required><?= htmlspecialchars($product['Deskripsi']); ?></textarea>

        <label for="Kategori">Kategori:</label>
        <select name="Kategori" required>
            <?php foreach ($kategori_list as $kategori) : ?>
                <option value="<?= $kategori['ID']; ?>" <?= ($product['Kategori'] == $kategori['ID']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($kategori['Kategori']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="Kategori_Liga">Kategori Liga:</label>
        <select name="Kategori_Liga" required>
            <?php foreach ($liga_list as $liga) : ?>
                <option value="<?= $liga['ID']; ?>" <?= ($product['Kategori_Liga'] == $liga['ID']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($liga['Nama_Liga']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <h3>Ukuran & Harga</h3>
        <div id="size-container">
            <?php foreach ($sizes as $size) : ?>
                <div class="size-row">
                    <input type="hidden" name="size_id[]" value="<?= $size['ID']; ?>">
                    <input type="text" name="size[]" value="<?= htmlspecialchars($size['Size_Produk']); ?>" required placeholder="Ukuran">
                    <input type="number" name="harga[]" value="<?= htmlspecialchars($size['Harga']); ?>" required placeholder="Harga">
                    <button type="button" class="remove-size" data-id="<?= $size['ID']; ?>">Hapus</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-size">Tambah Ukuran</button>

        <div class="button-group">
            <a href="homeadmin.php" class="cancel-button">Cancel</a>
            <input type="submit" name="update" value="Update Produk">
        </div>
    </form>
</div>

<script>
document.getElementById("add-size").addEventListener("click", function () {
    var container = document.getElementById("size-container");
    var div = document.createElement("div");
    div.classList.add("size-row");

    div.innerHTML = `
        <input type="hidden" name="size_id[]" value="">
        <input type="text" name="size[]" required placeholder="Ukuran">
        <input type="number" name="harga[]" required placeholder="Harga">
        <button type="button" class="remove-size">Hapus</button>
    `;

    container.appendChild(div);
});

document.addEventListener("click", function (event) {
    if (event.target.classList.contains("remove-size")) {
        var sizeRow = event.target.parentElement;
        var sizeID = event.target.getAttribute("data-id");

        if (sizeID) {
            var deleteInput = document.createElement("input");
            deleteInput.type = "hidden";
            deleteInput.name = "delete_size[]";
            deleteInput.value = sizeID;
            document.getElementById("updateForm").appendChild(deleteInput);
        }

        sizeRow.remove();
    }
});
</script>

</body>
</html>
