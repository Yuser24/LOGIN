<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_de_votacion";

// Establecer la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Verificar si se ha enviado un token válido a través de GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["token"])) {
    $token = $_GET["token"];

    // Verificar si el token existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE token_olvido_contrasena = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El token es válido, mostrar el formulario para cambiar la contraseña
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Restablecer Contraseña</title>
            <link rel="stylesheet" href="stylesform.css">
        </head>
        <body>
            <div class="cambio-contraseña">
                <h2>Cambiar Contraseña</h2>
                <form action="restablecer_contraseña.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <label for="nueva_contrasena">Nueva Contraseña:</label>
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                    <input type="submit" value="Cambiar Contraseña">
                </form>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Token inválido o expirado.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el formulario de cambio de contraseña
    $token = $_POST["token"];
    $nueva_contrasena = $_POST["nueva_contrasena"];

    // Verificar si el token existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE token_olvido_contrasena = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token válido, actualizar la contraseña en la base de datos
        $row = $result->fetch_assoc();
        $usuario_id = $row["id_usuario"];
        
        // Hash de la nueva contraseña
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

        // Actualizar la contraseña y limpiar el token en la base de datos
        $sql_update = "UPDATE usuarios SET contrasena = ?, token_olvido_contrasena = NULL WHERE id_usuario = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hashed_password, $usuario_id);
        $stmt_update->execute();

        echo "Contraseña cambiada correctamente.";
    } else {
        echo "Token inválido o expirado.";
    }
} else {
    echo "Acceso no autorizado.";
}

$conn->close();  // Cerrar la conexión después de su uso
?>
