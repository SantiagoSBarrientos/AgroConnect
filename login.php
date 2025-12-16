<?php
// login.php (Tu código PHP actual se mantiene)
session_start();
include("conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // campo puede ser cédula o correo
    $identificador = trim($_POST['identificador']);
    $clave = $_POST['clave'];

    // buscamos el usuario por correo o cédula
    $stmt = mysqli_prepare($conexion, "SELECT id_usuario, nombre, clave, tipo FROM usuarios WHERE correo = ? OR cedula = ?");
    mysqli_stmt_bind_param($stmt, "ss", $identificador, $identificador);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_usuario, $nombre, $hash, $tipo);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($clave, $hash)) {
            // Login OK
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['tipo'] = $tipo;
            header("Location: index.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado (verifica tu correo o cédula).";
    }
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | AgroConnect Urabá</title>
    <link rel="stylesheet" href="css/style.php?page=login">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de la Paleta */
        :root {
            --color-verde-principal: #4CAF50; /* Verde Agricultura */
            --color-verde-oscuro: #388E3C;
            --color-fondo-claro: #F5F5F7; /* Gris muy claro */
            --color-texto-oscuro: #1D1D1F; /* Negro profundo */
            --color-sombra-suave: rgba(0, 0, 0, 0.08); 
        }

        /* --- INICIO: ESTILOS PARA EL FONDO DE PÁGINA INTERACTIVO --- */
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
            /* Simula la fuente San Francisco */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; 
            margin: 0;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh; 
            background-color: transparent; /* Importante: el fondo lo manejará .page-background */
        }

        /* Contenedor principal del formulario: La "tarjeta flotante" COMPACTA */
        .login-container {
            background-color: #FFFFFF; /* Mantenemos el blanco para la tarjeta */
            padding: 30px; /* Reducido para compactar */
            /* Bordes muy redondeados (Estilo Apple) */
            border-radius: 20px; 
            /* Sombra suave y amplia (Efecto flotante) */
            box-shadow: 0 10px 30px var(--color-sombra-suave); 
            border: 1px solid #E0E0E0; 
            width: 100%;
            max-width: 350px; /* Ancho más compacto */
            text-align: center;
            position: relative; /* Asegura que la tarjeta esté encima del overlay del fondo */
            z-index: 1; /* La tarjeta debe estar por encima del overlay (-1) */
        }

        h2 {
            /* Título temático con emoji de campo/planta */
            font-size: 1.8em; 
            font-weight: 600; 
            color: var(--color-texto-oscuro); 
            margin-bottom: 30px;
            text-align: center; 
        }

        /* Estilo para el enlace "Volver al inicio" y "Registro" */
        .nav-link {
            color: var(--color-verde-principal); 
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 15px; 
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
            margin-top: 10px; /* Más compacto */
            margin-bottom: 5px;
            text-align: left; 
            font-size: 0.85em; 
        }

        /* Estilo para los campos de texto y contraseña (Inputs) */
        input[name="identificador"], 
        input[name="clave"] {
            width: 100%; 
            padding: 10px; /* Más compacto */
            margin-bottom: 15px;
            border: 1px solid #D1D1D6; 
            border-radius: 8px; /* Bordes redondeados */
            box-sizing: border-box; 
            font-size: 0.95em;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #FFFFFF; 
        }

        input:focus {
            border-color: var(--color-verde-principal); 
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2); /* Sombra de enfoque sutil */
            outline: none; 
        }

        /* Estilo del botón de Enviar ("Entrar") */
        button[type="submit"] {
            background-color: var(--color-verde-principal); 
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px; 
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.2s, opacity 0.2s;
            width: 100%; 
            margin-top: 15px;
        }

        button[type="submit"]:hover {
            background-color: var(--color-verde-oscuro); 
        }
        
        /* Estilo para el mensaje de error (Minimalista) */
        .error-message {
            color: #D32F2F; /* Mantengo el rojo original para el texto del error */
            background-color: #FFEBEE; /* Fondo del mensaje de error */
            border: 1px solid #EF9A9A;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            margin-bottom: 15px;
            text-align: left; /* Alineado a la izquierda */
            font-size: 0.9em;
        }
        
        /* Enlace inferior de registro centrado */
        .register-link {
            text-align: center; 
            margin-top: 20px; 
            font-size: 0.9em;
        }
        
    </style>
</head>

<body class="page-background">
<div class="login-container">
    <h2>¡Bienvenido a AgroConnect Urabá! 🌱</h2>
    
    <a href="index.php" class="nav-link">⬅️ Volver al inicio</a>

<?php 
    if(!empty($error)) {
        echo "<p class='error-message'>$error</p>";
    }
?>

    <form method="post" action="login.php">
      <label for="identificador">Correo o Cédula</label>
        <input name="identificador" id="identificador" type="text" required value="<?= isset($identificador) ? htmlspecialchars($identificador) : '' ?>">
        
        <label for="clave">Contraseña</label>
        <input name="clave" id="clave" type="password" required>
        
        <button type="submit">Entrar</button>
    </form>
    
    <p class="register-link">
    <a href="registro.php" class="nav-link">¿No tienes cuenta? Regístrate aquí</a>
  
    <a href="recuperar.php" class="nav-link">¿Olvidaste tu contraseña?</a>
</p>

</div>
</body>
</html>