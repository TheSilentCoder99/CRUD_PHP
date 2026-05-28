<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit();
}


require 'conexion.php';

// RECUPERAR DATOS DEL PRODUCTO ACTUAL EN BASE AL ID QUE SE ME PASE POR GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $conn->prepare('SELECT producto.*, fabricante.nombre AS nombre_fabricante
 FROM producto 
 INNER JOIN fabricante ON producto.id_fabricante = fabricante.id
 WHERE producto.id = :id');

    $stmt->execute(['id' => $id]);
    $productoActual = $stmt->fetch(PDO::FETCH_ASSOC);

}


// ACTUALIZAR DATOS DEL PRODUCTO EN LA BD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nuevo_nombre']) && !empty($_POST['nuevo_precio']) && !empty($_POST['id_fabricante'])) {

    $id = $_POST['id'];
    $nombre = $_POST['nuevo_nombre'];
    $precio = $_POST['nuevo_precio'];
    $id_fabricante = $_POST['id_fabricante'];
    $descripcion = $_POST['nueva_descripcion'];

    // ALMACENAR IMAGENES EN RUTA TEMPORAL PARA DESPUÉS GUARDARLOS EN LA CARPETA QUE CONTIENE LAS IMÁGENES EN LOCAL
    // guardo el archivo que me envían en el formulario
    $imagen = $_FILES['nueva_imagen']['name'];

    // ruta temporal donde lo guarda php
    $ruta_temporal = $_FILES['nueva_imagen']['tmp_name'];

    // ruta final donde lo quiero guardar yo
    // __DIR__ es una constante de PHP que devuelve la ruta absoluta de la carpeta donde está el archivo PHP actual:
    $ruta_final = $ruta_final = __DIR__ . '/imagenes/' . $imagen;

    // lo muevo a mi ruta final
    move_uploaded_file($ruta_temporal, $ruta_final);

    // guardo la ruta en una variable para pasarsela a la BD
    $url_imagen_enBD = 'imagenes/'. $imagen;

    $stmt = $conn->prepare('UPDATE producto SET nombre = :nombre, precio = :precio, id_fabricante = :id_fabricante, descripcion = :descripcion, imagen = :imagen WHERE id = :id');

    $stmt->execute(['nombre' => $nombre, 'precio' => $precio, 'id_fabricante' => $id_fabricante, 'descripcion' => $descripcion, 'imagen' => $url_imagen_enBD, 'id' => $id]);

    header('Location: All_productos.php');
}

// TRAIGO TODOS LOS DATOS DE LOS FABRICANTES PARA USARLOS EN EL SELECT
$all_fabricantes = $conn->prepare("SELECT * FROM fabricante");
$all_fabricantes->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Actualizar producto</title>
</head>

<body>
    <a href="All_productos.php">Volver</a>
    <h3>Información del producto</h3>
    <div>

        <label for=""><?php echo $productoActual["nombre"] ?></label>


        <label for=""><?php echo $productoActual["precio"] ?>€</label>
        <br>

        <label for=""><?php echo $productoActual["nombre_fabricante"] ?></label>
        <br>

        <label for=""><?php echo $productoActual["descripcion"] ?></label>
        <br>

        <br>
        <div>
            <img src="<?php echo $productoActual['imagen'];?>" alt="foto de producto">
        </div> 
        <br>
    </div>

    <!-- Con este formulario actualizamos el producto actual en la BD -->

    <form action="update_producto.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $productoActual['id'] ?>">


        <label for="nuevo_nombre">Ingresa el nuevo nombre: </label>
        <br>
        <input type="text" name="nuevo_nombre" value="<?php echo $productoActual["nombre"] ?>">

        <br>
        <br>


        <label for="nuevo_precio">Ingresa el nuevo precio: </label>
        <br>
        <input type="text" name="nuevo_precio" value="<?php echo $productoActual["precio"] ?>">

        <br>
        <br>

        <label for="nuevo_fabricante">Ingresa el nuevo fabricante: </label>
        <br>

        <select name="id_fabricante">
            <?php
            foreach ($all_fabricantes as $fabricante): ?>
                <option value="<?php echo $fabricante['id'] ?>"><?php echo $fabricante['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="nueva_descripcion">Ingresa la nueva descripción: </label>
        <br>
        <input type="text" name="nueva_descripcion" value="<?php echo $productoActual["descripcion"] ?>"
            accept="image/*">

        <br>
        <br>

        <label for="nueva_imagen">Ingresa la nueva imagen: </label>
        <br>
        <input type="file" name="nueva_imagen">
        <br>
        <br>
        <button type="submit" onclick="return confirm('¿Guardar modificaciones?')">Actualizar</button>
    </form>

</body>

</html>