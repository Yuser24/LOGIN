<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_de_votacion";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conn->connect_error]));
}

session_start(); // Asegúrate de iniciar la sesión si aún no está iniciada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén el id_usuario de la sesión
    $id_usuario = $_SESSION["id_usuario"];
    $id_opcion = $_POST["id_opcion"];

    // Verificar si el usuario ya ha votado por esta opción
    $sql_verificar = "SELECT * FROM votos WHERE id_usuario_voto = ? AND id_opcion_voto = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_usuario, $id_opcion);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows == 0) {
        // El usuario no ha votado por esta opción, proceder a insertar el voto
        $sql_insertar = "INSERT INTO votos (id_usuario_voto, id_opcion_voto) VALUES (?, ?)";
        $stmt_insertar = $conn->prepare($sql_insertar);
        $stmt_insertar->bind_param("ii", $id_usuario, $id_opcion);

        if ($stmt_insertar->execute()) {
            $sql_actualizar_votos = "UPDATE opciones SET total_votos = total_votos + 1 WHERE id_opcion = ?";
            $stmt_actualizar_votos = $conn->prepare($sql_actualizar_votos);
            $stmt_actualizar_votos->bind_param("i", $id_opcion);
            $stmt_actualizar_votos->execute();

            echo json_encode(["message" => "Voto registrado exitosamente"]);
        } else {
            echo json_encode(["error" => "Error al registrar el voto"]);
        }

        $stmt_insertar->close();
        $stmt_actualizar_votos->close();
    } else {
        echo json_encode(["error" => "Ya has votado por esta opción"]);
    }

    $stmt_verificar->close();
}

$conn->close();
?>
