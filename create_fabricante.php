<?php
require 'conexion.php';

$patron_nombre = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ .,\-_]+$/
';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre']) && preg_match($patron_nombre,$_POST['nombre'])) {

    $nombre_fabricante = $_POST['nombre'];
    $stmt = $conn->prepare("INSERT INTO fabricante (nombre) VALUES (:nombre)");
    $stmt->execute([':nombre' => $nombre_fabricante]);

    header('Location: create_fabricante.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_crear_fabricante.css">
    <title>Insertar fabricante</title>
</head>

<body>
    <h2>Añadir fabricante</h1>
        <form action="create_fabricante.php" method="post">INTRODUCE LOS DATOS:
            <hr>
            <br>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre">
            <button type="submit">Guardar</button>
            <br>
        </form>
        <br>
                <a href="All_fabricantes.php">Volver</a>

        <hr>

        <?php
        foreach ($fabricantes as $fabricante): ?>
            <div>
                <p><?php echo $fabricante['nombre']; ?></p>
            </div>
        <?php endforeach; ?>
</body>
</html>