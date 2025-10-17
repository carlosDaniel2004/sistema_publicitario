<?php
// config/db.php

$host = 'localhost';
$dbname = 'fulbito_db';
$user = 'daniel'; // Usuario por defecto en XAMPP
$pass = 'daniel1908@Peru';     // Contraseña por defecto en XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
