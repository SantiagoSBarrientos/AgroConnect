<?php
// registro.php (Tu código PHP actual se mantiene)
session_start();
include("conexion/conexion.php");

// *** INICIO: DEBUGGING - Verificar si la conexión es válida después del include ***
// Asumiendo que $conexion es la variable de conexión global.
if (!isset($conexion) || mysqli_connect_errno()) {
    // Si la conexión falló o la variable $conexion no está definida
    $error = "Error Fatal: No se pudo conectar a la base de datos. Por favor, verifica el archivo conexion/conexion.php. Código: " . (isset($conexion) ? mysqli_connect_errno() : 'N/A');
}
// *** FIN: DEBUGGING ***


if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    // ... Tu lógica de procesamiento de formulario permanece igual ...
    $nombre = trim($_POST['nombre']);
    $cedula = trim($_POST['cedula']); 
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];
    $tipo = $_POST['tipo']; 
    $telefono = trim($_POST['telefono']);
    $ubicacion = trim($_POST['ubicacion']);

    // Validaciones simples
    if (empty($nombre) || empty($correo) || empty($cedula) || empty($clave) || empty($tipo)) {
        $error = "Completa todos los campos obligatorios (incluye la contraseña).";
    } elseif (strlen($clave) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres.";
    } else {
        // Verificar si ya existe un usuario con el mismo correo o cédula
        $stmt = mysqli_prepare($conexion, "SELECT id_usuario FROM usuarios WHERE correo = ? OR cedula = ?");
        
        // *** DEBUGGING: Check for prepare error (SELECT) ***
        if (!$stmt) {
             $error = "Error de consulta (SELECT): No se pudo preparar. Detalles: " . mysqli_error($conexion);
        } else {
            // Procedemos con la lógica existente
            mysqli_stmt_bind_param($stmt, "ss", $correo, $cedula);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Ya existe un usuario con esa cédula o correo.";
            } else {
                mysqli_stmt_close($stmt);
                // Hashear la contraseña
                $hash = password_hash($clave, PASSWORD_DEFAULT);
                
                // Insertar nuevo usuario con cédula
                $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre, cedula, correo, clave, tipo, telefono, ubicacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                // *** DEBUGGING: Check for prepare error (INSERT) ***
                if (!$stmt) {
                    $error = "Error de consulta (INSERT): No se pudo preparar. Detalles: " . mysqli_error($conexion);
                } else {
                    mysqli_stmt_bind_param($stmt, "sssssss", $nombre, $cedula, $correo, $hash, $tipo, $telefono, $ubicacion);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "✅ Registro exitoso.<br><a href='login.php'>Iniciar sesión</a>";
                    } else {
                        // *** DEBUGGING: Check for execute error (INSERT) ***
                        $error = "Error al registrar usuario. Detalles: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registro | AgroConnect Urabá</title>
    <link rel="stylesheet" href="css/style.php?page=registro">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de la Paleta (Acentuando el verde y la limpieza) */
        :root {
            --color-verde-principal: #4CAF50; /* Verde Agricultura */
            --color-verde-oscuro: #388E3C;
            --color-fondo-claro: #F5F5F7; 
            --color-texto-oscuro: #1D1D1F; 
            --color-sombra-suave: rgba(0, 0, 0, 0.08); 
        }

        /* --- INICIO: ESTILOS PARA EL FONDO DE PÁGINA INTERACTIVO (COPIADO DE LOGIN) --- */
        .page-background {
            background-image: url('img/fondo.jpeg'); /* Usamos tu imagen */
            background-size: cover; /* Cubre toda el área */
            background-position: center center; /* Centra la imagen */
            background-attachment: fixed; /* Esto hace que sea interactivo/paralaje */
            background-repeat: no-repeat;
            position: relative; /* Necesario para que el overlay funcione */
            z-index: 0; /* Aseguramos que el fondo esté detrás de todo */
        }

        .page-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.45); /* Capa semi-transparente para oscurecer el fondo */
            z-index: -1; /* Pone el overlay detrás del contenido y de la tarjeta */
        }
        /* --- FIN: ESTILOS PARA EL FONDO DE PÁGINA INTERACTIVO --- */
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; 
            margin: 0;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: transparent; /* El fondo lo maneja la clase .page-background */
        }

        /* Contenedor: HACEMOS EL FORMULARIO MÁS PEQUEÑO */
        .registro-container {
            background-color: #FFFFFF;
            padding: 30px; /* Reducimos el padding */
            border-radius: 20px; 
            box-shadow: 0 10px 30px var(--color-sombra-suave); 
            border: 1px solid #E0E0E0; 
            width: 100%;
            max-width: 350px; /* ANCHO MÁS COMPACTO */
            text-align: center;
            position: relative; /* Necesario para el z-index */
            z-index: 1; /* Asegura que la tarjeta esté encima del overlay del fondo */
        }

        h2 {
            /* Título con emoji de siembra */
            font-size: 1.8em; /* Un poco más pequeño */
            font-weight: 600; 
            color: var(--color-texto-oscuro); 
            margin-bottom: 25px; /* Reducimos el margen */
            text-align: center; /* Centramos el título */
        }

        /* Estilo para los enlaces de navegación */
        .nav-link {
            color: var(--color-verde-principal); 
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 15px; /* Reducimos el margen */
            display: block; 
            transition: color 0.3s;
            text-align: left;
            font-size: 0.9em;
        }
        
        .nav-link:hover {
            color: var(--color-verde-oscuro); 
            text-decoration: none;
        }

        /* Estilo para las etiquetas (Labels) */
        label {
            display: block; 
            color: var(--color-texto-oscuro);
            font-weight: 500;
            margin-top: 10px; /* Reducimos el margen */
            margin-bottom: 5px;
            text-align: left; 
            font-size: 0.85em; /* Un poco más pequeña */
        }

        /* Estilo para los campos de texto y selección (Inputs y Select) */
        input[type="text"], 
        input[type="email"], 
        input[type="password"],
        select {
            width: 100%; 
            padding: 10px; /* Reducimos el padding del input */
            margin-bottom: 8px; /* Reducimos el margen inferior */
            border: 1px solid #D1D1D6; 
            border-radius: 8px; /* Un poco menos redondeado para ser compacto */
            box-sizing: border-box; 
            font-size: 0.95em;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #FFFFFF; 
        }
        
        input:focus, select:focus {
            border-color: var(--color-verde-principal); 
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2); 
            outline: none; 
        }

        /* Estilo del botón de Enviar ("Registrar") */
        button[type="submit"] {
            background-color: var(--color-verde-principal); 
            color: white;
            padding: 12px 20px; /* Más compacto */
            border: none;
            border-radius: 8px; 
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.2s, opacity 0.2s;
            width: 100%; 
            margin-top: 20px; /* Reducimos el margen superior */
        }

        button[type="submit"]:hover {
            background-color: var(--color-verde-oscuro); 
        }
        
        /* Estilo para los mensajes de error/éxito */
        .message-box {
            padding: 10px; /* Más compacto */
            border-radius: 8px;
            font-weight: 500;
            margin: 15px 0;
            text-align: left;
            font-size: 0.9em;
        }
        
        .error-message {
            color: #D32F2F; 
            background-color: #FFEBEE;
            border: 1px solid #EF9A9A;
        }

        .success-message {
            color: var(--color-verde-oscuro); 
            background-color: #E8F5E9;
            border: 1px solid var(--color-verde-principal);
        }
        
        /* Enlace inferior de sesión centrado */
        .login-link {
            text-align: center; 
            margin-top: 20px; /* Reducimos el margen */
            font-size: 0.9em;
        }
    </style>
</head>

<body class="page-background">
<div class="registro-container">
    <h2>Únete a AgroConnect Urabá 🌱</h2>
    
    <a href="index.php" class="nav-link">⬅️ Volver al inicio</a>

<?php 
    if(!empty($error)) {
        echo "<p class='message-box error-message'>$error</p>";
    }
    if(!empty($success)) {
        echo "<p class='message-box success-message'>$success</p>";
    }
?>

    <form method="post" action="registro.php">
      <label for="nombre">Nombre</label>
      <input name="nombre" id="nombre" type="text" value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>" required>

      <label for="cedula">Cédula</label>
      <input name="cedula" id="cedula" type="text" value="<?= isset($cedula) ? htmlspecialchars($cedula) : '' ?>" required>

      <label for="correo">Correo Electrónico</label>
      <input name="correo" id="correo" type="email" value="<?= isset($correo) ? htmlspecialchars($correo) : '' ?>" required>

      <label for="clave">Contraseña (Mín. 4 caracteres)</label>
      <input name="clave" id="clave" type="password" required>

      <label for="tipo">Tipo de Usuario 🧑‍🌾</label>
      <select name="tipo" id="tipo" required>
        <option value="campesino" <?= (isset($tipo) && $tipo == 'campesino') ? 'selected' : '' ?>>Campesino</option>
        <option value="comprador" <?= (isset($tipo) && $tipo == 'comprador') ? 'selected' : '' ?>>Comprador</option>
      </select>

      <label for="telefono">Teléfono (Opcional)</label>
      <input name="telefono" id="telefono" type="text" value="<?= isset($telefono) ? htmlspecialchars($telefono) : '' ?>">

      <label for="ubicacion">Ubicación / Dirección (Opcional)</label>
      <input name="ubicacion" id="ubicacion" type="text" value="<?= isset($ubicacion) ? htmlspecialchars($ubicacion) : '' ?>">

      <button type="submit">Registrarme</button>
    </form>

    <p class="login-link"><a href="login.php" class="nav-link">¿Ya tienes cuenta? Inicia sesión</a></p>
</div>
</body>
</html>
