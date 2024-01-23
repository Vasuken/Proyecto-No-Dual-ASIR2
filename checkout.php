<?php
session_start();
// Asegúrate de que el usuario haya iniciado sesión y tenga productos en el carrito
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

$host = '127.0.0.1:3307';
$db_usuario = 'root';
$db_contrasena = '';
$db_nombre = 'alameda_motor';
$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->autocommit(FALSE);

        // Obtener usuario_id a partir de usuario_dni
        $usuario_dni = $_SESSION['usuario_dni'];
        $queryUsuario = "SELECT id FROM usuarios WHERE dni = ?";
        $stmt = $db->prepare($queryUsuario);
        $stmt->bind_param("s", $usuario_dni);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 0) {
            throw new Exception("No se encontró un usuario con ese DNI");
        }
        $fila = $resultado->fetch_assoc();
        $usuario_id = $fila['id'];
        $stmt->close();
        
        $total_compra = 0; // Inicializa el total de la compra
        
        foreach ($_SESSION['carrito'] as $productoId => $cantidad) {
            // Consultar el precio del producto desde la base de datos
            $queryPrecio = "SELECT precio FROM productos WHERE id = ?";
            $stmt = $db->prepare($queryPrecio);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $resultadoPrecio = $stmt->get_result();
            
            if ($resultadoPrecio->num_rows === 0) {
                throw new Exception("No se encontró un producto con ID " . $productoId);
            }
            $filaPrecio = $resultadoPrecio->fetch_assoc();
            $precioProducto = $filaPrecio['precio'];
            $stmt->close();
            
            // Calcular el precio total para este producto
            $precioTotalProducto = (float) $precioProducto * (int) $cantidad;
            $total_compra += $precioTotalProducto;
        }

        // Insertar la nueva orden
        $queryOrden = "INSERT INTO orders (usuario_id, total_price, created, modified, status) VALUES (?, ?, NOW(), NOW(), '1')";
        $stmt = $db->prepare($queryOrden);
        $stmt->bind_param("di", $usuario_id, $total_compra);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $order_id = $stmt->insert_id; // Obtener el ID del pedido insertado
        $stmt->close();

        // Insertar productos en la tabla order_items
        foreach ($_SESSION['carrito'] as $productoId => $cantidad) {
            $queryOrderItem = "INSERT INTO order_items (order_id, producto_id, quantity) VALUES (?, ?, ?)";
            $stmt = $db->prepare($queryOrderItem);
            $stmt->bind_param("iii", $order_id, $productoId, $cantidad);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar en la tabla order_items: " . $stmt->error);
            }
        }

        $db->commit();
        $_SESSION['carrito'] = [];
        $_SESSION['mensaje_compra_exitosa'] = "Compra realizada con éxito."; // Guardamos el mensaje en la sesión
        header("Location: carrito.php"); // Redirigimos al carrito
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['mensaje_error_compra'] = "Error en la compra: " . $e->getMessage(); // Guardamos el error en la sesión
        header("Location: carrito.php"); // Redirigimos al carrito
        exit;
    }

    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>
<body>
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
    <!-- Banner de Bienvenida -->
    <section class="register-banner">
        <div class="small-banner">
            <h1>Bienvenidos a Alameda Motor</h1>
            <p>Tu parada confiable en el camino, ofreciendo combustible de alta calidad y servicios excepcionales.</p>
        </div>
    </section> 
    <div class="container-payment">
    <div class="section carrito-check">
        <h2>Revisa tu carrito</h2>
        <ul class="products-list">
            <?php
            // Listar productos del carrito
            $total_compra = 0; // Asegúrate de inicializar esta variable antes de usarla.
            foreach ($_SESSION['carrito'] as $productoId => $cantidad) {
                $query = "SELECT nombre_producto, precio, imagen_producto FROM productos WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $productoId);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $producto = $resultado->fetch_assoc();

                $total_producto = $cantidad * $producto['precio'];
                $total_compra += $total_producto;

                echo "<li>";
                echo "<img src='{$producto['imagen_producto']}' alt='{$producto['nombre_producto']}'>";
                echo "<div>";
                echo "<strong>{$producto['nombre_producto']}</strong>, Cantidad: $cantidad, Precio: $total_producto €";
                echo "</div>";
                echo "</li>";
            }
            ?>
        </ul>
        <p class="total-purchase">Total de la compra: <?php echo $total_compra; ?> €</p>
    </div>

    <!-- Tarjeta -->
    <div class="section payment-details">
        <h2>Datos de Pago</h2>
        <form action="checkout.php" method="post" onsubmit="return validarFormulario()">
    <input type="text" name="nombre_tarjeta" id="nombre_tarjeta" placeholder="Nombre en la Tarjeta" required onkeypress="return soloLetras(event)">
    <input type="text" name="numero_tarjeta" id="numero_tarjeta" placeholder="**** **** **** ****" required onkeypress="return soloNumeros(event)" maxlength="16">
    <select name="mes_expiracion" required>
        <option value="" disabled selected>Mes</option>
        <?php for ($i = 1; $i <= 12; $i++) { echo "<option value='$i'>$i</option>"; } ?>
    </select>
    <select name="anio_expiracion" required>
        <option value="" disabled selected>Año</option>
        <?php for ($i = date("Y"); $i <= date("Y") + 10; $i++) { echo "<option value='$i'>$i</option>"; } ?>
    </select>
    <input type="password" name="cvv" id="cvv" placeholder="CVV" required onkeypress="return soloNumeros(event)" maxlength="3">
    <button type="submit" class="confirm-purchase">Confirmar Compra</button>
</form>
    </div>
    <!-- Tarjeta -->
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

</body>
<!-- Enlaces a JS de Bootstrap y dependencias aquí ... -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="main.js"></script>
</html>