<?php

session_start();

require 'conexion.php';

$stm = $conn->prepare("SELECT * FROM usuario WHERE id = :id_usuario");

$stm->execute(['id_usuario' => $_SESSION['usuario_id']]);

$este_usuario = $stm->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_panel_usuario.css">
    <title>Panel de usuario</title>
</head>

<body>
    <a href="index.php">Volver</a>
    <br>
   
        <img class="foto_usuario" src="<?php echo $este_usuario['foto_perfil']?>" alt="foto de perfil del usuario" width="200" height="200">
   
    <br>
    <h1><?php echo $este_usuario['nombre'] . " " . $este_usuario['apellido1'] ?></h1>
    <?php
    echo "<p>" . $este_usuario['nombre'] . "</p>";
    echo "<p>" . $este_usuario['apellido1'] . "</p>";
    echo "<p>" . $este_usuario['apellido2'] . "</p>";
    echo "<p>" . $este_usuario['email'] . "</p>";
    echo "<p>" . $este_usuario['login'] . "</p>";
    ?>
    <a href="update_usuario.php?id=<?php echo $este_usuario['id']; ?>" target="_blank">Editar usuario</a>

    <h2>Ajustes</h2>


</body>

</html>