<?php
session_start();
include("conexion/conexion.php");

if (!isset($_GET['id'])) {
    echo "Producto no encontrado";
    exit;
}

$id_producto = intval($_GET['id']);

// Obtener datos del producto
$sql = "SELECT p.*, u.nombre AS vendedor, u.telefono, u.correo
        FROM productos p
        JOIN usuarios u ON p.id_vendedor = u.id_usuario
        WHERE id_producto = $id_producto";

$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Producto no encontrado";
    exit;
}

$producto = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($producto['nombre_producto']) ?></title>
    <link rel="stylesheet" href="css/style.php?page=registro">

</head>
<body>

<main style="max-width:900px; margin: 0 auto; padding: 20px;"> <!-- ✅ AGREGADO -->

<a href="productos.php" class="btn">⬅️ Volver</a>

<h1><?= htmlspecialchars($producto['nombre_producto']) ?></h1>

<img src="img/productos/<?= htmlspecialchars($producto['imagen']) ?>" 
     style="width:250px; border-radius:10px; margin:20px 0;">

<p><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
<p><strong>Precio:</strong> $<?= number_format($producto['precio'],0,',','.') ?></p>
<p><strong>Cantidad:</strong> <?= $producto['cantidad'] ?> <?= htmlspecialchars($producto['unidad']) ?></p>

<hr>

<h2>💬 Comentarios</h2>

<?php if (isset($_SESSION['id_usuario'])): ?>
    <form action="comentar.php" method="POST" style="margin-bottom:30px;">
        <input type="hidden" name="id_producto" value="<?= $id_producto ?>">

        <label>Calificación:</label>
        <select name="calificacion" required>
            <option value="1">1 ⭐</option>
            <option value="2">2 ⭐⭐</option>
            <option value="3">3 ⭐⭐⭐</option>
            <option value="4">4 ⭐⭐⭐⭐</option>
            <option value="5">5 ⭐⭐⭐⭐⭐</option>
        </select>

        <br><br>

        <label>Comentario:</label>
        <textarea name="comentario" required style="width:100%; height:100px;"></textarea>

        <br><br>

        <button type="submit" class="btn">Publicar comentario</button>
    </form>
<?php else: ?>
    <p>Debes <a href="login.php">iniciar sesión</a> para comentar.</p>
<?php endif; ?>

<hr>

<?php
// Mostrar comentarios
$sqlComentarios = "SELECT c.*, u.nombre 
                   FROM comentarios c 
                   JOIN usuarios u ON c.id_usuario = u.id_usuario
                   WHERE id_producto = $id_producto
                   ORDER BY fecha DESC";

$comentarios = mysqli_query($conexion, $sqlComentarios);
?>

<?php if (mysqli_num_rows($comentarios) > 0): ?>
    <?php while ($c = mysqli_fetch_assoc($comentarios)): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px 0; border-radius:8px;">
            <strong><?= htmlspecialchars($c['nombre']) ?></strong> — 
            <?= $c['calificacion'] ?> ⭐

            <p><?= htmlspecialchars($c['comentario']) ?></p>
            <small><?= $c['fecha'] ?></small>

            <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $c['id_usuario']): ?>
                <form action="eliminar_comentario.php" method="POST" style="margin-top:10px;">
                    <input type="hidden" name="id_comentario" value="<?= $c['id_comentario'] ?>">
                    <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
                    <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">
                        Eliminar
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay comentarios aún.</p>
<?php endif; ?>

</main> <!-- ✅ FIN DEL MAIN -->

</body>

</html>

