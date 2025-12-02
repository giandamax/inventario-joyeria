<?php
session_start();
include 'conexion.php';

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal - Luce Dorata</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fuentes Elegantes -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">

    <style>
        body {
            /* Fondo suave y elegante (Rosa pastel muy claro) */
            background-color: #fff0f3;
            background-image: linear-gradient(315deg, #fff0f3 0%, #fff5f6 74%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        /* Barra de navegación transparente/minimalista */
        .navbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #d4af37 !important; /* Color Dorado */
            font-size: 1.5rem;
        }

        .btn-logout {
            color: #888;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 5px 15px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            background-color: #d4af37;
            color: white;
            border-color: #d4af37;
        }

        /* Estilo del Logo Central */
        .logo-container {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 40px;
            animation: fadeInDown 1s ease;
        }

        .brand-logo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%; /* Lo hace redondo */
            border: 4px solid #fff;
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3); /* Sombra dorada suave */
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .welcome-text {
            font-family: 'Playfair Display', serif;
            color: #555;
            margin-top: 15px;
            font-size: 1.8rem;
        }

        /* Tarjetas del Menú */
        .menu-card {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.15); /* Sombra dorada al pasar mouse */
        }

        .icon-box {
            font-size: 3rem;
            margin-bottom: 20px;
            /* Degradado dorado para los iconos */
            background: -webkit-linear-gradient(#d4af37, #b48811);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .card-text {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        /* Botones personalizados */
        .btn-menu {
            background: #333;
            color: white;
            border-radius: 25px;
            padding: 8px 25px;
            transition: 0.3s;
            border: none;
        }

        .btn-menu:hover {
            background: #d4af37; /* Dorado al pasar mouse */
            color: white;
        }

        /* Animación de entrada */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- Navbar Minimalista -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-regular fa-gem"></i> Luce Dorata</a>
    <div class="ms-auto d-flex align-items-center">
        <span class="me-3 text-muted d-none d-md-block">Hola, <b><?php echo $_SESSION['nombre']; ?></b></span>
        <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="container">
    
    <!-- Sección del Logo e Imagen de Marca -->
    <div class="logo-container">
        <!-- AQUÍ SE CARGA TU IMAGEN -->
        <!-- Asegúrate de que la imagen se llame 'logo.jpg' o 'logo.png' -->
        <img src="logo.jpg" alt="Luce Dorata Logo" class="brand-logo">
        <h1 class="welcome-text">Gestión de Inventario</h1>
        <p class="text-muted">Administración exclusiva y elegante</p>
    </div>
    
    <!-- Grid de Opciones -->
    <div class="row justify-content-center">
        
        <!-- Tarjeta 1: Ver Inventario -->
        <div class="col-md-4 mb-4">
            <div class="menu-card" onclick="location.href='inventario.php';">
                <div class="icon-box">
                    <i class="fa-solid fa-gem"></i> <!-- Icono de joya -->
                </div>
                <h5 class="card-title">Mis Joyas</h5>
                <p class="card-text">Consulta y administra las piezas disponibles.</p>
                <a href="inventario.php" class="btn btn-menu">Ver Colección</a>
            </div>
        </div>

        <!-- Tarjeta 2: Registrar Producto -->
        <div class="col-md-4 mb-4">
            <div class="menu-card" onclick="location.href='registrar_producto.php';">
                <div class="icon-box">
                    <i class="fa-solid fa-circle-plus"></i> <!-- Icono de más -->
                </div>
                <h5 class="card-title">Nueva Pieza</h5>
                <p class="card-text">Registra una nueva entrada de bisutería.</p>
                <a href="registrar_producto.php" class="btn btn-menu">Agregar</a>
            </div>
        </div>

        <!-- Tarjeta 3: Movimientos -->
        <div class="col-md-4 mb-4">
            <div class="menu-card" onclick="location.href='actualizar_stock.php';">
                <div class="icon-box">
                    <i class="fa-solid fa-rotate"></i> <!-- Icono de rotación -->
                </div>
                <h5 class="card-title">Movimientos</h5>
                <p class="card-text">Registrar ventas o reposición de stock.</p>
                <a href="actualizar_stock.php" class="btn btn-menu">Actualizar</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>