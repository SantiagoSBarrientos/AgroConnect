<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroConnect Uraba</title>
    <link rel="stylesheet" type="text/css" href="css/style.php?page=index">

    <style>

        .logo-icon {
    display: inline-block;
    animation: pulseIcon 3s infinite ease-in-out;
}

@keyframes pulseIcon {
    0% { transform: scale(1); }
    50% { transform: scale(1.07); }
    100% { transform: scale(1); }
}

        /* --- Estilos generales y Footer --- */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* --- Estilos de la Sección Principal (Donde está el texto) --- */
        main, section:not(.features) {
            flex: 1;
            position: relative;
            z-index: 1;
            text-align: center;
            color: white; 
            
            /* Centrado de Contenido (Se mantiene para que el texto esté en el centro de la pantalla) */
            display: flex; 
            flex-direction: column;
            justify-content: center; /* Centra el contenido verticalmente */
            align-items: center; /* Centra el contenido horizontalmente */
            padding: 50px 20px; 
        }

        /* --- Estilo del video de fondo --- */
        #video-fondo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.9); 
            transition: filter 0.5s ease;
        }
        
        /* === CÓDIGO CLAVE: Solo al PÁRRAFO de descripción (Letra más blanca) === */
        #inicio p {
            /* Simplemente definimos el color como blanco puro */
            color: #FFFFFF; 
            
           ímite de ancho /* Eliminamos cualquier l que pudiera causar scroll horizontal (si existiera) */
            max-width: none; 
            
            
            margin: 10px auto;
        }
        
        
        #inicio h2 {
             color: white; 
             margin: 10px auto;
        }
        

        footer {
            text-align: center;
            padding: 15px 0;
            font-size: 0.95rem;
            color: #555;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin-top: auto;
            transition: transform 0.6s ease, opacity 0.6s ease;
        }

        footer.show {
            transform: translateY(0);
            opacity: 1;
        }

        .features {
    background: #ffffffee;
    padding: 50px 20px;
    display: flex;
    justify-content: center;
    gap: 50px;
    color: #333;
    text-align: center;
}

.feature-item img {
    width: 60px;
    margin-bottom: 12px;
}

.feature-item h4 {
    font-size: 18px;
    color: #2c7a3f;
}

.feature-item p {
    font-size: 14px;
    color: #666;
}



    </style>
</head>

<body>
    
<video autoplay muted loop id="video-fondo">
    <source src="videos/videourabafondopresentacion.mp4" type="video/mp4">
    Tu navegador no soporta video HTML5.
</video>

<header>
    <h1><span class="logo-icon">🌱</span> AgroConnect Urabá</h1>
   
    <nav>
        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>
        <?php if (!isset($_SESSION['id_usuario'])): ?>
            <a href="registro.php">Registrarse</a>
            <a href="login.php">Iniciar sesión</a>
        <?php else: ?>
            <a href="perfil.php">Mi Perfil (<?=htmlspecialchars($_SESSION['nombre'])?>)</a>
            <a href="logout.php">Cerrar sesión</a>
        <?php endif; ?>
    </nav>

</header>

<main class="hero">

    <section id="inicio">
        <h2>Conectando el campo con la ciudad 🌾</h2>
        <p>
            AgroConnect Urabá es una plataforma digital que permite a campesinos y compradores 
            conectarse directamente, eliminando intermediarios y promoviendo un comercio justo 
            y local.
        </p>

        <div class="botones">
            <a href="productos.php" class="btn">Ver productos</a>
            <a href="registro.php" class="btn">Unirme ahora</a>
        </div>
    </section>
</main>

<section class="features">
    <div class="feature-item">
        <img src="img/icon-frutas.png" alt="">
        <h4>Productos frescos</h4>
        <p>Directo del campo a tu mesa.</p>
    </div>

    <div class="feature-item">
        <img src="img/icon-handshake.png" alt="">
        <h4>Conexión directa</h4>
        <p>Sin intermediarios.</p>
    </div>

    <div class="feature-item">
        <img src="img/icon-justo.png" alt="">
        <h4>Comercio justo</h4>
        <p>Apoyando a productores locales.</p>
    </div>
</section>

<footer id="footer">
    <p>© 2025 AgroConnect Urabá - Todos los derechos reservados<p>
</footer>

<script>
    // Animación suave de aparición del footer
    window.addEventListener("load", () => {
        document.getElementById("footer").classList.add("show");
    });
</script>

</body>
</html>