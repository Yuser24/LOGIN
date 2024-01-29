<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_de_votacion";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo_usuario"];
    $contrasena = $_POST["contrasena"];

    $errors = array();  // Array para almacenar mensajes de error

    // Verificar si el nombre cumple con ciertos criterios
    if (strlen($nombre) < 3) {
        $errors[] = "El nombre debe tener al menos 3 caracteres.";
    }

    if (strlen($nombre) >= 3 && preg_match('/[0-9]/', $nombre)) {
        $errors[] = "El nombre no debe contener números.";
    }

    // Verificar la longitud de la contraseña
    if (strlen($contrasena) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Verificar si la contraseña tiene al menos una letra mayúscula, una letra minúscula y un número
    if (!preg_match('/[A-Z]/', $contrasena) || !preg_match('/[a-z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
        $errors[] = "La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.";
    }

    if (empty($errors)) {
        // Implementar la verificación de cuenta por correo electrónico (puedes enviar un correo de verificación aquí)
        // ...

        // Hash de la contraseña después de verificar los criterios
        $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

        // Utilizar consultas preparadas para prevenir inyecciones SQL
        $sql = "INSERT INTO usuarios (nombre, correo_electronico, contrasena) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Vincular parámetros y ejecutar la consulta
        $stmt->bind_param("sss", $nombre, $correo, $contrasena_hashed);

        if ($stmt->execute()) {
            echo "Registro exitoso";
        } else {
            echo "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Mostrar mensajes de error en un mensaje emergente con JavaScript
        echo '<script>alert("' . implode("\\n", $errors) . '");</script>';
    }
}

$conn->close();
?>
