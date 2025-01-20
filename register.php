<?php

$myPDO = new PDO('sqlite:db/db.sqlite3');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
    header("Location: index.php");
}

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="icon" type="gambar" href="gambar/removebg.png">
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="loginbg">
      <img class="Adainlogo" src="gambar/AdainLogo.png" alt="Adain Logo" />
      <h2 class="Loginh2">Register</h2>
      <form action="register.php" method="POST">
        <label for="username"><b>Username</b></label>
        <br />
        <input
          type="text"
          id="username"
          placeholder="Enter Username"
          name="username"
          required
        />
        <br /><br/>
        <label for="email"><b>Email</b></label>
        <br />
        <input
          type="email"
          id="email"
          placeholder="Enter Email"
          name="email"
          required
        />
        <br /><br />
        <label for="password"><b>Password</b></label>
        <br />
        <input
          type="password"
          id="password"
          placeholder="Enter Password"
          name="password"
          required
        />
        <br/><br/>
        <label for="role"><b>Role</b></label>
        <select class="DropdownRole" id="role" name="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="Admin">Admin</option>
          <option value="User">User</option>
        </select>
        <br /><br />
        <input class="ButtonRegist" type="submit" value="Create Account" />
      </form>
      <p class="Loginh2">
        Already have an account? <a href="index.php">Login here</a>
      </p>
    </div>
  </body>
</html>
