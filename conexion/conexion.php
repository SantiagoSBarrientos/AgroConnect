<?php
$host = "localhost";
$usuario = "root";
$clave = "";
$base_datos = "agro_conect";

// Crear la conexión
$conexion = mysqli_connect($host, $usuario, $clave, $base_datos);

// Verificar si la conexión fue exitosa
if (!$conexion) {
    die("❌ Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Si quieres comprobarlo, puedes descomentar esta línea:
// echo "✅ Conexión exitosa a la base de datos.";
?>
