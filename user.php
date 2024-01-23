<?php
session_start();  // Inicia una nueva sesión o reanuda la existente

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $dni = $db->real_escape_string($_POST['dni']);
    $contrasena = $db->real_escape_string($_POST['password']);

    $stmt = $db->prepare("SELECT contraseña FROM usuarios WHERE dni = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        if (password_verify($contrasena, $usuario['contraseña'])) {
            $_SESSION['usuario_dni'] = $dni;
            $_SESSION['loggedin'] = true;
            
            $successMensaje = "Inicio de sesión exitoso. Redirigiendo...";
            // header("Location: user.php");
        } else {
            $errorMensaje = "Contraseña incorrecta.";
        }
    } else {
        $errorMensaje = "No se encontró usuario con ese DNI.";
    }
    $stmt->close();
}

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
} else {
  echo "Usuario no identificado.";
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

<!-- Contenido de la página de usuario -->
<div class="welcome-user">
    <h1>Bienvenido al Perfil del Usuario,  <?php echo $row['nombre'] ?? 'No disponible'; ?></h1>
    <div class="panel-usuario">
    <h2>Tu información</h2>
    <div class="datos-usuario">
    <p><strong>DNI:</strong> <?php echo $row['dni'] ?? 'No disponible'; ?></p>
        <p><strong>Nombre:</strong> <?php echo $row['nombre'] ?? 'No disponible'; ?></p>
        <p><strong>Apellido 1:</strong> <?php echo $row['apellido1'] ?? 'No disponible'; ?></p>
        <p><strong>Apellido 2:</strong> <?php echo $row['apellido2'] ?? 'No disponible'; ?></p>
        <p><strong>Correo:</strong> <?php echo $row['correo'] ?? 'No disponible'; ?></p>
    </div>
    <a href="mispedidos.php" class="btn-mejorar-plan">Ver mis pedidos</a>
<div>
    <form action="logout.php" method="post">
        <input type="submit" value="Cerrar Sesión" class="btn btn-danger"/>
    </form>
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
