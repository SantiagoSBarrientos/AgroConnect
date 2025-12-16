<?php
session_start();
include 'conexion/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_comentario = intval($_POST['id_comentario']);
$id_producto = intval($_POST['id_producto']);

// Verificar que el comentario pertenece al usuario
$stmt = $conexion->prepare("SELECT id_usuario FROM comentarios WHERE id_comentario = ?");
$stmt->bind_param("i", $id_comentario);
$stmt->execute();
$stmt->bind_result($idDueno);
$stmt->fetch();
$stmt->close();

if ($idDueno != $_SESSION['id_usuario']) {
    header("Location: producto.php?id=$id_producto");
    exit;
}

// Eliminar comentario
$stmt = $conexion->prepare("DELETE FROM comentarios WHERE id_comentario = ?");
$stmt->bind_param("i", $id_comentario);
$stmt->execute();
$stmt->close();

header("Location: producto.php?id=$id_producto");
exit;
?>
