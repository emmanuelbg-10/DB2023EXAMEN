<?php
$user = 'root';
$password = '';
$db = 'filmdb';
$dsn = "mysql:host=localhost;dbname=" . $db;

try {
 $conn = new PDO($dsn, $user, $password);
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
  die("Error en la conexion: ". $ex->getMessage());
}