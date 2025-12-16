<?php

date_default_timezone_set('America/Bogota');

    include("conexion/conexion.php");

    // --- Función para mostrar "hace X tiempo" ---
function tiempo_relativo($fecha) {
    $ts = strtotime($fecha);
    $diff = time() - $ts;

    if ($diff < 60) return "hace un momento";
    if ($diff < 3600) return "hace " . floor($diff / 60) . " minutos";
    if ($diff < 86400) return "hace " . floor($diff / 3600) . " horas";
    if ($diff < 604800) return "hace " . floor($diff / 86400) . " días";
    if ($diff < 2592000) return "hace " . floor($diff / 604800) . " semanas";
    
    return "hace " . floor($diff / 2592000) . " meses";
}


    $primary = isset($primary) ? $primary : '#4CAF50'; 
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : '';

if (!empty($buscar)) {

    // ✅ BUSQUEDA ACTIVADA + PROMEDIO + TOTAL COMENTARIOS
    $sql = "SELECT p.id_producto, p.nombre_producto, p.descripcion, p.precio, p.cantidad, p.unidad,
                    p.whatsapp, p.imagen, p.fecha_publicacion,
                    u.nombre AS vendedor, u.telefono, u.correo,
                    (SELECT AVG(calificacion) FROM comentarios c WHERE c.id_producto = p.id_producto) AS promedio,
                (SELECT COUNT(*) FROM comentarios c WHERE c.id_producto = p.id_producto) AS total_comentarios

            FROM productos p
            JOIN usuarios u ON p.id_vendedor = u.id_usuario
            WHERE p.nombre_producto LIKE '%$buscar%'
               OR p.descripcion LIKE '%$buscar%'
            ORDER BY p.fecha_publicacion DESC";

} else {

    // ✅ CONSULTA NORMAL + PROMEDIO + TOTAL COMENTARIOS
    $sql = "SELECT p.id_producto, p.nombre_producto, p.descripcion, p.precio, p.cantidad, p.unidad,
                    p.whatsapp, p.imagen, p.fecha_publicacion,
                    u.nombre AS vendedor, u.telefono, u.correo,
                    (SELECT AVG(calificacion) FROM comentarios WHERE id_producto = p.id_producto) AS promedio,
                    (SELECT COUNT(*) FROM comentarios WHERE id_producto = p.id_producto) AS total_comentarios
            FROM productos p
            JOIN usuarios u ON p.id_vendedor = u.id_usuario
            ORDER BY p.fecha_publicacion DESC";
}

$result = mysqli_query($conexion, $sql);


$result = mysqli_query($conexion, $sql);

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos disponibles | AgroConnect Urabá</title>
    <link rel="stylesheet" href="css/style.php?page=registro">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-principal: <?=$primary?>; 
            --color-texto-oscuro: #1e293b; 
        }
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5; 
            background-image: url('img/fondo.jpeg'); 
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
        
        h2 {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            font-size: 2.2rem;
            color: #FFFFFF; 
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.6);
            font-weight: 700;
        }

        .productos-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 30px;
            padding: 40px;
            max-width: 1300px;
            margin: auto;
        }

        .producto-card {
            background: #fff; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 20px; 
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .producto-card:hover {
            transform: translateY(-5px); 
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }

        .producto-card img {
            width: 100%;
            height: 190px; 
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 15px; 
        }

        .producto-info {
            flex-grow: 1; 
        }
        
        h3 {
            font-size: 1.5rem; 
            color: var(--color-principal);
            margin-bottom: 8px;
            font-weight: 700;
            line-height: 1.2;
        }

        .producto-info p {
            margin: 5px 0; 
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }
        
        .producto-info strong {
            color: var(--color-texto-oscuro);
            font-weight: 600;
        }

        hr {
            margin: 10px 0; 
            border: 0;
            border-top: 1px solid #e0e0e0;
        }
        
        .contact-group {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .volver-btn {
            display: inline-block;
            background: var(--color-principal);
            color: white;
            padding: 14px 28px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            margin: 20px auto 40px auto;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .volver-btn:hover {
            background: var(--color-texto-oscuro);
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <h2>🌱 Productos Frescos Disponibles en Urabá</h2>

<div style="
    position:relative;
    max-width:1300px;
    margin:0 auto;
    height:0; /* ✅ NO empuja nada hacia abajo */
">
    <form method="GET" action="productos.php" 
        style="
            position:absolute;
            top:60px;     /* ✅ Ajusta esto para bajarlo */
            right:40px;   /* ✅ Alineado a la derecha */
            display:flex;
            gap:8px;
        ">
        
        <div style="position:relative;">
            <input 
                id="buscarInput"
                type="text" 
                name="buscar" 
                placeholder="Buscar producto..." 
                value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>"
                style="
                    padding:8px 35px 8px 12px; 
                    border-radius:10px; 
                    border:1px solid #ccc; 
                    width:220px;
                "
            >
            <span 
                onclick="document.getElementById('buscarInput').value=''; this.parentNode.parentNode.submit();"
                style="
                    position:absolute;
                    right:10px;
                    top:50%;
                    transform:translateY(-50%);
                    cursor:pointer;
                    font-weight:bold;
                    color:#999;
                    font-size:16px;
                "
            >×</span>
        </div>

        <button 
            type="submit"
            style="padding:8px 16px; border:none; background:#4CAF50; color:white; border-radius:10px; font-weight:600;">
            Buscar
        </button>
    </form>
</div>


    <div style="text-align:center;">
        <a href="index.php" class="volver-btn">⬅️ Volver al inicio</a>
    </div>

    <div class="productos-container">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)): 
        ?>
            <div class="producto-card">
                <?php if(!empty($row['imagen'])): ?>
                    <img src="img/productos/<?=htmlspecialchars($row['imagen'])?>" 
                        alt="<?=htmlspecialchars($row['nombre_producto'])?>"
                        onerror="this.onerror=null;this.src='https://placehold.co/300x190/82C092/ffffff?text=Imagen+no+disponible';">
                <?php else: ?>
                    <img src="https://placehold.co/300x190/82C092/ffffff?text=Producto+de+Urabá" 
                        alt="Producto sin imagen">
                <?php endif; ?>

                <div class="producto-info">

                <h3><?=htmlspecialchars($row['nombre_producto'])?></h3>

               <?php
    // Mostrar etiqueta "Nuevo" si fue publicado hace 2 días o menos
    if (!empty($row['fecha_publicacion'])) {
        $fecha = new DateTime($row['fecha_publicacion']);
        $hoy = new DateTime();
        $dias = $fecha->diff($hoy)->days;

        if ($dias <= 2) {
            echo '
                <span style="
                    display:inline-block;
                    margin-bottom:6px;
                    padding:5px 10px;
                    background:#4CAF50;
                    color:white;
                    border-radius:8px;
                    font-size:13px;
                    font-weight:600;
                ">
                    🆕 Nuevo
                </span>
            ';
        }
    }
?>




                <?php if ($row['total_comentarios'] >= 5): ?>
    <div style="
        display:inline-block;
        background:#ffebee;
        color:#d32f2f;
        padding:4px 10px;
        border-radius:8px;
        font-size:12px;
        font-weight:700;
        margin-bottom:8px;
    ">
        🔥 Tendencia
    </div>
<?php endif; ?>


<?php 
$prom = round(floatval($row['promedio']), 1);
$total = intval($row['total_comentarios']);

if ($row['promedio'] !== null): 
?>
    <div style="margin:6px 0; font-size:14px; color:#f39c12;">
        <?php 
            $full = floor($prom);
            $half = ($prom - $full) >= 0.5 ? 1 : 0;
            $empty = 5 - ($full + $half);

            for ($i=0; $i<$full; $i++) echo "★";
            if ($half) echo "⯨"; // media estrella
            for ($i=0; $i<$empty; $i++) echo "☆"; // vacías
        ?>
        <span style="color:#444; font-size:13px;">
            <?= $prom ?> (<?= $total ?> comentarios)
        </span>
    </div>
<?php endif; ?>

<p><?=nl2br(htmlspecialchars($row['descripcion']))?></p>

                    <p><strong>💲 Precio:</strong> $<?=number_format($row['precio'], 0, ',', '.')?> / <?=htmlspecialchars($row['unidad'])?></p>
                    <p><strong>📦 Cantidad disponible:</strong> <?=intval($row['cantidad'])?> <?=htmlspecialchars($row['unidad'])?></p>

                    <?php if (intval($row['cantidad']) <= 5): ?>
    <span style="
        display:inline-block;
        margin-top:6px;
        padding:5px 10px;
        background:#ffcc00;
        color:#7a5f00;
        border-radius:8px;
        font-size:13px;
        font-weight:600;
    ">
        ⚠  Cantidad Baja
    </span>
<?php endif; ?>


                    <p style="color:#666; font-size:13px; margin-top:4px;">
    🕒 Publicado <?= tiempo_relativo($row['fecha_publicacion']) ?>
</p>


                    <hr>

                    <p>👤 <strong>Vendedor:</strong> <?=htmlspecialchars($row['vendedor'])?></p>
                    
                    <div class="contact-group">
                        <?php if(!empty($row['telefono'])): ?>
                            <p>📞 <strong>Teléfono:</strong> 
                                <a href="tel:<?=htmlspecialchars($row['telefono'])?>">
                                    <?=htmlspecialchars($row['telefono'])?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php if(!empty($row['whatsapp'])): ?>
                            <p>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" 
                                    style="width: 18px; height: 18px; vertical-align: middle; margin-right: 5px;">
                                <strong>WhatsApp:</strong> 
                                <a href="https://wa.me/57<?=htmlspecialchars($row['whatsapp'])?>" target="_blank" style="color: #25D366; font-weight: 600;">
                                    +57 <?=htmlspecialchars($row['whatsapp'])?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php if(!empty($row['correo'])): ?>
                            <p>📧 <strong>Correo:</strong> 
                                <a href="mailto:<?=htmlspecialchars($row['correo'])?>">
                                    <?=htmlspecialchars($row['correo'])?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ✅✅✅ BOTÓN DE VER PRODUCTO INSERTADO AQUÍ ✅✅✅ -->
                <a href="producto.php?id=<?= $row['id_producto'] ?>"
                   style="
                        margin-top:12px;
                        display:block;
                        text-align:center;
                        background:#4CAF50;
                        color:white;
                        padding:10px 15px;
                        border-radius:10px;
                        font-weight:600;
                        text-decoration:none;
                        transition:.3s;
                   ">
                    Ver producto
                </a>
                <!-- ✅✅✅ FIN DEL BOTÓN ✅✅✅ -->

            </div>
        <?php 
            endwhile; 
        } else {
           echo '<p style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #fff; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">🔍 No se encontraron productos con ese nombre.</p>';
        }
        ?>
    </div>

    <button id="btnTop" 
style="
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #8b4cafff;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    transition: 0.3s;
    z-index: 999;
">
↑
</button>

<script>
    const btnTop = document.getElementById("btnTop");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 400) {
            btnTop.style.display = "block";
        } else {
            btnTop.style.display = "none";
        }
    });

    btnTop.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>



</body>
</html>
