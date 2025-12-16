<?php
session_start();
include("conexion/conexion.php");

// *** INICIO: DEBUGGING - Verificar conexión después del include ***
// Si la conexión falló o la variable $conexion no está definida
if (!isset($conexion) || mysqli_connect_errno()) {
    $error = "Error Fatal de Conexión: No se pudo conectar a la base de datos. Verifica 'conexion/conexion.php'. Código: " . (isset($conexion) ? mysqli_connect_errno() : 'N/A');
}
// *** FIN: DEBUGGING ***


if ($_SESSION['tipo'] !== 'campesino') {

    // ✅ Fondo de toda la página
    echo "
    <style>
    body {
        background: linear-gradient(135deg, #d8f3dc, #b7e4c7, #95d5b2);
        background-attachment: fixed;
        margin: 0;
        padding: 0;
    }
    </style>
    ";

    // ✅ Cuadro bonito
    echo "
    <div style='
        max-width:450px;
        margin:80px auto;
        background:#e8f5e9;
        padding:30px;
        border-radius:18px;
        box-shadow:0 8px 20px rgba(0,0,0,0.15);
        font-family:Arial, sans-serif;
        text-align:center;
    '>
        <h2 style='color:#d32f2f;'>❌ Acceso denegado</h2>
        <p>Esta sección es exclusiva para <strong>campesinos</strong>.</p>
        <a href='index.php' style='
            display:inline-block;
            padding:12px 20px;
            background:#4CAF50;
            color:white;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        '>Volver al inicio</a>
    </div>
    ";
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $nombre_producto = trim($_POST['nombre_producto']);
    $descripcion = trim($_POST['descripcion']);
    // Aseguramos que los valores sean numéricos para evitar problemas con la base de datos
    $precio = (isset($_POST['precio']) && is_numeric($_POST['precio'])) ? floatval($_POST['precio']) : 0;
    $cantidad = (isset($_POST['cantidad']) && is_numeric($_POST['cantidad'])) ? intval($_POST['cantidad']) : 0;
    $unidad = trim($_POST['unidad']);
    $whatsapp = trim($_POST['whatsapp']);
    $id_vendedor = $_SESSION['id_usuario'];
    
    // Validamos que WhatsApp sea un número, incluso si lo pasamos como string a la BD
    if (empty($nombre_producto) || empty($descripcion) || $precio <= 0 || $cantidad <= 0 || empty($unidad) || !preg_match('/^[0-9]+$/', $whatsapp)) {
        $error = "⚠️ Completa todos los campos correctamente. Precio y Cantidad deben ser positivos, y el WhatsApp debe ser solo números.";
    } else {
        $imagen = null;

        if (!empty($_FILES['imagen']['name'])) {
            $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
            $rutaDestino = "img/productos/" . $nombreImagen;

            if (!is_dir("img/productos")) {
                // @: Suprimimos el warning si la carpeta ya existe por alguna razón
                @mkdir("img/productos", 0777, true); 
            }

            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                $imagen = $nombreImagen;
            } else {
                $error = "❌ Error al subir la imagen. Verifica permisos del servidor o tamaño del archivo.";
            }
        }

        if (empty($error)) {
            $sql = "INSERT INTO productos (nombre_producto, descripcion, precio, cantidad, unidad, whatsapp, imagen, id_vendedor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            
            // *** DEBUGGING: Check for prepare error ***
            if (!$stmt) {
                 $error = "❌ Error de consulta (PREPARE): No se pudo preparar el statement. Detalle: " . mysqli_error($conexion) . " - SQL: " . htmlspecialchars($sql);
            } else {
                mysqli_stmt_bind_param($stmt, "sssdsssi", $nombre_producto, $descripcion, $precio, $cantidad, $unidad, $whatsapp, $imagen, $id_vendedor);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "✅ Producto publicado correctamente. ¡Listo para la venta!";
                    // Limpiamos las variables para que el formulario se muestre vacío
                    unset($nombre_producto, $descripcion, $precio, $cantidad, $unidad, $whatsapp);
                } else {
                    // *** DEBUGGING: Improved execute error reporting ***
                    $error = "❌ Error al publicar el producto (EXECUTE): " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar producto | AgroConnect Urabá</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.php?page=productos">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Para el icono de WhatsApp si lo desea --><style>
        :root {
            --color-verde-principal: #4CAF50;
            --color-verde-oscuro: #388E3C;
            --color-fondo-claro: #F5F5F7; 
            --color-texto-oscuro: #1D1D1F; 
            --color-azul-claro: #007AFF; 
            --color-borde-claro: #E5E5EA; 
            --color-fondo-segment: #F2F2F7;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; 
            
            background-color: var(--color-fondo-claro); /* Fallback */
            
            
            background-image: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.2)),
                              url('img/fondo.jpeg'),
                              url('img/fondo.jpg'); 
            
            background-size: cover; 
            background-position: center center; 
            background-attachment: fixed; 
            background-repeat: no-repeat;
            
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        

        .form-container {
            background-color: #FFFFFF;
            padding: 25px;
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08), 0 1px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid var(--color-borde-claro); 
            max-width: 420px; 
            margin: auto; 
            position: relative;
        }
        
        header h2 {
            font-size: 1.8em;
            font-weight: 700; 
            color: var(--color-texto-oscuro); 
            margin-bottom: 25px;
            text-align: center;
        }

        
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px; 
            margin-top: 5px;
            margin-bottom: 18px;
            border: 1px solid var(--color-borde-claro);
            border-radius: 12px; 
            box-sizing: border-box; 
            font-size: 0.95em;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #FFFFFF; 
            resize: vertical; 
        }
        
        input:focus, textarea:focus {
            border-color: var(--color-verde-principal); 
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2); 
            outline: none; 
        }

        label {
            display: block; 
            color: var(--color-texto-oscuro);
            font-weight: 600; 
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .grid-2 input {
            margin-bottom: 18px;
        }
        
        /
        .input-group {
            border: 1px solid var(--color-borde-claro); 
            border-radius: 12px;
            padding: 0 12px;
            margin-bottom: 18px;
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
        }
        .input-group input {
            padding: 12px 0;
            border: none;
            margin: 0;
        }

        .whatsapp-icon {
            color: #25D366;
            font-size: 1.3em;
            margin-right: 10px;
        }

        
        button[type="submit"] {
            background: linear-gradient(180deg, var(--color-verde-principal) 0%, var(--color-verde-oscuro) 100%);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 12px; 
            cursor: pointer;
            font-size: 1em;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
            transition: opacity 0.2s, transform 0.1s;
            width: 100%; 
            margin-top: 25px;
        }

        button[type="submit"]:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9em;
            overflow-wrap: break-word; 
        }
        .error-message {
            color: #D32F2F;
            background-color: #FFEBEE;
        }
        .success-message {
            color: #2E7D32;
            background-color: #E8F5E9;
        }
        
        
        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            border: 1px solid var(--color-borde-claro);
            display: block;
            padding: 12px;
            cursor: pointer;
            border-radius: 12px;
            color: var(--color-texto-oscuro);
            font-weight: 500;
            background-color: #FFFFFF;
            width: 100%;
            text-align: center;
            transition: background-color 0.2s, border-color 0.2s;
            margin-bottom: 18px;
            box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.05); 
        }

        .custom-file-upload:hover {
            background-color: #F8F8F8;
            border-color: #A0A0A0;
        }
        
        
        .unidad-select-container {
            margin-bottom: 18px;
            padding: 3px; 
            background-color: var(--color-fondo-segment);
            border-radius: 10px;
            display: flex;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
        }
        
        .unidad-select-container .unidad-option {
            flex-grow: 1;
            text-align: center;
            padding: 8px 0;
            cursor: pointer;
            border-radius: 8px;
            font-size: 0.8em;
            font-weight: 500;
            color: var(--color-texto-oscuro);
            transition: background-color 0.2s, box-shadow 0.2s, color 0.2s;
            border: none;
            background-color: transparent;
        }

        .unidad-select-container .unidad-option.selected {
            background-color: #FFFFFF;
            color: var(--color-texto-oscuro); 
            font-weight: 700; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
        
        
        .footer-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid var(--color-borde-claro);
        }

        .footer-link a {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 0.9em;
            color: var(--color-azul-claro);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .footer-link a:hover {
            color: var(--color-verde-principal);
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const unidadInput = document.querySelector("input[name='unidad']");
            const opciones = document.querySelectorAll(".unidad-option");
            const fileInput = document.getElementById('imagen');
            const fileLabel = document.querySelector('.custom-file-upload');
            
            
            const initializeSelection = () => {
                const currentValue = unidadInput.value;
                if (currentValue) {
                    opciones.forEach(op => {
                        if (op.dataset.value === currentValue) {
                            op.classList.add("selected");
                        }
                    });
                }
            };

            
            opciones.forEach(op => {
                op.addEventListener("click", () => {
                    opciones.forEach(o => o.classList.remove("selected"));
                    op.classList.add("selected");
                    unidadInput.value = op.dataset.value;
                });
            });
            
            
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    fileLabel.innerHTML = `🖼️ Imagen seleccionada: ${e.target.files[0].name}`;
                } else {
                    fileLabel.innerHTML = `🌄 Cargar imagen del producto (Opcional)`;
                }
            });

            initializeSelection();
        });
    </script>
</head>
<body>
<div class="form-container">
    <header>
        <h2>📤 Publicar Nuevo Producto</h2>
    </header>

    <?php if(!empty($error)): ?>
        <p class='message error-message'><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <p class='message success-message'><?=htmlspecialchars($success)?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="nombre_producto">Nombre del producto</label>
        <input name="nombre_producto" id="nombre_producto" type="text" required value="<?= isset($nombre_producto) ? htmlspecialchars($nombre_producto) : '' ?>">

        <label for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3" required><?= isset($descripcion) ? htmlspecialchars($descripcion) : '' ?></textarea>

        <div class="grid-2">
            <div>
                <label for="precio">Precio ($)</label>
                <input name="precio" id="precio" type="number" step="0.01" min="0.01" required value="<?= isset($precio) ? htmlspecialchars($precio) : '' ?>">
            </div>
            <div>
                <label for="cantidad">Cantidad</label>
                <input name="cantidad" id="cantidad" type="number" min="1" required value="<?= isset($cantidad) ? htmlspecialchars($cantidad) : '' ?>">
            </div>
        </div>
        
        <label for="whatsapp">Número de WhatsApp (Contacto)</label>
        <div class="input-group">
            <span class="whatsapp-icon">📞</span>
            <input name="whatsapp" id="whatsapp" type="text" placeholder="Ejemplo: 3124567890" required value="<?= isset($whatsapp) ? htmlspecialchars($whatsapp) : '' ?>">
        </div>

        <label>Unidad de medida (Segment Control)</label>
        <!-- Este input hidden almacena el valor seleccionado por el JavaScript --><input type="hidden" name="unidad" id="unidad_hidden" required value="<?= isset($unidad) ? htmlspecialchars($unidad) : '' ?>">

        <div class="unidad-select-container">
            <div class="unidad-option" data-value="Kilogramos">⚖️ Kg</div>
            <div class="unidad-option" data-value="Bultos">🥔 Bultos</div>
            <div class="unidad-option" data-value="Litros">💧 Lt</div>
            <div class="unidad-option" data-value="Cajas">📦 Cajas</div>
            <div class="unidad-option" data-value="Unidades">🔢 Und</div>
        </div>

        <label for="imagen" class="custom-file-upload">
            🌄 Cargar imagen del producto (Opcional)
        </label>
        <input type="file" name="imagen" id="imagen" accept="image/*">

        <button type="submit">Publicar Producto</button>
    </form>
    
    <div class="footer-link">
        <a href="productos.php">🛒 Ver productos publicados</a>
        <a href="index.php">🏠 Volver al inicio</a>
    </div>

</div>
</body>
</html>
