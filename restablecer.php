<?php
session_start();
include("conexion/conexion.php");

$token = $_GET['token'] ?? null;
$mensaje = "";

// 1️⃣ Validar que el token venga en la URL
if (!$token) {
    die("<h3>Token inválido.</h3>");
}

// 2️⃣ Verificar el token en la base de datos
$stmt = mysqli_prepare($conexion, 
    "SELECT id_usuario, token_expira FROM usuarios WHERE token_recuperacion = ?");
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    die("<h3>El enlace no es válido o ya fue usado.</h3>");
}

mysqli_stmt_bind_result($stmt, $id_usuario, $token_expira);
mysqli_stmt_fetch($stmt);

// 3️⃣ Validar expiración
if (strtotime($token_expira) < time()) {
    die("<h3>El enlace ha expirado. Solicita uno nuevo.</h3>");
}

// 4️⃣ Si el usuario envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $clave1 = $_POST['clave1'];
    $clave2 = $_POST['clave2'];

    // Validar que coincidan
    if ($clave1 !== $clave2) {
        $mensaje = "Las contraseñas no coinciden.";
    } else if (strlen($clave1) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } else {

        // Encriptar contraseña
        $nueva_clave = password_hash($clave1, PASSWORD_DEFAULT);

        // Actualizar contraseña y borrar token
        $stmt2 = mysqli_prepare($conexion,
            "UPDATE usuarios SET clave = ?, token_recuperacion = NULL, token_expira = NULL 
             WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt2, "si", $nueva_clave, $id_usuario);
        mysqli_stmt_execute($stmt2);

        $mensaje = "ok"; // Marcador para mostrar mensaje de éxito
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Restablecer contraseña</title>
<meta charset="utf-8">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: "Poppins", sans-serif;
    background: #eaf5ea url('img/hojas-bg.png') repeat;
    padding: 20px;
    max-width: 400px;
    margin: auto;
}

h2 {
    text-align: center;
    color: #1b5e20;
    font-weight: 600;
}

.form-container {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    border: 2px solid #a5d6a7;
    margin-top: 15px;
}

label {
    font-weight: 600;
    color: #1b5e20;
    display: block;
    margin-bottom: 5px;
}

input[type="password"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #81c784;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f1fff1;
    font-size: 14px;
}

button {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
    color: white;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    margin-top: 5px;
}

button:hover {
    opacity: 0.9;
}

.alert-success {
    background: #d0ffe0;
    border-left: 5px solid #1b5e20;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
}

.alert-error {
    background: #ffdede;
    border-left: 5px solid #c62828;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
}

.back-btn {
    margin-top: 20px;
    display: inline-block;
    padding: 10px 20px;
    background: #c8e6c9;
    border-radius: 6px;
    color: #1b5e20;
    text-decoration: none;
}

.back-btn:hover {
    background: #b2dfb2;
}

.success-box {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
    border: 2px solid #a5d6a7;
    max-width: 420px;
    margin: 40px auto;
    text-align: center;
    animation: fadeIn 0.4s ease;
}

.success-box h3 {
    color: #1b5e20;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 12px;
}

.success-box p {
    font-size: 15px;
    color: #2e2e2e;
}

.success-box a {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #2e7d32;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
}

.success-box a:hover {
    opacity: 0.9;
}

/* Animación suave */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}


</style>
</head>

<body style="font-family: Poppins; max-width:400px; margin:auto; padding:20px;">

<h2>Restablecer contraseña</h2>

<?php if ($mensaje === "ok") { ?>

<div class="success-box">
    <h3>✔ Contraseña restablecida</h3>
    <p>Tu contraseña ha sido actualizada correctamente.</p>
    <a href="login.php">Iniciar sesión</a>
</div>


<?php exit; } ?>

<?php if (!empty($mensaje)) { ?>
    <div style="background:#ffdede; padding:10px; border-radius:8px; margin-bottom:15px;">
        <?= $mensaje ?>
    </div>
<?php } ?>

<form method="post">

    <label>Nueva contraseña:</label>
    <input type="password" name="clave1" required 
           style="width:100%; padding:10px; margin-bottom:15px;">

    <label>Confirmar contraseña:</label>
    <input type="password" name="clave2" required 
           style="width:100%; padding:10px; margin-bottom:15px;">

    <button type="submit"
            style="background:#4CAF50; color:white; width:100%; padding:10px; border:none; border-radius:5px;">
        Guardar nueva contraseña
    </button>

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

</form>


</body>
</html>