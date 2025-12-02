<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    $sql_borrar = "DELETE FROM productos WHERE id_producto = $id_eliminar";
    mysqli_query($conexion, $sql_borrar);
    header("Location: inventario.php");
}

$where = "";
if (isset($_POST['buscar'])) {
    $busqueda = mysqli_real_escape_string($conexion, $_POST['termino']);
    $where = "WHERE nombre LIKE '%$busqueda%' OR categoria LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%'";
}

$sql = "SELECT * FROM productos $where ORDER BY id_producto DESC";
$resultado = mysqli_query($conexion, $sql);

// (NUEVO) Inicializamos las variables para los totales
$total_stock_fisico = 0;
$total_valor_inventario = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colección - Luce Dorata</title>
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

        .header-brand { text-align: center; padding: 20px 0; }
        .logo-small { width: 60px; height: 60px; border-radius: 50%; border: 2px solid white; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3); object-fit: cover; }

        .container-table {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 20px;
        }

        h2 { font-family: 'Playfair Display', serif; color: #333; font-weight: 700; }

        .search-input {
            border-radius: 25px;
            border: 1px solid #ddd;
            padding: 10px 20px;
        }
        
        .btn-search {
            background: #333; color: white; border-radius: 25px; padding: 10px 25px; border: none;
        }
        .btn-search:hover { background: #d4af37; color: white; }

        /* Tabla Estilizada */
        .table { margin-bottom: 0; }
        .table thead th {
            background-color: #333;
            color: #d4af37; /* Texto dorado en cabecera */
            border: none;
            font-weight: 400;
            font-family: 'Playfair Display', serif;
            letter-spacing: 1px;
        }
        .table tbody tr { transition: background 0.3s; }
        .table tbody tr:hover { background-color: #fffaf0; } /* Fondo crema al pasar mouse */
        
        .badge-stock {
            background-color: #f8d7da; color: #842029; font-weight: normal; border-radius: 10px; padding: 5px 10px;
        }
        
        .price-tag { font-weight: 600; color: #333; }
        
        .btn-delete {
            color: #dc3545; border: 1px solid #f8d7da; background: white; border-radius: 50%; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;
        }
        .btn-delete:hover { background: #dc3545; color: white; }

        .btn-back { color: #888; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .btn-back:hover { color: #d4af37; }

        /* (NUEVO) Estilos para el pie de totales */
        .summary-footer {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #eee;
        }
        .summary-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .summary-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .summary-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }
        .text-gold { color: #d4af37 !important; }
    </style>
</head>
<body>
    
    <div class="header-brand">
        <!-- Ajusta la ruta si es necesario -->
        <img src="logo.jpg" class="logo-small" alt="Logo">
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Volver</a>
            <h2 class="m-0">Colección de Joyas</h2>
            <div style="width: 80px;"></div> 
        </div>

        <div class="container-table">
            <!-- Buscador -->
            <form action="inventario.php" method="POST" class="d-flex mb-4 justify-content-center">
                <input type="text" name="termino" class="form-control search-input me-2" placeholder="Buscar joya..." style="max-width: 300px;">
                <button type="submit" name="buscar" class="btn-search"><i class="fa-solid fa-search"></i></button>
                <a href="inventario.php" class="btn btn-outline-secondary ms-2" style="border-radius: 25px;">Ver todo</a>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>CÓDIGO</th>
                            <th>PIEZA</th>
                            <th>CATEGORÍA</th>
                            <th>PRECIO</th>
                            <th class="text-center">STOCK</th>
                            <th class="text-center">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // (NUEVO) Comprobamos si hay filas antes de entrar
                        if(mysqli_num_rows($resultado) > 0) {
                            while ($row = mysqli_fetch_assoc($resultado)) { 
                                
                                // (NUEVO) Cálculos matemáticos dentro del bucle
                                $stock_item = intval($row['cantidad']);
                                $precio_item = floatval($row['precio']);
                                
                                $total_stock_fisico += $stock_item; // Suma unidades
                                $total_valor_inventario += ($stock_item * $precio_item); // Suma Dinero
                        ?>
                        <tr>
                            <td class="text-muted small"><?php echo $row['codigo']; ?></td>
                            <td class="fw-bold text-dark"><?php echo $row['nombre']; ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $row['categoria']; ?></span></td>
                            <td class="price-tag">$<?php echo number_format($row['precio'], 2); ?></td>
                            <td class="text-center">
                                <?php if($row['cantidad'] < 5) { ?>
                                    <span class="badge-stock">¡Solo <?php echo $row['cantidad']; ?>!</span>
                                <?php } else { ?>
                                    <span class="fw-bold text-muted"><?php echo $row['cantidad']; ?></span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <a href="inventario.php?eliminar=<?php echo $row['id_producto']; ?>" 
                                   class="btn-delete"
                                   title="Eliminar Pieza"
                                   onclick="return confirm('¿Eliminar esta pieza de la colección?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            } // Fin While
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>No hay productos registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- (NUEVO) Sección de Totales al final de la tabla -->
            <?php if(mysqli_num_rows($resultado) > 0) { ?>
            <div class="summary-footer row">
                <div class="col-md-6 border-end d-flex justify-content-center align-items-center mb-3 mb-md-0">
                    <div class="summary-item">
                        <span class="summary-label">Total Piezas (Físico)</span>
                        <span class="summary-value"><?php echo number_format($total_stock_fisico); ?></span>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <div class="summary-item">
                        <span class="summary-label">Valor Total Colección</span>
                        <span class="summary-value text-gold">$<?php echo number_format($total_valor_inventario, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php } ?>
            <!-- Fin sección totales -->

        </div>
    </div>
</body>
</html>