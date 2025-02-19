<?php

$myPDO = new PDO('sqlite:db/db.sqlite3');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $role='User';

    $sql_insert = "INSERT INTO Data_User (Username, Email, Password, role) 
                       VALUES (:username, :email, :hashed_password, :role)";

    $insert = $myPDO->prepare($sql_insert);

    $insert->execute([
        ':username' => $username,
        ':email' => $email,
        ':hashed_password' => $hashed_password,
        ':role' => $role,
    ]);
    $message = "Registration successful!";
    $_SESSION['Username'] = $username;
    header("Location: index.php");
}

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="icon" type="gambar" href="gambar/jerseyonly_logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="register.css" />
  </head>
  <body>
  <div class="navbar_logo">
<img class="jerseyfylogo" src="gambar/jerseyfy_logo_loginregist.png">
</div>
<div class="containerform">
    <div class="loginbg">
    <div class="signup-login">
            <h3 class="signup">Sign Up</h3>
            <a href="index.php"><h3 class="login">Login</h3></a>
        </div>
      <h2 class="Registeratas">Register</h2>
      <form action="register.php" method="POST" autocomplete="off">
        <label for="username"><b>Username</b></label>
        <br />
        <input
          type="text"
          id="username"
          placeholder="Username*"
          name="username"
          required
        />
        <br /><br/>
        <label for="email"><b>Email</b></label>
        <br />
        <input
          type="email"
          id="email"
          placeholder="Email*"
          name="email"
          required
        />
        <br /><br />
        <label for="password"><b>Password</b></label>
        <br />
        <div class="password-wrapper">
  <input type="password" id="password" placeholder="Password*" name="password" required />
  <i class="fas fa-eye" id="togglePassword"></i>
</div>

        <br /><br />
        <input class="ButtonRegist" type="submit" value="Sign Up" />
      </form>
      <p class="Loginh2">
        Sudah Punya Akun? <a href="index.php">Login disini</a>
      </p>
    </div>
</div>
<script>
  const togglePassword = document.querySelector("#togglePassword");
const passwordField = document.querySelector("#password");

togglePassword.addEventListener("click", function () {
    // Toggle visibility password
    const isPasswordVisible = passwordField.type === "password";
    passwordField.type = isPasswordVisible ? "text" : "password";

    // Toggle icon
    this.classList.toggle("fa-eye-slash");
});

</script>
  </body>
</html>
