<?php
$message = "";


// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "list_user";

//$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$myPDO = new PDO('sqlite:db/db.sqlite3');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO Data_User (Username, Email, Password) 
                       VALUES (:username, :email, :hashed_password)";

    $insert = $myPDO->prepare($sql_insert);

    $insert->execute([
        ':username' => $username,
        ':email' => $email,
        ':hashed_password' => $hashed_password,
    ]);
    $message = "Registration successful!";
    header("Location: index.html");
}

?>
