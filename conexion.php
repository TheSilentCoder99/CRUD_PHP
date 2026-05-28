<?php
$host = '127.0.0.1';
$port = 3307;
$dbname = 'tienda';
$user = 'root';
$passwordbd = 'carnivora04.';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $passwordbd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>


