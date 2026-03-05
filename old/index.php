<?php

include './includes/include.php';
// Verificar si el usuario ya está autenticado
if(isset($_SESSION[APP_NAME]['cd_user'])) {
    // Redirigir al usuario a la página principal o a donde desees
    header('Location: ' . WEB_PATH . 'doAction?action=list_solicitudes');
    exit(); // Asegúrate de que se detenga la ejecución del código aquí
} else {
    // Si el usuario no está autenticado, redirigir a la página de inicio de sesión
    header('Location: ' . WEB_PATH . 'doAction?action=login_init');
    exit();
}

?>