<?php
session_start();
include("conexion/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
$id = $_SESSION['id_usuario'];

$stmt = mysqli_prepare($conexion, "SELECT id_producto, nombre_producto, precio, cantidad, imagen FROM productos WHERE id_vendedor = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | AgroConnect Urabá</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --color-verde-principal: #4CAF50;
        --color-verde-oscuro: #388E3C;
        --color-fondo-claro: #F5F5F7; 
        --color-texto-oscuro: #1D1D1F; 
        --color-sombra-suave: rgba(0, 0, 0, 0.08); 
        --color-gris-claro: #E0E0E0;
    }
    
    body {
        font-family: Poppins, sans-serif;
        margin: 0;
        padding: 30px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh; 
        background-image: url('img/fondo.jpeg');
        background-size: cover; 
        background-position: center center; 
        background-attachment: fixed; 
        background-repeat: no-repeat;
        background-color: #E8F5E9;
    }

    .perfil-container {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 30px; 
    border-radius: 20px; 
    box-shadow: 0 10px 30px var(--color-sombra-suave); 
    border: 1px solid #D1D1D6; 
    width: 100%;
    max-width: 450px; 
    text-align: left;

    /* ✅ Centrado perfecto del contenedor */
    margin-left: auto;
    margin-right: auto;
}



    h2 {
        font-size: 1.8em;
        font-weight: 600;
        color: var(--color-texto-oscuro);
        margin-bottom: 5px;
        padding-bottom: 5px;
        border-bottom: 2px solid var(--color-verde-principal);
    }

    h3 {
        font-size: 1.4em;
        font-weight: 600;
        color: var(--color-verde-oscuro);
        margin-top: 25px;
        margin-bottom: 15px;
    }

    .user-info {
        font-size: 0.95em;
        color: #555;
        margin-bottom: 20px;
    }
    
    .user-info strong {
        font-weight: 600;
        color: var(--color-texto-oscuro);
    }

    /* ✅ BOTONES SUBIDOS ARRIBA */
    .perfil-actions {
        margin-bottom: 20px;
    }

    .perfil-actions a {
        margin-right: 5px;
    }

    .action-link, .logout-link, .nav-link {
        margin-top: 5px !important;
        margin-bottom: 5px !important;
    }

    .product-card {
        display: flex;
        gap: 15px;
        background-color: #F8F8F8;
        padding: 12px; 
        margin-bottom: 12px;
        border-radius: 12px; 
        border: 1px solid var(--color-gris-claro);
        transition: box-shadow 0.2s;
    }
    
    .product-card:hover {
        box-shadow: 0 4px 10px rgba(76, 175, 80, 0.1);
    }
    
    .product-card img {
        max-width: 80px; 
        height: 80px; 
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--color-gris-claro);
    }

    .product-details strong {
        display: block;
        font-size: 1.1em;
        margin-bottom: 4px;
        color: var(--color-texto-oscuro);
    }
    
    .product-actions {
        margin-top: 8px;
        display: flex;
        gap: 10px;
    }

    .edit-link {
        background-color: var(--color-verde-principal);
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8em;
    }

    .delete-link {
        background-color: #FF5722;
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8em;
    }

    .action-link {
        background-color: var(--color-verde-principal);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
    }

    .logout-link {
        background-color: #f44336;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
    }

    .nav-link {
        display: inline-block;
        color: var(--color-verde-principal);
        text-decoration: none;
        font-weight: 600;
        padding: 10px 15px;
        border-radius: 8px;
    }

    .alert {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid #c3e6cb;
        font-size: 0.9em;
        font-weight: 600;
    }

    /* Centrar perfectamente la tarjeta del perfil y hacerla un poco más pequeña */
.center-card {
  width: 100%;
  max-width: 420px;
  margin: -25px auto 20px; /* SUBIDO */
  border-radius: 18px;
  background: #ffffff;
  box-shadow: 0 12px 35px rgba(0,0,0,0.12);
  padding: 24px;
  text-align: center;
}

/* Por si el contenedor padre estaba “empujando” a la izquierda */
.perfil-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

</style>

</head>

<body>
<div class="perfil-container">

    <?php if(isset($_GET['update']) && $_GET['update']=="ok"): ?>
        <div class="alert">✅ Producto actualizado exitosamente</div>
    <?php endif; ?>

    <h2>Mi Perfil de Cultivo 🧑‍🌾</h2>

    <!-- ✅ Tarjeta tipo Facebook -->
<div style="
    width:100%;
    max-width:380px;
    background:white;
    padding:25px;
    border-radius:20px;
    margin:5px auto 0 auto;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    text-align:center;
    display:block;
">

    <!-- Foto de perfil -->
    <img src="img/avatar_default.png" 
     style="
        width:75px;
        height:75px;
        border-radius:50%;
        object-fit:cover;
        border:2px solid #4CAF50;
        margin-bottom:8px;
     ">

    <!-- Nombre del usuario -->
    <h3 style="margin:0; font-size:1.3em; color:#333;">
        <?= htmlspecialchars($_SESSION['nombre']) ?>
    </h3>

    <!-- Tipo de usuario -->
    <p style="margin:5px 0; color:#666; font-size:0.9em;">
        <?= htmlspecialchars($_SESSION['tipo']) ?> ✅
    </p>

    <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
</div>

    
    <p class="user-info">
        Conectado como: <strong><?=htmlspecialchars($_SESSION['nombre'])?></strong><br>
        Tipo de Usuario: <strong><?=htmlspecialchars($_SESSION['tipo'])?></strong>
    </p>

    <!-- ✅✅✅ BOTONES SUBIDOS ARRIBA ✅✅✅ -->
   <div class="perfil-actions">
    <a href="publicar.php" class="action-link">Publicar nuevo producto</a>
    <a href="logout.php" class="logout-link">Cerrar sesión</a>
    <a href="index.php" class="nav-link">🏠 Volver al inicio</a>
</div>

<?php
$total_productos = mysqli_num_rows($result);
?>

<h3 style="margin-top:-10px;">Productos Publicados</h3>

<p style="margin-top:-10px; color:#555; font-size:0.9em;">
    📦 Has publicado <strong><?= $total_productos ?></strong> productos
</p>


    <div class="product-list">
        <?php $has_products = false; ?>
        <?php while($row = mysqli_fetch_assoc($result)): $has_products = true; ?>
        
       <?php
$cant = intval($row['cantidad']);

if ($cant <= 5) {
    $color_borde = "#e53935"; // rojo
} elseif ($cant <= 20) {
    $color_borde = "#fbc02d"; // amarillo
} else {
    $color_borde = "#4CAF50"; // verde
}
?>

<div class="product-card" style="border-left: 15px solid <?= $color_borde ?>;">

            <?php 
            $imagePath = !empty($row['imagen']) ? 'img/productos/' . $row['imagen'] : 'img/placeholder.png'; 
            ?>
            <img src="<?=$imagePath?>" alt="Imagen de <?=htmlspecialchars($row['nombre_producto'])?>" 
                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/E8E8E8/888888?text=N/A'">

            <div class="product-details">
                <strong><?=htmlspecialchars($row['nombre_producto'])?></strong>
                <p>Precio: $<?=number_format($row['precio'], 2)?> | Cantidad Dispo: <?=intval($row['cantidad'])?></p>
                
                <div class="product-actions">
                    <a href="editarproducto.php?id=<?=intval($row['id_producto'])?>" class="edit-link">✏️ Editar</a>

                    <a href="eliminar_producto.php?id=<?=intval($row['id_producto'])?>"
                       class="delete-link"
                       onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                       🗑️ Eliminar
                    </a>
                </div>
            </div>
        </div>

        <?php endwhile; ?>

        <?php if (!$has_products): ?>
            <p style="color:#777; font-style:italic;">Aún no has publicado ningún producto.</p>
        <?php endif; ?>
    </div>

</div>

<!-- ✅ BOTÓN VOLVER ARRIBA -->
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
