<?php
session_start();
include("conexion/conexion.php"); // mismo include que usas en perfil.php

// Debug temporal: activa si quieres ver errores (quitar en producción)
// ini_set('display_errors', 1); error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Validar que llegue id por GET
if (!isset($_GET['id'])) {
    // Para depuración: muestra mensaje simple
    die("Error: ID de producto no recibido.");
}

$id_producto = intval($_GET['id']);
$id_usuario = intval($_SESSION['id_usuario']);

// Verificar que el producto exista y pertenezca al usuario
$stmt = mysqli_prepare($conexion, "SELECT id_producto FROM productos WHERE id_producto = ? AND id_vendedor = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_producto, $id_usuario);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($res);

if (!$producto) {
    die("Producto no encontrado o no tienes permisos para eliminarlo.");
}

// Si todo OK, eliminar (usar prepared statement)
$stmtDel = mysqli_prepare($conexion, "DELETE FROM productos WHERE id_producto = ? AND id_vendedor = ?");
mysqli_stmt_bind_param($stmtDel, "ii", $id_producto, $id_usuario);
$ok = mysqli_stmt_execute($stmtDel);

if ($ok) {
    // redirigir a perfil con éxito
    header("Location: perfil.php?msg=eliminado");
    exit;
} else {
    die("Error al eliminar el producto. " . mysqli_error($conexion));
}
