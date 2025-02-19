    <?php
    try {
        session_start();
        $db = new PDO('sqlite:db/db.sqlite3');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ambil daftar liga
        $liga_stmt = $db->query("SELECT ID, Nama_Liga FROM Kategori_liga");
        $liga_list = $liga_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar kategori
        $kategori_stmt = $db->query("SELECT ID, Kategori FROM Kategori");
        $kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Inisialisasi parameter query
        $search_query = "%";
        $liga_query = "";
        $kategori_query = "";

        // Cek apakah ada pencarian nama produk
        if (!empty($_GET['Search_Query'])) {
            $search_query = "%" . $_GET['Search_Query'] . "%";
        }

        // Cek apakah ada filter liga
        if (!empty($_GET['Liga'])) {
            $liga_query = "AND Produk.Kategori_Liga = :Liga";
        }

        // Cek apakah ada filter kategori
        if (!empty($_GET['Kategori'])) {
            $kategori_query = "AND Produk.Kategori = :Kategori";
        }

        // Query produk
        $sql = "SELECT Produk.ID, Produk.Nama_Produk, Produk.Deskripsi, Produk.Gambar, 
                    Kategori.Kategori AS Nama_Kategori,
                    (SELECT MIN(Harga) FROM SizeProduct WHERE SizeProduct.ID_Product = Produk.ID) AS Harga_Termurah
                FROM Produk 
                JOIN Kategori ON Produk.Kategori = Kategori.ID
                WHERE Produk.Nama_Produk LIKE :Nama_Produk $liga_query $kategori_query";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':Nama_Produk', $search_query, PDO::PARAM_STR);

        if (!empty($_GET['Liga'])) {
            $stmt->bindValue(':Liga', $_GET['Liga'], PDO::PARAM_INT);
        }

        if (!empty($_GET['Kategori'])) {
            $stmt->bindValue(':Kategori', $_GET['Kategori'], PDO::PARAM_INT);
        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Home</title>
        <link rel="icon" type="image/png" href="gambar/jerseyonly_logo.png" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="./home.css" />
    </head>

    <body>
        <div class="Navbar">
            <a href="home.php">
                <img class="LogoNavbar" src="gambar/jerseyfy_logo.png" alt="Logo" />
            </a>
            <div class="content_navbar">
                <a href="#aboutus" class="nav-link" id="about-link">About Us</a>
                <div class="searchbar">
                    <input type="search" id="searchproduk" autocomplete="off" name="Search_Query" placeholder="Search berdasarkan nama" value="<?= htmlspecialchars($_GET['Search_Query'] ?? '') ?>">

                    <select class="dropdown_kategori" name="Kategori" id="kategori">
                        <option value="">Semua Produk</option>
                        <?php
                        foreach ($kategori_list as $kategori) {
                            $selected = (isset($_GET['Kategori']) && $_GET['Kategori'] == $kategori['ID']) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($kategori['ID']) . "' $selected>" . htmlspecialchars($kategori['Kategori']) . "</option>";
                        }
                        ?>
                    </select>

                    <select class="dropdown_liga" name="Liga" id="liga">
                        <option value="">Semua Liga</option>
                        <?php
                        foreach ($liga_list as $liga) {
                            $selected = (isset($_GET['Liga']) && $_GET['Liga'] == $liga['ID']) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($liga['ID']) . "' $selected>" . htmlspecialchars($liga['Nama_Liga']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="button-container">
                    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>

                <div class="profile-dropdown">
                    <i class="fa-solid fa-user profile-icon"></i>
                    <div class="dropdown-content">
                        <p><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
                        <p><?= htmlspecialchars($_SESSION['email']) ?></p>
                        <div class="buttoneditandlogout">
                        <a href="logout.php" class="logout-button">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h1>Daftar Produk</h1>
        <div class="product-container">
            <?php
            if ($products) {
                foreach ($products as $product) {
                    echo '<a href="detailproduct.php?id=' . urlencode($product['ID']) . '" class="product-card-link">';
                    echo '<div class="product-card">';
                    echo '<img src="' . htmlspecialchars($product['Gambar']) . '" alt="' . htmlspecialchars($product['Nama_Produk']) . '" class="product-image">';
                    echo '<h2>' . htmlspecialchars($product['Nama_Produk']) . '</h2>';
                    echo '<p class="Tag_Kategori_Card">Kategori: ' . htmlspecialchars($product['Nama_Kategori']) . '</p>';

                    if ($product['Harga_Termurah'] !== null) {
                        echo '<p class="Harga">Price: Rp ' . number_format($product['Harga_Termurah'], 0, ',', '.') . '</p>';
                    } else {
                        echo '<p class="Harga">Price: Tidak tersedia</p>';
                    }

                    echo '</div></a>';
                }
            } else {
                echo '<p>Tidak ada produk yang tersedia.</p>';
            }
            ?>
        </div>

        <div class="hero-section" id="aboutus">
            <img src="gambar/antonydua.jpg" alt="Banner" class="hero-image">
            <div class="overlay"></div>
            <div class="hero-text">
                <h2>About Us</h2>
                <p>Selamat datang di Jerseyfy! Kami menyediakan berbagai macam jersey berkualitas tinggi untuk para pecinta sepak bola.
                    Dengan koleksi dari berbagai liga dan klub favorit Anda, kami berkomitmen untuk memberikan produk terbaik dengan harga yang kompetitif.</p>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelector(".profile-icon").addEventListener("click", function() {
                    document.querySelector(".dropdown-content").classList.toggle("active");
                });

                window.addEventListener("click", function(e) {
                    if (!document.querySelector(".profile-dropdown").contains(e.target)) {
                        document.querySelector(".dropdown-content").classList.remove("active");
                    }
                });

                const searchInput = document.getElementById("searchproduk");
                const kategoriDropdown = document.getElementById("kategori");
                const ligaDropdown = document.getElementById("liga");

                function applyFilter() {
                    const searchQuery = searchInput.value.trim();
                    const kategori = kategoriDropdown.value;
                    const liga = ligaDropdown.value;

                    let urlParams = new URLSearchParams();
                    if (searchQuery) urlParams.set("Search_Query", searchQuery);
                    if (kategori) urlParams.set("Kategori", kategori);
                    if (liga) urlParams.set("Liga", liga);

                    window.location.search = urlParams.toString();
                }

                kategoriDropdown.addEventListener("change", applyFilter);
                ligaDropdown.addEventListener("change", applyFilter);

                searchInput.addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        applyFilter();
                    }
                });
            });
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("about-link").addEventListener("click", function(event) {
                    event.preventDefault(); // Mencegah lompatan langsung
                    document.getElementById("aboutus").scrollIntoView({
                        behavior: "smooth"
                    });
                });
            });
        </script>
    </body>

    </html>