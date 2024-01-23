<?php
session_start(); // Iniciar la sesión

// Desactivar todas las variables de sesión
$_SESSION = array();

// Eliminar la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destruir la sesión

header("Location: index.html"); // Redirigir a index.html
exit;
?>
