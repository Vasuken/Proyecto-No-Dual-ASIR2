<?php
session_start();

// Conexión a la base de datos
$host = '127.0.0.1:3307';
$db_usuario = 'root';
$db_contrasena = '';
$db_nombre = 'alameda_motor';

$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

$errorMensaje = '';
$successMensaje = '';

// Código de inicio de sesión existente aquí...

if (isset($_SESSION['usuario_dni'])) {
    $dni = $_SESSION['usuario_dni'];
    $query = "SELECT dni, nombre, apellido1, apellido2, correo FROM usuarios WHERE dni = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontró información del usuario.";
    }
    $stmt->close();

    // Consulta para obtener los pedidos del usuario
    $queryPedidos = "SELECT orders.id AS order_id, productos.nombre_producto AS nombre_producto, productos.precio, order_items.quantity 
    FROM orders JOIN order_items ON orders.id = order_items.order_id 
    JOIN productos ON order_items.producto_id = productos.id 
    WHERE orders.usuario_id = (SELECT id FROM usuarios WHERE dni = ?)
    ORDER BY orders.id";

    $stmt = $db->prepare($queryPedidos);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultPedidos = $stmt->get_result();

    // Estructura para agrupar los productos por pedido
    $pedidos = [];
    while ($pedido = $resultPedidos->fetch_assoc()) {
        $pedidos[$pedido['order_id']]['productos'][] = [
            'nombre_producto' => $pedido['nombre_producto'],
            'precio' => $pedido['precio'],
            'quantity' => $pedido['quantity']
        ];
    }
    $stmt->close();
}

$db->close();
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
  <!-- Mensajes -->
  <?php if($errorMensaje): ?>
    <div class="error-mensaje" id="errorMensaje">
      <?php echo $errorMensaje; ?>
      <button class="close-btn" onclick="document.getElementById('errorMensaje').style.display='none'">&times;</button>
    </div>
  <?php endif; ?>

  <?php if($successMensaje): ?>
    <div class="success-mensaje" id="successMensaje">
      <?php echo $successMensaje; ?>
      <button class="close-btn" onclick="document.getElementById('successMensaje').style.display='none'">&times;</button>
    </div>
  <?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
  <!-- Pedidos -->
  <h2 class="your-order">Tus pedidos</h2>
  <div class="order-grid">
        <?php foreach ($pedidos as $order_id => $pedido): ?>
            <div class="order-card">
                <div class="order-content">
                    <h2 class="order-title">Pedido #<?php echo htmlspecialchars($order_id); ?></h2>
                    <?php foreach ($pedido['productos'] as $producto): ?>
                        <p class="order-details">
                            Producto: <?php echo htmlspecialchars($producto['nombre_producto']); ?><br>
                            Cantidad: <?php echo htmlspecialchars($producto['quantity']); ?><br>
                            Precio: <?php echo htmlspecialchars($producto['precio']); ?>€
                        </p>
                    <?php endforeach; ?>
                    <a href="detalle_pedido.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="order-button">Ver Detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
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