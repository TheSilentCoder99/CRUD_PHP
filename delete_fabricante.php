<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {

    $id_fabricante = $_GET['id'];

    // Elimino el fabricante cuyo ID se me ha pasado mediante GET
    $este_fabricante = $conn->prepare("DELETE FROM fabricante WHERE id = :id ");
    $este_fabricante->execute([':id' => $id_fabricante]);

    header('Location: All_fabricantes.php');
    exit();
}