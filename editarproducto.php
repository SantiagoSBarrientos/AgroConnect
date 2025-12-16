<?php
session_start();
include("conexion/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Validar el producto recibido
if (!isset($_GET['id'])) {
    die("Error: No se recibió el ID del producto.");
}

$id_producto = intval($_GET['id']);

// Cargar datos del producto para editar
$stmt = mysqli_prepare($conexion, "SELECT * FROM productos WHERE id_producto = ? AND id_vendedor = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_producto, $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);

if (!$producto) {
    die("Producto no encontrado o no tienes permiso.");
}

// Si envían actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);

    $nuevaImagen = $producto['imagen']; // por defecto se queda igual

    // Si subieron imagen nueva
    if (!empty($_FILES['imagen']['name'])) {
        $imgName = time() . "_" . basename($_FILES['imagen']['name']);
        $destino = "img/productos/" . $imgName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $nuevaImagen = $imgName;
        }
    }

    $update = mysqli_prepare($conexion, 
        "UPDATE productos SET nombre_producto=?, precio=?, cantidad=?, imagen=? WHERE id_producto=? AND id_vendedor=?");
    mysqli_stmt_bind_param($update, "sdisii", $nombre, $precio, $cantidad, $nuevaImagen, $id_producto, $id_usuario);
    mysqli_stmt_execute($update);

    header("Location: perfil.php?msg=editado");
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Editar Producto | AgroConnect Urabá</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: Poppins, sans-serif;
    background-image: url('img/fondo.jpeg');
    background-size: cover;
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.container {
    background: rgba(255,255,255,0.9);
    padding: 30px;
    width: 380px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #2E7D32;
    font-weight: 600;
}
input[type="text"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius : 8px;
}
button {
    width: 100%;
    padding: 10px;
    background: #4CAF50;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
}
button:hover {
    background: #43A047;
}
a {
    text-decoration: none;
    display: block;
    margin-top: 12px;
    text-align: center;
    color: #388E3C;
    font-weight: 600;
}
img {
    width: 100%;
    border-radius: 10px;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="container">
<h2>✏️ Editar Producto</h2>

<img src="img/productos/<?=$producto['imagen']?>" alt="imagen">

<form method="post" enctype="multipart/form-data">
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?=$producto['nombre_producto']?>" required>

    <label>Precio</label>
    <input type="number" name="precio" step="0.01" value="<?=$producto['precio']?>" required>

    <label>Cantidad</label>
    <input type="number" name="cantidad" value="<?=$producto['cantidad']?>" required>

    <label>Cambiar Imagen (opcional)</label>
    <input type="file" name="imagen">

    <button type="submit">Actualizar</button>
</form>

<a href="perfil.php">⬅️ Volver a mi perfil</a>
</div>

</body>
</html>
