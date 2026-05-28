<?php

require 'conexion.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $conn->prepare('SELECT producto.*, fabricante.nombre AS nombre_fabricante
    FROM producto 
    INNER JOIN fabricante ON producto.id_fabricante = fabricante.id
    WHERE producto.id = :id');

    $stmt->execute(['id' => $id]);

    $este_producto = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Ficha producto</title>
</head>

<body>
    <?php foreach ($este_producto as $producto): ?>
        <div class="ficha_este_producto">
            <h2><?php echo $producto['nombre']; ?></h2>
            <div>
                <img src="<?php echo $producto['imagen'];?>">
            </div>
            <br>
            <p><?php echo $producto['precio']; ?> €</p>
            <p><?php echo $producto['nombre_fabricante']; ?></p>
            <p class="area_descripcion"><?php echo $producto['descripcion']; ?></p>
        </div>
    <?php endforeach; ?>
    <br>
    <a href="index.php">🔙 Volver</a>

</body>

</html>