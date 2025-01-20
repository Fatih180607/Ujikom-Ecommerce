<?php
$myPDO = new PDO('sqlite:db/db.sqlite3');
$errorMessage = "";  // Variabel untuk menampung pesan error

// Memeriksa apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    // Query untuk mencari username di database`
    $sql = "SELECT * FROM Data_User WHERE Username = :username";
    $stmt = $myPDO->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Mengambil hasil query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row["Password"])) {
        // Cek Role user
        if ($row["Role"] === "Admin") {
            header("Location: homeadmin.php");
            exit;
        } else if ($row["Role"] === "User") {
            header("Location: home.php");
            exit;
        } else {
            echo "Role tidak valid.";
        }
    } else {
        $errorMessage = "Sorry, your username and password are incorect, please try again"; 
       }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="gambar" href="gambar/removebg.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="loginbg">
        <img class="Adainlogo" src="gambar/AdainLogo.png" alt="Adain Logo">
        <h2 class="Loginh2">Login</h2>
        <form action="index.php" method="POST">
            <label for="username"><b>Username</b></label>
            <br />
            <input type="text" id="username" placeholder="Username" name="username" required />
            <br /><br />
            <label for="password"><b>Password</b></label>
            <br />
            <input type="password" id="password" placeholder="Enter Password" name="password" required />
            <br /><br />
            <input class="ButtonLogin" type="submit" value="Login"/>
        </form>
        <?php
        if ($errorMessage) {
            echo "<p class='error-messagelogin'>$errorMessage</p>";
        }
        ?>
        <p class="Loginh2">Don't have an account yet? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
