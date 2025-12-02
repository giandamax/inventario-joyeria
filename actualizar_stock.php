<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $cantidad = intval($_POST['cantidad']);
    $tipo = $_POST['tipo'];

    $consulta = mysqli_query($conexion, "SELECT cantidad FROM productos WHERE id_producto = $id_producto");
    $producto = mysqli_fetch_assoc($consulta);
    $stock_actual = $producto['cantidad'];

    if ($tipo == 'entrada') {
        $nuevo_stock = $stock_actual + $cantidad;
        $msj_tipo = "agregaron";
        $color_msj = "success";
    } else {
        if ($stock_actual >= $cantidad) {
            $nuevo_stock = $stock_actual - $cantidad;
            $msj_tipo = "retiraron";
            $color_msj = "warning";
        } else {
            $error = "Stock insuficiente para realizar el retiro.";
        }
    }

    if (!isset($error)) {
        $sql_update = "UPDATE productos SET cantidad = $nuevo_stock WHERE id_producto = $id_producto";
        if (mysqli_query($conexion, $sql_update)) {
            $mensaje = "<div class='alert alert-$color_msj'>Movimiento registrado: Se $msj_tipo $cantidad unidades.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de base de datos.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>$error</div>";
    }
}

$productos = mysqli_query($conexion, "SELECT id_producto, nombre, codigo FROM productos ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos - Luce Dorata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #fff0f3;
            background-image: linear-gradient(315deg, #fff0f3 0%, #fff5f6 74%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .header-brand { text-align: center; padding: 20px 0; }
        .logo-small { width: 60px; height: 60px; border-radius: 50%; border: 2px solid white; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3); object-fit: cover; }

        .card-custom {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 { font-family: 'Playfair Display', serif; color: #333; margin-bottom: 20px; text-align: center; font-weight: 700; }
        
        .form-select, .form-control {
            border-radius: 10px; padding: 12px; border: 1px solid #eee; background-color: #fafafa;
        }
        .form-select:focus, .form-control:focus {
            border-color: #d4af37; box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); background-color: white;
        }

        .btn-action {
            background: #333; color: white; border-radius: 30px; padding: 12px; width: 100%; border: none; font-weight: 600; transition: 0.3s;
        }
        .btn-action:hover { background: #d4af37; }

        .btn-back { color: #888; text-decoration: none; display: block; text-align: center; margin-bottom: 20px; transition: color 0.3s; }
        .btn-back:hover { color: #d4af37; }
    </style>
</head>
<body>
    
    <div class="header-brand">
        <img src="logo.jpg" class="logo-small" alt="Logo">
    </div>

    <div class="container">
        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Volver al Menú</a>

        <div class="card-custom">
            <h2>Actualizar Stock</h2>
            <p class="text-center text-muted mb-4">Registra entradas de proveedores o salidas por venta.</p>

            <?php echo $mensaje; ?>

            <form action="actualizar_stock.php" method="POST">
                
                <div class="mb-4">
                    <label class="fw-bold mb-2">Seleccionar Joya</label>
                    <select name="id_producto" class="form-select" required>
                        <option value="">-- Buscar en la lista --</option>
                        <?php while ($p = mysqli_fetch_assoc($productos)) { ?>
                            <option value="<?php echo $p['id_producto']; ?>">
                                <?php echo $p['nombre']; ?> (Ref: <?php echo $p['codigo']; ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold mb-2">Tipo de Movimiento</label>
                        <select name="tipo" class="form-select">
                            <option value="entrada">📥 Entrada (Compra)</option>
                            <option value="salida">📤 Salida (Venta)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold mb-2">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" placeholder="Ej: 5" required>
                    </div>
                </div>

                <button type="submit" class="btn-action">
                    Registrar Movimiento
                </button>
            </form>
        </div>
    </div>
</body>
</html>