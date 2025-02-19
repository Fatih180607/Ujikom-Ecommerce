<?php
session_start();
$myPDO = new PDO('sqlite:db/db.sqlite3');

if (isset($_SESSION['username'])) {
    if ($_SESSION["role"] === "Admin") {
        header("Location: homeadmin.php");
        exit;
    } else if ($_SESSION["role"] === "User") {
        header("Location: home.php");
        exit;
    } else {
        echo "Role tidak valid.";
        exit();
    }
}

$errorMessage = ""; // Pastikan variabel ini dideklarasikan sebelum digunakan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM Data_User WHERE Username = :username";
        $stmt = $myPDO->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row["Password"])) {
            $_SESSION['username'] = $row['Username'];
            $_SESSION['role'] = $row['Role'];
            $_SESSION['email'] = $row['Email']; // Tambahkan ini agar email juga tersimpan

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
            $errorMessage = "Sorry, your username and password are incorrect, please try again"; 
        }
    } else {
        $errorMessage = "Please fill in both fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="gambar" href="gambar/jerseyonly_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar_logo">
        <img class="jerseyfylogo" src="gambar/jerseyfy_logo_loginregist.png">
    </div>
    <div class="hero-section"></div>

    <div class="containerform">
        <div class="loginbg">
            <div class="signup-login">
                <a href="register.php"><h3 class="signup">Sign Up</h3></a>
                <h3 class="login">Login</h3>
            </div>
            <h2 class="loginh2">Login</h2>

            <form action="index.php" method="POST" autocomplete="off">
                <label for="username"><b>Username</b></label><br/>
                <input type="text" id="username" placeholder="Username*" name="username" required />
                <br /><br />
                <label for="password"><b>Password</b></label><br/>
                <div class="password-wrapper">
                    <input type="password" id="password" placeholder="Password*" name="password" required />
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
                <br /><br />
                <input class="ButtonLogin" type="submit" value="Login"/>
            </form>
            <a href="home.php"><input class="ButtonGuest" type="submit" value="Masuk sebagai guest"></a>
            <?php if (!empty($errorMessage)) : ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <p class="registeraccount">Belum memiliki akun?<a href="register.php"> Daftar di sini</a></p>
        </div>
    </div>
    
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const passwordField = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const isPasswordVisible = passwordField.type === "password";
            passwordField.type = isPasswordVisible ? "text" : "password";
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>
</html>
