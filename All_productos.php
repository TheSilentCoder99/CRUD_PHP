<?php

require 'conexion.php';

// BUSCAR UN PRODUCTO
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['p_buscado'])) {

    $nombre_producto = "%" . $_GET['p_buscado'] . "%";

    $stmt = $conn->prepare("SELECT producto.*, fabricante.nombre AS nombre_fabricante
FROM producto
INNER JOIN fabricante ON producto.id_fabricante = fabricante.id
WHERE producto.nombre LIKE :producto_buscado");

    $stmt->execute(['producto_buscado' => $nombre_producto]);

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    // SI NO SE ENCUENTRA, MOSTRAR TODOS
    $stmt = $conn->prepare("SELECT producto.*, fabricante.nombre AS nombre_fabricante
FROM producto
JOIN fabricante ON producto.id_fabricante = fabricante.id");

    $stmt->execute();

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

// BOTON MOSTRAR TODOS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['mostrar_todos'])) {

    $stmt = $conn->prepare("SELECT producto.*, fabricante.nombre AS nombre_fabricante
FROM producto
JOIN fabricante ON producto.id_fabricante = fabricante.id");
    $stmt->execute();
    $productos = $stmt->fetch(PDO::FETCH_ASSOC);
}

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
<html>

<head>
    <link rel="stylesheet" href="estilo_all_fabprod.css">
</head>

<body>
    <h2>Productos</h2>

    <a href="create_producto.php" target="_blank">Añadir producto</a>
    <br>
    <a href="All_fabricantes.php">Ir a fabricantes</a>
    <br>
    <a href="index.php">Página principal</a>

    <!-- FORMULARIO QUE ENVÍA LA BÚSQUEDA Y LA PETICIÓN DE MOSTRAR TODOS -->
    <form method="get" action="All_productos.php">
        <input type="text" name="p_buscado" placeholder="Buscar producto...">
        <button type="submit">Buscar</button>
        <br>
        <button type="submit" name="mostrar_todos">Mostrar todos</button>
        <br>
    </form>
    <br>

    <?php
    foreach ($productos as $producto): ?>
        <div>
            <!-- Datos de cada producto -->
            <h2><?php echo $producto['nombre']; ?></h2>
            <p><?php echo $producto['precio']; ?>€</p>
            <p><?php echo $producto['descripcion']; ?></p>
            <p><?php echo $producto['nombre_fabricante']; ?></p>

            <!-- Añades los botones de editar y eliminar -->
            <a href="update_producto.php?id=<?php echo $producto["id"] ?>" target="_blank">Editar</a>

            <a href="delete_producto.php?id=<?php echo $producto['id']; ?>"
                onclick="return confirm('¿Seguro que quieres eliminar este producto?')">
                Eliminar
            </a>

        </div>
    <?php endforeach; ?>

    <hr>
    <!-- NAVEGACIÓN DE PÁGINAS -->
    <div class="paginacion-container">
        <?php for ($i = 1; $i <= $total_paginas; $i++):
            $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
            $activo = ($i == $pagina_actual) ? 'activo' : '';
            ?>
            <a href="All_productos.php?pagina=<?php echo $i; ?>" class="<?php echo $activo; ?>">
                <?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

</body>

</html>