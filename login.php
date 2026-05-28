<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error = '';

    // 1. Recoger los datos del formulario
    if (isset($_POST['usuario']) && isset($_POST['password'])) {
        $login = $_POST['usuario'];
        $password = $_POST['password'];
    }


    // 2. Buscar el usuario en la BD
    require 'conexion.php';
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE login = :login");
    $stmt->execute([':login' => $login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


    // 3. Verificar contraseña
    if ($usuario && password_verify($password, $usuario['password'])) {

        // 4. Credenciales correctas: guardar en sesión y redirigir al usuario a la página de inicio.
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        header("Location: index.php");
        exit();

    } else {
        // 5. Credenciales incorrectas: mostrar error
        $error = "Usuario o contraseña incorrectos";
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
    <title>Iniciar sesión</title>
</head>

<body>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- EL FORMULARIO DEBE MANDAR AL USUARIO AL ARCHIVO QUE CONTIENE LA LÓGICA DE VALIDACIÓN. DESDE ESA LÓGICA, SI EL CONTENIDO DEL FORMULARIO ES CORRECTO, ES CUANDO REENVÍAS AL USUARIO A LA PÁGINA DE INICIO-->
    <form action="login.php" method="post">
        <label for="usuario">Nombre de usuario: </label><br>
        <input type="text" name="usuario">
        <br>
        <br>

        <label for="password">Contraseña: </label>
        <br>
        <input type="password" name="password">
        <br>
        <br>
        <button type="submit">Enviar</button>
        <br>
    </form>
    <br>
    <a href="index.php">Continuar sin iniciar sesión</a>
    <br>
</body>
</html>