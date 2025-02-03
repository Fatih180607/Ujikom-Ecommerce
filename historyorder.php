<?php
session_start();

if (isset($_SESSION['role'])) {
  if($_SESSION["role"] <> "Admin"){
  header("Location: home.php");
  exit;  
  }
} else{
  header("Location:index.php");
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>History Order</title>
    <link rel="icon" type="gambar" href="gambar/removebg.png">
  </head>
  <body>
    <h1>Ini History Order User</h1>
  </body>
</html>