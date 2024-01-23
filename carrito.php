<?php
session_start();

$host = '127.0.0.1:3307';
$db_usuario = 'root';
$db_contrasena = '';
$db_nombre = 'alameda_motor';
$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

// Mensajes de éxito o error
$successMensaje = '';
$errorMensaje = '';

// Funcionalidad para actualizar la cantidad del carrito
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    foreach ($_POST['cantidad'] as $productoId => $newCantidad) {
        $newCantidad = intval($newCantidad);

        // Si la nueva cantidad es 0, elimina el producto del carrito
        if ($newCantidad == 0) {
            unset($_SESSION['carrito'][$productoId]);
            continue; // Continúa con el siguiente producto
        }

        // Realiza una consulta para obtener las existencias del producto desde la base de datos
        $query = "SELECT existencias, nombre_producto, precio FROM productos WHERE id = '" . $db->real_escape_string($productoId) . "'";
        $result = $db->query($query);

        if ($row = $result->fetch_assoc()) {
            // Verifica si la cantidad solicitada no supera las existencias disponibles
            if ($newCantidad <= $row['existencias']) {
                // Actualiza la cantidad en el carrito
                $_SESSION['carrito'][$productoId]['cantidad'] = $newCantidad;
                $successMensaje = "Carrito actualizado.";
            } else {
                $errorMensaje = "Lo sentimos, " . $row['nombre_producto'] . " no tiene suficiente stock actualmente" . " disponemos de " . $row['existencias'] . " unidades.";
            }
        } else {
            $errorMensaje = "Producto no encontrado.";
        }
    }
}

// Compra realizada con éxito
if (isset($_SESSION['mensaje_compra_exitosa'])) {
    $mensajeCompraExitosa = $_SESSION['mensaje_compra_exitosa'];
    unset($_SESSION['mensaje_compra_exitosa']); // Elimina el mensaje para que no se muestre de nuevo
}

if (isset($_SESSION['mensaje_error_compra'])) {
    $mensajeErrorCompra = $_SESSION['mensaje_error_compra'];
    unset($_SESSION['mensaje_error_compra']); // Elimina el mensaje para que no se muestre de nuevo
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alameda Motor - Tu Gasolinera de Confianza</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>

<body>

<!-- Mensajes compra exitosa/error -->
<?php if (!empty($mensajeCompraExitosa)): ?>
    <div class= "alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $mensajeCompraExitosa; ?>
        <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
                <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($mensajeErrorCompra)): ?>
    <div class="alerta-error">
        <?php echo $mensajeErrorCompra; ?>
        <button type="button" class="close-alerta" aria-label="Close" onclick="this.parentElement.style.display='none';">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<!-- Fin mensaje de compra exitosa/error -->
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand"
                href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'index.php' : 'index.html'; ?>">Alameda
                Motor</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link"
                            href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'index.php' : 'index.html'; ?>">Inicio</a>
                    </li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="user.php">Mi Perfil</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Regístrate</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="carrito.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span id="contador-carrito">
                                    <?php echo array_sum($_SESSION['carrito'] ?? []); ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'valores.php' : 'valores.html'; ?>">Nuestros
                            Valores</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            Servicios
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item"
                                href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'tienda.php' : 'tienda.php'; ?>">Tienda</a>
                            <a class="dropdown-item"
                                href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'precios.php' : 'precios.html'; ?>">Combustibles</a>
                            <a class="dropdown-item"
                                href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'carga_electricos.php' : 'carga_electricos.html'; ?>">Carga
                                de eléctricos</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'contacto.php' : 'contacto.html'; ?>">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Carrito -->
    <div id="carrito-container">
        <?php if (!empty($successMensaje)): ?>
            <div class="success-mensaje">
                <?php echo $successMensaje; ?>
                <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorMensaje)): ?>
            <div class="error-mensaje">
                <?php echo $errorMensaje; ?>
                <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['carrito'])): ?>
            <p>Tu carrito está vacío.</p>
        <?php else: ?>
            <form action="carrito.php" method="post">
                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $totalCarrito = 0;
                    foreach ($_SESSION['carrito'] as $productoId => $cantidad):
                        $stmt = $db->prepare("SELECT nombre_producto, precio FROM productos WHERE id = ?");
                        $stmt->bind_param("i", $productoId);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        if ($producto = $resultado->fetch_assoc()):
                            $subtotal = $producto['precio'] * $cantidad;
                            $totalCarrito += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                                </td>
                                <td>
                                    <input type="number" name="cantidad[<?php echo $productoId; ?>]"
                                        value="<?php echo $cantidad; ?>" min="0">
                                </td>
                                <td>
                                    <?php echo number_format($producto['precio'], 2); ?>€
                                </td>
                                <td>
                                    <?php echo number_format($subtotal, 2); ?>€
                                </td>
                            </tr>
                            <?php
                        endif;
                        $stmt->close();
                    endforeach;
                    ?>
                    <tr>
                        <td colspan="3" align="right"><strong>Total:</strong></td>
                        <td><strong>
                                <?php echo number_format($totalCarrito, 2); ?>€
                            </strong></td>
                    </tr>
                </table>
                <div class="update-cart">
                    <button type="submit" name="action" value="update" class="btn btn-primary btn-block">Actualizar
                        Carrito</button>
                </div>
            </form>
            <a href="checkout.php" class="btn btn-success btn-block">Proceder a la compra</a>
        <?php endif; ?>
    </div>

    <!-- Chat Button -->
    <!-- Botón de Soporte Rápido -->
    <div class="support-button">
        Soporte Rápido
    </div>
    <!-- Ventana de Soporte Rápido -->
    <div class="support-window">
        <div class="support-header">
            Soporte Rápido
            <span class="close-support">&times;</span>
        </div>
        <div class="support-messages"></div>
        <div class="support-content">
            <button class="back-to-options" style="display:none;">Volver a las opciones</button>
            <div class="quick-help">
                <h4>¿Cómo podemos ayudarte hoy?</h4>
                <ul>
                    <li><button class="help-option" data-problem="account_issue">Problemas con mi cuenta</button></li>
                    <li><button class="help-option" data-problem="payment_issue">Problemas con el pago</button></li>
                    <li><button class="help-option" data-problem="technical_issue">Problemas técnicos</button></li>
                    <li><button class="help-option" data-problem="other_issue">Otros problemas</button></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Modal para Iniciar Sesión aquí ... -->

    <!-- Enlaces a JS de Bootstrap y dependencias aquí ... -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="main.js"></script>
</body>

</html>