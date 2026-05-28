<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {

    $id_producto = $_GET['id'];

    // Elimino el producto cuyo ID se me ha pasado mediante GET
    $producto = $conn->prepare("DELETE FROM producto WHERE id = :id ");
    $producto->execute([':id' => $id_producto]);
    header('Location: All_productos.php');
    exit();

}