<?php

$coinciden = true;
$iserror = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // RECOJO LOS DATOS DEL USUARIO A INSERTAR
   $nombre_registrado = $_POST['nombre'];
$apellido1_registrado = $_POST['apellido1'];
$apellido2_registrado = $_POST['apellido2'];
$email_registrado = $_POST['email'];
$login_registrado = $_POST['login'];
$password_registrada = $_POST['password'];
$comprobacion_password_registrada = $_POST['password_comprobacion'];

// VALIDACIONES DE ENTRADA

if (empty($nombre_registrado) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $nombre_registrado)) {
    $iserror = 'El nombre es obligatorio y solo puede contener letras.';
} elseif (empty($apellido1_registrado) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $apellido1_registrado)) {
    $iserror = 'El primer apellido es obligatorio y solo puede contener letras.';
} elseif (empty($email_registrado) || !preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email_registrado)) {
    $iserror = 'El email es obligatorio y debe tener un formato válido.';
} elseif (empty($login_registrado) || !preg_match('/^[a-zA-Z0-9]+$/', $login_registrado)) {
    $iserror = 'El login es obligatorio y solo puede contener letras y números, sin espacios.';
} elseif (empty($password_registrada)) {
    $iserror = 'La contraseña es obligatoria.';
} elseif ($password_registrada !== $comprobacion_password_registrada) {
    $iserror = 'Las contraseñas no coinciden.';
}

    // TRATAMIENTO DE LAS IMÁGENES
    $imagen_usuario = $_FILES['nueva_imagen']['name'];

    // ruta temporal donde lo guarda php
    $ruta_temporal = $_FILES['nueva_imagen']['tmp_name'];

    // ruta final donde lo quiero guardar yo
    // __DIR__ es una constante de PHP que devuelve la ruta absoluta de la carpeta donde está el archivo PHP actual:
    $ruta_final = __DIR__ . '/fotos_perfil/' . $imagen_usuario;

    // lo muevo a mi ruta final
    move_uploaded_file($ruta_temporal, $ruta_final);

    // guardo la ruta relativa en una variable para pasarsela a la BD
    $url_imagen_enBD = 'fotos_perfil/' . $imagen_usuario;


    // TENDRÍAS QUE COMPROBAR QUE EL USUARIO NO EXISTE YA
    // 2. Buscar el usuario en la BD
    require 'conexion.php';
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = :email_existente");
    $stmt->execute([':email_existente' => $email_registrado]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


    // COMPRUEBO QUE LAS CONTRASEÑAS COINCIDAN
    if ($password_registrada != $comprobacion_password_registrada) {
        $coinciden = false;
        $iserror = 'Las contraseñas deben ser iguales.';

    } elseif (($usuario['email']) === $email_registrado) {
        $iserror = 'Este email ya pertenece a un usuario registrado en la BD.';
    } else {

    if(empty($iserror)) {

        // SI COINCIDEN, GUARDO EL HASH EN LA BD, NO LA CONTRASEÑA EN TEXTO PLANO
        $hash = password_hash($password_registrada, PASSWORD_BCRYPT);

        $stm = $conn->prepare('INSERT INTO usuario (nombre,apellido1,apellido2,email,login,password,foto_perfil) VALUES (:nombre,:apellido1,:apellido2, :email, :login_usuario, :password_usuario, :foto_perfil_registrada)');

        $stm->execute(['nombre' => $nombre_registrado, 'apellido1' => $apellido1_registrado, 'apellido2' => $apellido2_registrado, 'email' => $email_registrado, 'login_usuario' => $login_registrado, 'password_usuario' => $hash, 'foto_perfil_registrada' => $url_imagen_enBD]);

        $iserror = 'Registro completado.';

        header('Location:login.php');

    }

}
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_update_usuario.css">
    <title>Registro</title>
</head>

<body>

    <h1>Completa los campos</h1>
      
    <form action="registro.php" method="post">
        <br>
        <br>
        <label for="nueva_imagen"> Elige una foto de perfil: </label>
        <br>
        <input type="file" name="nueva_imagen">
        <br>
        <br>

        <label for="">Nombre <span style="color: red;"> *</span></label>
        <input type="text" name="nombre">
        <br>

        <label for="">Apellido 1 <span style="color: red;"> *</span></label>
        <input type="text" name="apellido1">
        <br>

        <label for="">Apellido 2</label>
        <input type="text" name="apellido2">

        <br>
        <label for="">email<span style="color: red;"> *</span></label>
        <input type="text" name="email">

        <br>

        <label for="">Nombre de usuario<span style="color: red;"> *</span></label>
        <input type="text" name="login">
        <br>


        <label for="password">Contraseña<span style="color: red;"> *</span></label>
        <input type="password" name="password">

        <br>

        <label for="password_comprobacion">Introduce tu contraseña otra vez: <span style="color: red;"> *</span></label>
        <input type="password" name="password_comprobacion">
        <br>
        <br>

        <?php

        echo '<h4>' . $iserror . '</h4>';

        ?>

        <button type="submit">Registrarse</button>
        <br>

    </form>
    <br>
    <a href="index.php">Continuar sin iniciar sesión</a>
    <br>
    <a href="login.php">Volver</a>


    <br>
</body>

</html>