<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error = '';

    // 1. Recoger los datos del formulario comprobando que se ingrese una regex que solo acepte letras, números y carácteres especiales, dejando fuera los espacios en blanco.
    if (isset($_POST['usuario']) && isset($_POST['password_user']) && preg_match('/^\S+$/', $_POST['usuario']) || !preg_match('/^\S+$/', $_POST['password'])) {

        $login = $_POST['usuario'];
        $password = $_POST['password_user'];
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
    <link rel="stylesheet" href="estilo_login.css">
    <title>Iniciar sesión</title>
</head>

<body>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- EL FORMULARIO DEBE MANDAR AL USUARIO AL ARCHIVO QUE CONTIENE LA LÓGICA DE VALIDACIÓN. DESDE ESA LÓGICA, SI EL CONTENIDO DEL FORMULARIO ES CORRECTO, ES CUANDO REENVÍAS AL USUARIO A LA PÁGINA DE INICIO-->
     <div class="contenedor-principal">
        <!-- Tarjeta de inicio de sesión -->
        <div class="contenedor">
            <form method="POST" action="login.php">
                <label>Nombre de usuario:</label>
                <input type="text" name="usuario" placeholder="usuario" required>
                
                <label>Contraseña:</label>
                <input type="password" name="password_user" placeholder="••••••••" required>
                
                <input type="submit" value="Enviar">
            </form>
        </div>
        
        <!-- Enlaces debajo de la tarjeta -->
        <div class="enlaces-container">
            <a href="index.php" class="enlace">Continuar sin iniciar sesión</a>
            <a href="registro.php" class="enlace">Registrarse</a>
        </div>
    </div>
</body>

</html>