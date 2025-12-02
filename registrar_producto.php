<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $categoria = mysqli_real_escape_string($conexion, $_POST['categoria']);
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    $sql = "INSERT INTO productos (codigo, nombre, categoria, precio, cantidad, descripcion, fecha_registro) 
            VALUES ('$codigo', '$nombre', '$categoria', '$precio', '$cantidad', '$descripcion', NOW())";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div class='alert alert-custom-success'><i class='fa-solid fa-check-circle'></i> ¡Pieza registrada con éxito!</div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Pieza - Luce Dorata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #fff0f3;
            background-image: linear-gradient(315deg, #fff0f3 0%, #fff5f6 74%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding-bottom: 40px;
        }
        
        /* Encabezado con Logo pequeño */
        .header-brand {
            text-align: center;
            padding: 20px 0;
        }
        .logo-small {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
            object-fit: cover;
        }
        
        .card-custom {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #eee;
            padding: 12px;
            background-color: #fafafa;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background-color: white;
            border-color: #d4af37; /* Dorado al escribir */
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }

        .btn-gold {
            background: #333;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-gold:hover {
            background: #d4af37;
            color: white;
            transform: translateY(-2px);
        }

        .btn-back {
            color: #888;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .btn-back:hover {
            color: #d4af37;
        }

        .alert-custom-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #badbcc;
        }
    </style>
</head>
<body>
    
    <div class="header-brand">
        <!-- Logo pequeño arriba -->
        <img src="logo.jpg" class="logo-small" alt="Logo">
    </div>

    <div class="container">
        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Volver al Menú</a>

        <div class="card-custom">
            <h2>Registrar Nueva Pieza</h2>
            
            <?php echo $mensaje; ?>

            <form action="registrar_producto.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label>Código de Referencia</label>
                        <input type="text" name="codigo" class="form-control" required placeholder="Ej: COL-001">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Nombre de la Pieza</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Collar Perlas de Río">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label>Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="Collar">Collar</option>
                            <option value="Pulsera">Pulsera</option>
                            <option value="Anillo">Anillo</option>
                            <option value="Zarcillos">Zarcillos</option>
                            <option value="Juego Completo">Juego Completo</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-4">
                        <label>Precio ($)</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required placeholder="0.00">
                    </div>
                    <div class="col-md-3 mb-4">
                        <label>Stock Inicial</label>
                        <input type="number" name="cantidad" class="form-control" required placeholder="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label>Descripción / Detalles</label>
                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Materiales, colores, detalles..."></textarea>
                </div>

                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-save me-2"></i> Guardar en Inventario
                </button>
            </form>
        </div>
    </div>
</body>
</html>