<?php
session_start();
include 'conexion.php';

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos
    $id_producto = intval($_POST['id_producto']); // Seguridad: forzar a entero
    $cantidad = intval($_POST['cantidad']);
    $tipo = $_POST['tipo'];
    
    // (NUEVO) 1. Recibimos la observación y escapamos caracteres especiales para seguridad
    $observacion = mysqli_real_escape_string($conexion, $_POST['observacion']);
    
    // (NUEVO) 2. Obtenemos la fecha actual del sistema
    $fecha = date('Y-m-d H:i:s'); 

    // Consultar stock actual
    $consulta = mysqli_query($conexion, "SELECT cantidad FROM productos WHERE id_producto = $id_producto");
    
    if ($row = mysqli_fetch_assoc($consulta)) {
        $stock_actual = intval($row['cantidad']);
        $error = null; // Variable para controlar errores lógicos

        // Calcular nuevo stock
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
                $error = "Stock insuficiente. Tienes $stock_actual y intentas retirar $cantidad.";
            }
        }

        // Si no hay errores lógicos, procedemos a guardar en BD
        if (!isset($error)) {
            // A. Actualizamos el saldo en la tabla PRODUCTOS
            $sql_update = "UPDATE productos SET cantidad = $nuevo_stock WHERE id_producto = $id_producto";
            $resultado_update = mysqli_query($conexion, $sql_update);

            if ($resultado_update) {
                // (NUEVO) B. Si el update funcionó, registramos en la tabla MOVIMIENTOS
                // Nota: id_movimiento no se pone porque suele ser AUTO_INCREMENT en la BD
                $sql_insert = "INSERT INTO movimientos (id_producto, tipo, cantidad, fecha, observacion) 
                               VALUES ('$id_producto', '$tipo', '$cantidad', '$fecha', '$observacion')";
                
                $resultado_insert = mysqli_query($conexion, $sql_insert);

                if ($resultado_insert) {
                    // (NUEVO) C. Mensaje de éxito mostrando el NUEVO SALDO
                    $mensaje = "<div class='alert alert-$color_msj'>
                                    <i class='fa-solid fa-check-circle'></i> Éxito: Se $msj_tipo $cantidad unidades.<br>
                                    <strong>Nuevo Saldo en Sistema: $nuevo_stock</strong>
                                </div>";
                } else {
                    $mensaje = "<div class='alert alert-danger'>Stock actualizado, pero error al guardar el historial: " . mysqli_error($conexion) . "</div>";
                }

            } else {
                $mensaje = "<div class='alert alert-danger'>Error al actualizar producto: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>El producto seleccionado no existe.</div>";
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
        .form-select, .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; background-color: #fafafa; }
        .form-select:focus, .form-control:focus { border-color: #d4af37; box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); background-color: white; }
        .btn-action { background: #333; color: white; border-radius: 30px; padding: 12px; width: 100%; border: none; font-weight: 600; transition: 0.3s; }
        .btn-action:hover { background: #d4af37; }
        .btn-back { color: #888; text-decoration: none; display: block; text-align: center; margin-bottom: 20px; transition: color 0.3s; }
        .btn-back:hover { color: #d4af37; }
        
        /* Estilo para tabla de historial reciente */
        .history-table { font-size: 0.9rem; margin-top: 30px; }
        .history-table th { font-weight: 600; color: #555; }
    </style>
</head>
<body>
    
    <div class="header-brand">
        <!-- Asegúrate que la ruta de la imagen sea correcta -->
        <img src="logo.jpg" class="logo-small" alt="Logo">
    </div>

    <div class="container pb-5">
        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Volver al Menú</a>

        <div class="card-custom">
            <h2>Actualizar Stock</h2>
            <p class="text-center text-muted mb-4">Registra entradas de proveedores o salidas por venta.</p>

            <?php echo $mensaje; ?>

            <!-- Asegúrate que el action apunte a este mismo archivo si quieres ver el mensaje aquí mismo -->
            <form action="" method="POST"> 
                
                <div class="mb-4">
                    <label class="fw-bold mb-2">Seleccionar Joya</label>
                    <select name="id_producto" class="form-select" required>
                        <option value="">-- Buscar en la lista --</option>
                        <?php 
                        // Reiniciamos el puntero por si acaso se usó arriba, o volvemos a hacer el query
                        if(mysqli_num_rows($productos) > 0) {
                            mysqli_data_seek($productos, 0); 
                            while ($p = mysqli_fetch_assoc($productos)) { 
                        ?>
                            <option value="<?php echo $p['id_producto']; ?>">
                                <?php echo $p['nombre']; ?> (Ref: <?php echo $p['codigo']; ?>)
                            </option>
                        <?php 
                            } 
                        }
                        ?>
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

                <!-- (NUEVO) Campo de Observación -->
                <div class="mb-4">
                    <label class="fw-bold mb-2">Observación / Motivo</label>
                    <input type="text" name="observacion" class="form-control" placeholder="Ej: Factura #1024 o Venta cliente X" required>
                </div>

                <button type="submit" class="btn-action">
                    Registrar Movimiento
                </button>
            </form>

            <!-- (OPCIONAL) Consulta rápida: Últimos 5 movimientos generales -->
            <div class="mt-5">
                <h5 class="text-center mb-3">Últimos Movimientos Registrados</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover history-table text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Joya</th>
                                <th>Tipo</th>
                                <th>Cant.</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Consulta JOIN para traer el nombre del producto
                            $sql_historial = "SELECT m.*, p.nombre 
                                              FROM movimientos m 
                                              JOIN productos p ON m.id_producto = p.id_producto 
                                              ORDER BY m.fecha DESC LIMIT 5";
                            $res_historial = mysqli_query($conexion, $sql_historial);

                            if(mysqli_num_rows($res_historial) > 0){
                                while($mov = mysqli_fetch_assoc($res_historial)){
                                    $badge = ($mov['tipo'] == 'entrada') ? 'bg-success' : 'bg-warning text-dark';
                                    echo "<tr>";
                                    echo "<td>" . $mov['nombre'] . "</td>";
                                    echo "<td><span class='badge $badge'>" . ucfirst($mov['tipo']) . "</span></td>";
                                    echo "<td>" . $mov['cantidad'] . "</td>";
                                    echo "<td>" . date('d/m H:i', strtotime($mov['fecha'])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-muted'>Sin movimientos recientes.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Fin sección opcional -->

        </div>
    </div>
</body>
</html>