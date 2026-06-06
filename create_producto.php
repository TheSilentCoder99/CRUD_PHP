<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}

require 'conexion.php';

$patron_nombre = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ .,\-_]+$/
';
$patron_precio = '/^\d+([.,]\d+)?$/
';

// COMPRUEBO QUE LOS DATOS BÁSICOS: NOMBRE, PRECIO Y FABRICANTE SE HAYAN INTRODUCIDO, NO ESTÉN VACÍOS Y CONTENGAN NÚMEROS, LETRAS Y SIN ESPACIOS VACÍOS.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre']) && !empty($_POST['precio']) && !empty($_POST['id_fabricante']) && preg_match($patron_nombre,$_POST['nombre']) && preg_match($patron_precio,$_POST['precio'])) {

    $nombre_producto = $_POST['nombre'];
    $precio_producto = (float) $_POST['precio'];
    $id_fabricante = $_POST['id_fabricante'];
    $descripcion = $_POST['descripcion_producto'];

    $imagen = $_FILES['nueva_imagen']['name'];

    $ruta_temporal = $_FILES['nueva_imagen']['tmp_name'];

     // ruta final donde lo quiero guardar yo
     // __DIR__ es una constante de PHP que devuelve la ruta absoluta de la carpeta donde está el archivo PHP actual:
     
    $ruta_final = __DIR__ . '/imagenes/' . $imagen;

    // lo muevo a mi ruta final
    move_uploaded_file($ruta_temporal, $ruta_final);

    // guardo la ruta en una variable para pasarsela a la BD
    $url_imagen_enBD = 'imagenes/'. $imagen;


    $stmt = $conn->prepare("INSERT INTO producto (nombre,precio,id_fabricante,descripcion,imagen) VALUES (:nombre,:precio,:id_fabricante,:descripcion,:imagen)");
    $stmt->execute([':nombre' => $nombre_producto, ':precio' => $precio_producto, ':id_fabricante' => $id_fabricante, ':descripcion' => $descripcion, ':imagen' => $url_imagen_enBD]);

    header('Location: create_producto.php');
    exit();
}

// MOSTRAR TODOS LOS PRODUCTOS
$productos = $conn->prepare("SELECT producto.*, fabricante.nombre AS nombre_fabricante
FROM producto
JOIN fabricante ON producto.id_fabricante = fabricante.id
ORDER BY producto.nombre");
$productos->execute();

// OBTENGO LOS DATOS DE LOS FABRICANTES PARA CONSTRUIR EN EL DESPLEGABLE
$all_fabricantes = $conn->prepare("SELECT * FROM fabricante");
$all_fabricantes->execute();


// CALCULANDO OFFSET Y DIFINIENDO NÚMERO DE PRODUCTOS POR PÁGINA
$productos_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $productos_por_pagina;

// 3. CONTAR EL TOTAL DE PRODUCTOS QUE COINCIDEN CON EL FILTRO
// Necesitas este número para saber cuántas páginas mostrar
// Usa el mismo $where para que el conteo respete la búsqueda activa. Este conteo usa como condición el where de la búsqueda, o sea, solo contará los que coincidan con la búsqueda.
$sql_count = "SELECT COUNT(*) FROM producto JOIN fabricante ON producto.id_fabricante = fabricante.id";

$stmt = $conn->prepare($sql_count);
$stmt->execute();

$total_productos = $stmt->fetchColumn(); // fetchColumn devuelve un solo valor, no un array
$total_paginas = ceil($total_productos / $productos_por_pagina);

$sql = "SELECT producto.*, fabricante.nombre AS nombre_fabricante
        FROM producto
        JOIN fabricante ON producto.id_fabricante = fabricante.id
        LIMIT $productos_por_pagina OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_crear_producto.css">
    <title>Insertar producto</title>
</head>
<a href="All_productos.php">Volver</a>

<body>
    <h2>Añadir producto</h1>
        <!-- ENVIAR DATOS A LA BD NORMALMENTE UTILIZAS POST -->
        <form action="create_producto.php" method="POST" enctype="multipart/form-data">INTRODUCE LOS DATOS:
            <hr>
            <br>
            <!-- NOMBRE -->
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre">
            <br>

            <!-- precio -->
            <label for="precio">Precio:</label>
            <input type="text" name="precio">
            <br>

            <!-- fabricante -->
            <label for="fabricantes">Elige el fabricante:</label>
            <!-- LISTA DESPLEGABLE (TIPO COMBOBOX) QUE MUESTRE LOS FABRICANTES, QUE EL USUARIO ELIJA Y EN BASE A LA ELECCIÓN, OBTIENES EL ID Y LO USAS EN TU CONSULTA -->
            <select name="id_fabricante">
                <?php
                foreach ($all_fabricantes as $fabricante): ?>
                    <option value="<?php echo $fabricante['id'] ?>"><?php echo $fabricante['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <a href="create_fabricante.php" target="_blank">Nuevo fabricante...</a>
            <br>
            <!-- DESCRIPCION -->
            <label for="descripcion_producto">Descripción:</label>
            <input type="text" name="descripcion_producto">
            <br>

            <!-- imagen -->
            <label for="imagen">Imagen:</label>
            <input type="file" name="nueva_imagen" accept="image/*">
            <br>
            <button type="submit">Guardar</button>
            <br>
        </form>
        <hr>

        <?php
        foreach ($productos as $producto): ?>
            <div>
                <h3><?php echo $producto['nombre']; ?></h3>

                <p><?php echo $producto['precio']; ?>€</p>

                <b>
                    <p><?php echo $producto['nombre_fabricante']; ?></p>
                </b>

                <p><?php echo $producto['descripcion']; ?></p>

                <div>
                <img src="<?php echo $producto['imagen']; ?>" alt="foto de producto">
            </div>

                <hr>
            </div>
        <?php endforeach; ?>

    <!-- NAVEGACIÓN DE PÁGINAS -->
<!-- Si estamos en la misma página de la URL, a la clase que rastrea el numeral se le añade la clase "ACTIVO" que es la clase que añade el color rojo a ese numeral. -->
<?php for ($i = 1; $i <= $total_paginas; $i++): 
    $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
    $activo = ($i == $pagina_actual) ? 'activo' : '';
?>
  <a href="create_producto.php?pagina=<?php echo $i; ?>" class="navegacion_crear_producto <?php echo $activo; ?>">
                <?php echo $i; ?>
            </a>
<?php endfor; ?>


</body>

</html>