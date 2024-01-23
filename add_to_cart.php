<?php
session_start();

// Inicializar el carrito como un arreglo vacío si aún no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

$host = '127.0.0.1:3307';
$db_usuario = 'root';
$db_contrasena = '';
$db_nombre = 'alameda_motor';
$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

if (isset($_GET['id'])) {
    $productoId = $_GET['id'];

    // Consulta para obtener las existencias
    $query = "SELECT existencias, nombre_producto FROM productos WHERE id = '" . $db->real_escape_string($productoId) . "'";
    $result = $db->query($query);

    if ($row = $result->fetch_assoc()) {
        if ($row['existencias'] > 0) {
            if (isset($_SESSION['carrito'][$productoId])) {
                $_SESSION['carrito'][$productoId]++;
            } else {
                $_SESSION['carrito'][$productoId] = 1;
            }
            $_SESSION['mensaje_carrito'] = "Cantidad actualizada en el carrito para " . $row['nombre_producto'] . ".";
        } else {
            $_SESSION['mensaje_carrito'] = "Lo sentimos, " . $row['nombre_producto'] . " no tiene stock disponible.";
        }
    } else {
        $_SESSION['mensaje_carrito'] = "Producto no encontrado.";
    }
}

// Actualizar la cantidad total en la sesión
$_SESSION['cantidad_total_carrito'] = array_sum($_SESSION['carrito']);

header('Location: tienda.php');
exit();

