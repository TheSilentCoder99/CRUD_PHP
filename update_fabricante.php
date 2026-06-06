<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}

require 'conexion.php';

// CONDICIONAL CON GET QUE MUESTRA TODOS LOS DATOS DEL OBJETO A TRAVÉS DEL ID RECIBIDO MEDIANTE GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {

    $id_fabricante = $_GET['id'];

    // Traigo los datos del fabricante en concreto
    $fabricante = $conn->prepare("SELECT * FROM fabricante WHERE id = :id ");
    $fabricante->execute([':id' => $id_fabricante]);
    $fabricanteActual = $fabricante->fetch(PDO::FETCH_ASSOC);

}

// CONDICIONAL CON POST PARA QUE SE EJECUTE LA ACTUALIZACIÓN QUE LE ENVIAMOS A TRAVÉS DEL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nuevo_nombre'])) {

    $id_fabricante = $_POST['id'];  // ← Recibo el ID del campo hidden
    $nuevo_nombre = $_POST['nuevo_nombre'];

    // Actualizo los datos en la BD
    $stmt = $conn->prepare("UPDATE fabricante SET nombre = :nombre WHERE id = :id");
    $stmt->execute(['nombre' => $nuevo_nombre, 'id' => $id_fabricante]);

    // UNA VEZ TERMINADA LA ACTUALIZACIÓN, REDIRIJO A LA PÁGINA PRINCIPAL CON TODOS LOS FABRICANTES
    header('Location: All_fabricantes.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_update_fabricante.css">
    <title>Actualizar fabricante</title>
</head>

<body>
    <a href="All_fabricantes.php">Volver</a>
    <h2>Actualizar fabricante</h1>

        <div>
            <!-- Muestro los datos del fabricante actual -->
            <label for="nombre"><?php echo $fabricanteActual["nombre"] ?></label>
            <label for="nuevo_nombre">Ingresa el nuevo nombre: </label>

            <!-- Con este formulario actualizamos el fabricante actual en la BD -->
            <form action="update_fabricante.php" method="post">
                <input type="hidden" name="id" value="<?php echo $fabricanteActual['id'] ?>">
                <input type="text" name="nuevo_nombre">
                <button type="submit">Actualizar</button>
            </form>


        </div>

</body>
<link rel="stylesheet" href="styles.css">

</html>