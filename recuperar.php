<?php
session_start();
include("conexion/conexion.php");

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = trim($_POST['identificador']);

    // Buscar usuario
    $stmt = mysqli_prepare($conexion, 
        "SELECT id_usuario, correo FROM usuarios WHERE correo = ? OR cedula = ?");
    mysqli_stmt_bind_param($stmt, "ss", $identificador, $identificador);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        mysqli_stmt_bind_result($stmt, $id_usuario, $correo);
        mysqli_stmt_fetch($stmt);

        // Crear token y expiración
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardar token
        $stmt2 = mysqli_prepare($conexion, 
            "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt2, "ssi", $token, $expira, $id_usuario);
        mysqli_stmt_execute($stmt2);

        // Enlace de recuperación
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/AgroConect/restablecer.php?token=" . $token;

        // Mensaje (en producción se envía por correo)
        $mensaje = "Te enviamos un enlace a: <strong>$correo</strong><br><br>
                    <small>Para pruebas: <a href='$link'>Restablecer contraseña</a></small>";

    } else {
        $mensaje = "No existe un usuario con ese correo o cédula.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Recuperar contraseña</title>
<meta charset="utf-8">

<!-- Pega el CSS AQUÍ -->
<style>
    body {
        font-family: Poppins, sans-serif;
        background: #f2f7f3; /* Verde muy suave */
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .card {
        background: #ffffff;
        width: 360px;
        padding: 25px 30px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        border-top: 5px solid #4CAF50;
    }

    h2 {
        margin: 0 0 10px 0;
        font-size: 22px;
        color: #2e7d32;
    }

    p {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }

    input {
        width: 100%;
        padding: 11px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 15px;
        outline: none;
        transition: border 0.2s;
    }

    input:focus {
        border-color: #4CAF50;
    }

    button {
        width: 100%;
        padding: 11px;
        background: #4CAF50;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        margin-top: 15px;
        cursor: pointer;
        transition: background 0.2s;
    }

    button:hover {
        background: #3d9143;
    }

    .mensaje {
        background: #e7fbe7;
        border-left: 4px solid #4CAF50;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        color: #2e7d32;
        font-size: 14px;
    }

    .volver {
        display: inline-block;
        margin-top: 18px;
        background: #dcdcdc;
        padding: 10px 20px;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        transition: 0.2s;
    }

    .volver:hover {
        background: #c8c8c8;
    }
</style>

</head>

<body style="font-family:Poppins; max-width:400px; margin:auto; padding:20px;">
    
<div class="card">

<h2>Recuperar contraseña</h2>
<p>Ingresa tu correo o cédula para enviarte el enlace de recuperación.</p>

<?php if ($mensaje) { ?>
<div style="background:#d8ffd8; padding:10px; margin-bottom:15px; border-radius:5px;">
    <?= $mensaje ?>
</div>
<?php } ?>

<form action="" method="post">
    <label>Correo o Cédula:</label>
    <input type="text" name="identificador" required style="width:100%; padding:10px; margin-bottom:15px;">

    <button type="submit" style="width:100%; padding:10px; background:#4CAF50; color:white;">
        Enviar enlace
    </button>
</form>

<div style="margin-top:20px;">
    <a href="login.php" 
       style="padding:10px 20px; 
              display:inline-block; 
              background:#ccc; 
              border-radius:5px; 
              text-decoration:none; 
              color:black;">
        ← Volver al Inicio
    </a>
</div>
</body>
</html>