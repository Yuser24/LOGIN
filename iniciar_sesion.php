<?php
// Inicia la sesión
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_de_votacion";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST["nombre_usuario"];
    $correo_usuario = $_POST["correo_usuario"];
    $contrasena_usuario = $_POST["contrasena_usuario"];

    // Utilizar consultas preparadas para prevenir inyecciones SQL
    $sql = "SELECT * FROM usuarios WHERE nombre = ? AND correo_electronico = ?";
    $stmt = $conn->prepare($sql);

    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("ss", $nombre_usuario, $correo_usuario);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($contrasena_usuario, $row["contrasena"])) {
            // Almacenar el id_usuario en la sesión
            $_SESSION["id_usuario"] = $row["id_usuario"];

            // Evitar que la página se almacene en la caché
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            // Redirigir al usuario a la página principal
            header("Location: index.html");
            exit();
        } else {
            // Mensaje de contraseña incorrecta
            echo "Contraseña incorrecta";
        }
    } else {
        // Usuario no encontrado, o cualquier otro mensaje de error
        echo "Usuario no encontrado";
    }

    $stmt->close();
}

$conn->close();
?>
