<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_de_votacion";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>
