<?php
session_start();
include 'conexion/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_producto = intval($_POST['id_producto']);
    $id_usuario = $_SESSION['id_usuario'];
    $calificacion = intval($_POST['calificacion']);
    $comentario = $_POST['comentario'];

    $stmt = $conexion->prepare("INSERT INTO comentarios (id_producto, id_usuario, calificacion, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_producto, $id_usuario, $calificacion, $comentario);

    $stmt->execute();
    $stmt->close();

    header("Location: producto.php?id=$id_producto");
    exit;
}

$conexion->close();
?>

