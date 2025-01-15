    <?php
    // Menghubungkan ke database MySQL
    $servername = "localhost";
    $username = "root"; // Username untuk XAMPP
    $password = ""; // Password untuk XAMPP
    $dbname = "list_user"; // Nama database

    // Membuat koneksi
    //$conn = new mysqli($servername, $username, $password, $dbname,3306);

    $myPDO = new PDO('sqlite:db/db.sqlite3');

    // Memeriksa apakah form sudah disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Mendapatkan data dari form
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query untuk mencari username di database
        $sql = "SELECT * FROM Data_User WHERE Username = '$username'";

        // Mengeksekusi query
        $result = $myPDO->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $row["Password"])) {
            // Jika login berhasil, arahkan ke home.html
            header("Location: home.html");
            exit;
        } else {
            // Jika password salah
            echo "Username atau Password salah.";
        }

        // Menutup koneksi
        //$conn->close();
    }
    ?>
