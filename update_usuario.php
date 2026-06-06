<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}


require 'conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];
} else {
    $id = $_POST['id'];
}

$stmt = $conn->prepare('SELECT * FROM usuario WHERE id = :id');
$stmt->execute(['id' => $id]);
$usuarioActual = $stmt->fetch(PDO::FETCH_ASSOC);

// RECUPERAR DATOS DEL USUARIO ACTUAL EN BASE AL ID QUE SE ME PASE POR GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $conn->prepare('SELECT * FROM usuario WHERE id = :id');

    $stmt->execute(['id' => $id]);

    $usuarioActual = $stmt->fetch(PDO::FETCH_ASSOC);

}

// ACTUALIZAR DATOS DEL USUARIO EN LA BD
// NOTA SOBRE VALIDACIONES EN EL MÉTODO DE ENTRADA:
/*
La regla general es: **la condición del `if` debe ser lo mínimo necesario para saber que tiene sentido procesar la petición**. En este caso, saber que es POST es suficiente.
Todoo lo demás, las comprobaciones de campos vacíos, los patrones, las reglas de negocio, van dentro como validaciones explícitas con sus mensajes de error correspondientes.
Si pones demasiadas condiciones en el `if` principal, estás descartando casos silenciosamente, sin decirle nada al usuario. El usuario envía el formulario y... nada. Sin mensaje, sin redirección, sin explicación.*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // campos obligatorios según diseño de la BD
    $id = $_POST['id'];

    $nombre = $_POST['nuevo_nombre'];
    $apellido1 = $_POST['nuevo_apellido1'];
    $email = $_POST['nuevo_email'];
    $login = $_POST['nuevo_login'];

    // Validaciones de entrada
    $patron_nombres = '/^[a-zA-ZáéíóúüñÑÁÉÍÓÚÜ]+$/';
    $patron_login = '/^[a-zA-Z0-9_]+$/';
    $patron_email = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    // Error a mostrar en caso de que falle alguna validación. DALES FORMA EN CADA IF. DESPUÉS CONDICIONALOS EN EL HTML MÁS ABAJO PARA QUE SE MUESTRE O NO DEPENDIENDO DE LO QUE HAYA PASADO
    $error = '';

    if (empty($nombre) || empty($apellido1) || empty($email) || empty($login)) {

        $error = 'El nombre y los apellidos no pueden estar vacíos.';

    } elseif (!preg_match($patron_nombres, $nombre) || !preg_match($patron_nombres, $apellido1) || !preg_match($patron_login, $login)) {

        $error = 'El nombre y los apellidos solo admite letras.';

    } elseif (!preg_match($patron_email, $email)) {
        $error = 'Ingresa un email válido';
    }

    // NO OBLIGATORIO EN LA BD. SI ESTÁ VACÍO, SE GUARDA COMO NULL
    $apellido2 = $_POST['nuevo_apellido2'];
    if (empty($apellido2)) {
        $apellido2 = null;
    }

    // TRATAMIENTO DE LAS IMÁGENES
    // ALMACENAR IMAGENES EN RUTA TEMPORAL PARA DESPUÉS GUARDARLOS EN LA CARPETA QUE CONTIENE LAS IMÁGENES EN LOCAL
    // guardo el archivo que me envían en el formulario
    $imagen_usuario = $_FILES['nueva_imagen']['name'];

    // ruta temporal donde lo guarda php
    $ruta_temporal = $_FILES['nueva_imagen']['tmp_name'];

    // ruta final donde lo quiero guardar yo
    // __DIR__ es una constante de PHP que devuelve la ruta absoluta de la carpeta donde está el archivo PHP actual:
   $ruta_final = __DIR__ . '/fotos_perfil/' . $imagen_usuario;

    // lo muevo a mi ruta final
    move_uploaded_file($ruta_temporal, $ruta_final);

    // guardo la ruta relativa en una variable para pasarsela a la BD
    $url_imagen_enBD = 'fotos_perfil/'. $imagen_usuario;



    if (empty($error)) {
        $stmt = $conn->prepare('UPDATE usuario SET nombre = :nombre_nuevo, apellido1 = :apellido1_nuevo, apellido2 = :apellido2_nuevo, email = :email_nuevo, login = :login_nuevo, foto_perfil = :nueva_foto_perfil WHERE id = :id');

        $stmt->execute(['nombre_nuevo' => $nombre, 'apellido1_nuevo' => $apellido1, 'apellido2_nuevo' => $apellido2, 'email_nuevo' => $email, 'id' => $id, 'login_nuevo' => $login, 'nueva_foto_perfil' => $url_imagen_enBD]);

        header('Location: panel_usuario.php');
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_update_usuario.css">
    <title>Actualizar usuario</title>
</head>

<body>
    <a href="index.php">Volver</a>

    <h3>Datos actuales</h3>
    <div>

    <h3 for="">Foto de perfil</h3>
        <br>
        <img class="foto_usuario" src="<?php echo $usuarioActual["foto_perfil"] ?>" alt="foto de perfil actual">
        <br>

        <h3 for="">Nombre</h3>
        <br>
        <label for=""><?php echo $usuarioActual["nombre"] ?></label>
        <br>


        <h3 for="">Apellido 1</h3>
        <br>
        <label for=""> <?php echo $usuarioActual["apellido1"] ?></label>
        <br>


        <h3 for="">Apellido 2</h3>
        <br>
        <label for=""><?php echo $usuarioActual["apellido2"] ?></label>
        <br>

        <h3 for="">Email</h3>
        <br>
        <label for=""><?php echo $usuarioActual["email"] ?></label>
        <br>

        <h3 for="">Nombre de usuario</h3>
        <br>
        <label for=""><?php echo $usuarioActual["login"] ?></label>
        <br>
    </div>

    <!-- Con este formulario actualizamos el producto actual en la BD -->

    <h2>Actualiza los datos</h2>

    <form action="update_usuario.php" method="post" enctype="multipart/form-data">

        <!-- Envío el ID de forma oculta a través del mismo formulario para poder hacer el update -->
        <input type="hidden" name="id" value="<?php echo $usuarioActual['id'] ?>">

        <label for="nuevo_nombre">Nuevo nombre: </label>
        <br>
        <input type="text" name="nuevo_nombre" value="<?php echo $usuarioActual["nombre"] ?>">
        <br>

        <label for="nuevo_apellido1">Nuevo apellido 1</label>
        <br>
        <input type="text" name="nuevo_apellido1" value="<?php echo $usuarioActual["apellido1"] ?>">
        <br>

        <label for="nuevo_apellido2">Nuevo apellido 2</label>
        <br>
        <input type="text" name="nuevo_apellido2" value="<?php echo $usuarioActual["apellido2"] ?>">
        <br>

        <label for="nuevo_email"> Nuevo email: </label>
        <br>
        <input type="text" name="nuevo_email" value="<?php echo $usuarioActual["email"] ?>">
        <br>

        <label for="nuevo_login"> Nuevo nombre de usuario: </label>
        <br>
        <input type="text" name="nuevo_login" value="<?php echo $usuarioActual["login"] ?>">
        <br>

        <label for="nueva_imagen"> Nueva foto de perfil: </label>
        <br>
        <input type="file" name="nueva_imagen">
        <br>

        <button type="submit" onclick="return confirm('¿Guardar modificaciones?')">Actualizar</button>
    </form>
    <?php
    if (!empty($error)) {

        echo '<script>alert("' . $error . '")</script>';
    }

    ?>

</body>

</html>