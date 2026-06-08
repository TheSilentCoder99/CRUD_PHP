<?php

session_start();

require 'conexion.php';


// 1. LEER PARÁMETROS DE LA URL
$orden = isset($_GET['orden']) && $_GET['orden'] === 'asc' ? 'asc' : 'desc';

$orden_siguiente = $orden === 'asc' ? 'desc' : 'asc';

$texto_boton = $orden === 'asc' ? 'precio ↑' : 'precio ↓';


// CALCULANDO OFFSET Y DIFINIENDO NÚMERO DE PRODUCTOS POR PÁGINA
$productos_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $productos_por_pagina;


// 2. CONSTRUIR EL WHERE DINÁMICAMENTE
// WHERE 1=1 siempre es verdadero, permite añadir AND después sin problema
$where = "WHERE 1=1";
$params = [];

if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
    $where .= " AND (producto.nombre LIKE :buscar OR producto.descripcion LIKE :buscar)";
    $params[':buscar'] = '%' . $_GET['buscar'] . '%';
}

// 3. CONTAR EL TOTAL DE PRODUCTOS QUE COINCIDEN CON EL FILTRO
// Necesitas este número para saber cuántas páginas mostrar
// Usa el mismo $where para que el conteo respete la búsqueda activa. Este conteo usa como condición el where de la búsqueda, o sea, solo contará los que coincidan con la búsqueda.
$sql_count = "SELECT COUNT(*) FROM producto JOIN fabricante ON producto.id_fabricante = fabricante.id $where";

$stmt = $conn->prepare($sql_count);
$stmt->execute($params);

$total_productos = $stmt->fetchColumn(); // fetchColumn devuelve un solo valor, no un array
$total_paginas = ceil($total_productos / $productos_por_pagina);

// 4. CONSULTA PRINCIPAL
// Mismo $where que el COUNT, más ORDER BY y LIMIT/OFFSET al final
$sql = "SELECT producto.*, fabricante.nombre AS nombre_fabricante
        FROM producto
        JOIN fabricante ON producto.id_fabricante = fabricante.id
        $where
        ORDER BY producto.precio $orden
        LIMIT $productos_por_pagina OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="estilo_index.css">

</head>

<body>
    <!-- MUESTRO UN TEXTO DE CERRAR SESIÓN O INICIAR SESIÓN SEGÚN HAYA YA UNA SESIÓN INICIADA O NO  -->
    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <a href="login.php" class="sessionIn">Iniciar sesión</a>
    <?php else: ?>
        <a href="logout.php" class="sessionOut">Cerrar sesión</a>
        <a href="panel_usuario.php">Mi usuario</a>
    <?php endif; ?>

    <!-- SI LA SESIÓN ESTÁ INICIADA, SE DA LA OPCIÓN DE GESTIONAR PRODUCTOS Y FABRICANTES -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <br>
        <a href="All_productos.php">Gestionar productos</a>
        <a href="All_fabricantes.php">Gestionar fabricantes</a>
    <?php endif; ?>


    <form method="GET" action="index.php">
        <input type="text" name="buscar" placeholder="Buscar producto...">
        <br>
        <button type="submit">Buscar</button>
        <br>
    </form>

    <a href="index.php?orden=<?php echo $orden_siguiente; ?>&buscar=<?php echo $_GET['buscar'] ?? ''; ?>">
        <?php echo $texto_boton; ?>
    </a>

    <br>
    <table border="1">
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['precio']; ?> €</td>
                <td><?php echo $producto['descripcion']; ?></td>
                <td><?php echo $producto['nombre_fabricante']; ?></td>
                <td><a href="ficha_producto.php?id=<?php echo $producto['id']; ?>" target="_blank"> Ver ficha </a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <hr>
    <!-- NAVEGACIÓN DE PÁGINAS -->
    <!-- Si estamos en la misma página de la URL, a la clase que rastrea el numeral se le añade la clase "ACTIVO" que es la clase que añade el color rojo a ese numeral. -->
    <?php for ($i = 1; $i <= $total_paginas; $i++):
        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $activo = ($i == $pagina_actual) ? 'activo' : '';
        ?>
        <a href="index.php?pagina=<?php echo $i; ?>&orden=<?php echo $orden; ?>&buscar=<?php echo $_GET['buscar'] ?? ''; ?>"
            class="navegacion_paginasIndex <?php echo $activo; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

</body>

</html>