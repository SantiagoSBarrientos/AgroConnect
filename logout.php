<?php
// logout.php
session_start();
session_unset();  // Limpia las variables de sesión
session_destroy(); // Destruye la sesión completamente
header("Location: index.php"); // Redirige al inicio
exit;
?>
