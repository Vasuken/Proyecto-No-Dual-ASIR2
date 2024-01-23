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

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
  $dni = $db->real_escape_string($_POST['dni']);
  $contrasena = $db->real_escape_string($_POST['password']);

  // Busca el usuario en la base de datos
  $stmt = $db->prepare("SELECT contraseña FROM usuarios WHERE dni = ?");
  $stmt->bind_param("s", $dni);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($usuario = $resultado->fetch_assoc()) {
    if (password_verify($contrasena, $usuario['contraseña'])) {
      // Establece variables de sesión
      $_SESSION['usuario_dni'] = $dni;
      $_SESSION['loggedin'] = true;

      // Guarda el mensaje de éxito en la sesión
      $_SESSION['successMensaje'] = "Inicio de sesión exitoso.";

      // Redirige a index.php
      header("Location: index.php");
      exit();
    } else {
      $_SESSION['errorMensaje'] = "Contraseña incorrecta.";
      header("Location: index.php");
      exit();
    }
  } else {
    $_SESSION['errorMensaje'] = "No se encontró usuario con ese DNI.";
    header("Location: index.php");
    exit();
  }
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
</head>
<body>
<!-- Barra de Navegación -->
<nav class="navbar navbar-expand-lg custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Alameda Motor</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="index.html">Inicio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Regístrate</a>
          </li>
        <li class="nav-item">
          <a class="nav-link" href="valores.html">Nuestros Valores</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Servicios
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="tienda.php">Tienda</a>
            <a class="dropdown-item" href="precios.html">Combustibles</a>
            <a class="dropdown-item" href="carga_electricos.html">Carga de eléctricos</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contacto.html">Contacto</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- Contenido Principal aquí ... -->

<!-- Banner de Bienvenida -->
<section class="register-banner">
    <div class="small-banner">
      <h1>Bienvenidos a Alameda Motor</h1>
      <p>Tu parada confiable en el camino, ofreciendo combustible de alta calidad y servicios excepcionales.</p>
    </div>
  </section>

<!-- Formulario de Inicio de Sesión -->
<div class="login-container">
      <form class="login-form" action="login.php" method="post">
        <h2>Iniciar Sesión</h2>
        <div class="form-group">
          <label for="login-dni">DNI <span class="required">*</span></label>
          <input type="text" id="login-dni" name="dni" pattern="\d{8}[A-Za-z]{1}" title="Introduce un DNI válido. Ejemplo: 12345678A" required>
        </div>
        <div class="form-group">
          <label for="login-password">Contraseña <span class="required">*</span></label>
          <input type="password" id="login-password" name="password" required>
        </div>
        <div class="form-group">
          <button type="submit" name="login">Iniciar Sesión</button>
        </div>
        <div class="form-links">
          <a href="#">¿Olvidaste tu contraseña?</a>
          <a href="register.html">Registrarse</a>
        </div>
      </form>
  
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
  

<!-- Enlaces a JS de Bootstrap y dependencias aquí ... -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="main.js"></script>
</body>
</html>
