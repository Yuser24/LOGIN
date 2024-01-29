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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo_usuario = $_POST["correo_usuario"];

    // Verificar si el correo electrónico existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo_electronico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generar un token único para el usuario
        $token = bin2hex(random_bytes(16));

        // Almacenar el token en la base de datos junto con el correo electrónico del usuario
        $sql = "UPDATE usuarios SET token_olvido_contrasena = ? WHERE correo_electronico = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $correo_usuario);
        $stmt->execute();

        // Configurar el correo electrónico
        $url = 'http://tudominio.com/restablecer_contraseña.php?token=' . $token;
        $subject = 'Restablecimiento de Contraseña';
        $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $url";
        $headers = 'From: tu_correo_electronico' . "\r\n" .
            'Reply-To: tu_correo_electronico' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // Enviar el correo electrónico
        if (mail($correo_usuario, $subject, $message, $headers)) {
            echo "Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.";
        } else {
            echo "Error al enviar el correo electrónico.";
        }
    } else {
        echo "El correo electrónico no está registrado en nuestra base de datos.";
    }

    $stmt->close();
    $conn->close();  // Cerrar la conexión después de su uso
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidé mi Contraseña</title>
    <link rel="stylesheet" href="stylesform.css">
</head>
<body>
    <div class="olvido-contraseña">
        <h2>Olvidé mi Contraseña</h2>
        <form action="olvide_contraseña.php" method="POST">
            <label for="correo_usuario">Correo Electrónico:</label>
            <input type="email" id="correo_usuario" name="correo_usuario" required>
            <input type="submit" value="Enviar Enlace de Restablecimiento">
        </form>
    </div>
</body>
</html>
