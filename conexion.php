<?php
$host = "localhost";
$usuario = "root";
$clave = "";
$bd = "mibd"; 

$conexion = mysqli_connect($host, $usuario, $clave, $bd);

// verificacion 
if (!$conexion) {
    die("Error crítico: No se pudo conectar a la base de datos '$bd'. Detalle: " . mysqli_connect_error());
}
?>