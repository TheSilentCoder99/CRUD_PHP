<?php

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['fabricante_buscado'])) {

    $nombre_fabricante = "%" . $_GET['fabricante_buscado'] . "%";

    $stmt = $conn->prepare('SELECT * FROM fabricante WHERE nombre LIKE :fabricante_buscado');
    $stmt->execute(['fabricante_buscado' => $nombre_fabricante]);
    $fabricantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // SI NO SE ENCUENTRA, MOSTRAR TODOS
    $stmt = $conn->prepare("SELECT * FROM fabricante");
    $stmt->execute();
    $fabricantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['mostrar_todos'])) {

    $stmt = $conn->prepare('SELECT * FROM fabricante');
    $stmt->execute();
    $fabricantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Fabricantes</h1>
    <hr>
    <a href="create_fabricante.php" target="_blank">Añadir fabricante</a>
    <br>
    <a href="All_productos.php">Ir a productos</a>
    <br>
    <a href="index.php">Página principal</a>
    <hr>
    <form method="GET" action="All_fabricantes.php">
        <input type="text" name="fabricante_buscado" placeholder="Buscar fabricante...">
        <button type="submit">Buscar</button>
        <br>
        <button type="submit" name="mostrar_todos">Mostrar todos</button>
    </form>

    <?php
    foreach ($fabricantes as $fabricante): ?>
        <div>
            <!-- Nombre de cada fabricante -->
            <?php echo $fabricante["nombre"] ?>

            <!-- Añades los botones de editar y eliminar -->
            <a href="update_fabricante.php?id=<?php echo $fabricante["id"] ?>" target="_blank">Editar</a>

            <a href="delete_fabricante.php?id=<?php echo $fabricante['id']; ?>"
                onclick="return confirm('¿Seguro que quieres eliminar este fabricante?')">
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
            <a href="All_fabricantes.php?pagina=<?php echo $i; ?>" class="<?php echo $activo; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

</body>

</html>