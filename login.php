<?php
session_start();
include 'conexion.php';

// verificacion de seccion 
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($conexion)) {
        die("Error: No hay conexión con la base de datos.");
    }

    $user = $_POST['usuario'];
    $pass = $_POST['clave']; 

    $user = mysqli_real_escape_string($conexion, $user);
    $pass = mysqli_real_escape_string($conexion, $pass);

    $sql = "SELECT * FROM usuarios WHERE usuario = '$user'";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && $row = mysqli_fetch_assoc($resultado)) {
        if ($pass == $row['contraseña']) {
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventario Bisutería</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fuente bonita (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            /* Fondo con degradado elegante (Azul a Morado) */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .brand-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 4rem;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            background-color: #f0f2f5;
            border: none;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #e8f0fe;
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 30px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: opacity 0.3s;
        }

        .btn-custom:hover {
            opacity: 0.9;
            color: white;
        }

        .input-group-text {
            border-radius: 30px 0 0 30px;
            border: none;
            background: #f0f2f5;
            color: #764ba2;
        }
        
        .form-control {
            border-radius: 0 30px 30px 0;
        }

        .title {
            color: #333;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Icono de Diamante/Joya -->
    <i class="fa-solid fa-gem brand-icon"></i>
    
    <h2 class="title">Bienvenido</h2>
    <p class="subtitle">Sistema de Gestión de Bisutería</p>

    <?php if(!empty($error)) { echo "<div class='alert alert-danger rounded-pill'>$error</div>"; } ?>

    <form action="login.php" method="POST">
        
        <div class="mb-4 input-group">
            <span class="input-group-text ps-3"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
        </div>

        <div class="mb-4 input-group">
            <span class="input-group-text ps-3"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
        </div>

        <button type="submit" class="btn btn-custom w-100 mb-3">
            INGRESAR
        </button>
        
        <div class="text-muted small">
            &copy; 2025 Calabozo - Guárico
        </div>
    </form>
</div>

</body>
</html>