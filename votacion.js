// Inicializar el contador de votos y el estado de votación
let totalVotos = [0, 0, 0, 0, 0, 0]; // Un array para almacenar los votos de cada opción
let usuarioVoto = false; // Variable para rastrear si el usuario ya ha votado

// Obtener todos los botones de votar y elementos de total de votos
const botonesVotar = document.querySelectorAll('[id^="botonVotar"]');
const elementosTotalVotos = document.querySelectorAll('[id^="totalVotos"]');

// Agregar un evento de clic a cada botón de votar
botonesVotar.forEach((boton, index) => {
    boton.addEventListener('click', function() {
        // Verificar si el usuario ya ha votado antes de incrementar el contador
        if (!usuarioVoto) {
            // Incrementar el contador de votos para la opción correspondiente
            totalVotos[index]++;

            // Actualizar el elemento que muestra el total de votos
            elementosTotalVotos[index].textContent = totalVotos[index];

            // Deshabilitar todos los botones después de votar
            botonesVotar.forEach(boton => boton.disabled = true);

            // Marcar que el usuario ha votado
            usuarioVoto = true;

            // Función para manejar el voto
            manejarVoto(index + 1); // Sumar 1 porque las opciones comienzan desde 1 en lugar de 0
        }
    });
});

// Función para manejar el voto
function manejarVoto(id_opcion) {
    console.log("Enviando voto para la opción:", id_opcion);

    // Enviar el voto al servidor usando AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'registrar_voto.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Asegúrate de enviar el valor correcto del ID de la opción
    console.log('Datos a enviar:', 'id_opcion=' + id_opcion);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Manejar la respuesta del servidor si es necesario
            console.log(xhr.responseText);
        }
    }

    // Asegúrate de que el objeto esté en el estado OPENED antes de enviar
    if (xhr.readyState === XMLHttpRequest.OPENED) {
        // Enviar los datos del voto al servidor
        xhr.send('id_opcion=' + id_opcion);
    }
}
