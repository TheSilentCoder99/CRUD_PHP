<?php

require 'conexion.php';

$where = "WHERE 1=1";
$params = [];

if (!empty($_GET['p_buscado'])) {
    $where .= " AND producto.nombre LIKE :p_buscado";
    $params[':p_buscado'] = '%' . $_GET['p_buscado'] . '%';
}

$productos_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $productos_por_pagina;

$sql_count = "SELECT COUNT(*) FROM producto JOIN fabricante ON producto.id_fabricante = fabricante.id $where";
$stmt = $conn->prepare($sql_count);
$stmt->execute($params);
$total_productos = $stmt->fetchColumn();
$total_paginas = ceil($total_productos / $productos_por_pagina);

$sql = "SELECT producto.*, fabricante.nombre AS nombre_fabricante
        FROM producto
        JOIN fabricante ON producto.id_fabricante = fabricante.id
        $where
        LIMIT $productos_por_pagina OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
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