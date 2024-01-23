<?php
session_start(); // Inicia o continúa la sesión

// Mostrar mensaje de éxito o error si existe
$successMensaje = isset($_SESSION['successMensaje']) ? $_SESSION['successMensaje'] : '';
$errorMensaje = isset($_SESSION['errorMensaje']) ? $_SESSION['errorMensaje'] : '';

// Limpia las variables de sesión después de guardarlas en variables locales
unset($_SESSION['successMensaje'], $_SESSION['errorMensaje']);
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
<!-- Mostrar mensajes -->
<?php if ($successMensaje): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $successMensaje; ?>
        <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($errorMensaje): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $errorMensaje; ?>
        <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
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
<!-- Contenido Principal aquí ... -->

<!-- Banner de Bienvenida -->
<section class="service-item fade-in welcome-banner">
    <div class="container">
      <h1>Bienvenidos a Alameda Motor</h1>
      <p>Tu parada confiable en el camino, ofreciendo combustible de alta calidad y servicios excepcionales.</p>
    </div>
  </section>
  
<!-- Sección de Servicios -->
<section class="services-section">
    <div class="container">
      <h2>Nuestros Servicios</h2>
      <div class="row">
        <div class="service-item fade-in col-md-4" id="combustibles">
          <h3>Combustibles</h3>
          <p>Ofrecemos una amplia gama de combustibles para tu vehículo, incluyendo gasolina premium y diésel.</p>
        </div>
        <div class="service-item fade-in col-md-4" id="tienda">
          <h3>Tienda</h3>
          <p>Visita nuestra tienda de conveniencia con una selección de snacks, bebidas y productos de emergencia para el automóvil.</p>
        </div>
        <div class="service-item fade-in col-md-4" id="carga-vehiculos">
          <h3>Carga de Vehículos Eléctricos</h3>
          <p>Proporcionamos estaciones de carga rápida para vehículos eléctricos, para que puedas seguir tu viaje sin demoras.</p>
        </div>
      </div>
    </div>
  </section>
  
  
  <!-- Sección de Promociones Especiales -->
  <section class="service-item fade-in special-offers-section">
    <div class="container">
      <h2>Promociones Especiales</h2>
      <p>Aprovecha nuestras ofertas exclusivas y descuentos en servicios seleccionados.</p>
      <!-- Detalles de promociones aquí... -->
    </div>
  </section>
  
  <!-- Sección de Contacto -->
  <section class="service-item fade-in contact-section">
    <div class="container">
      <h2>Contacto</h2>
      <p>¿Tienes preguntas o comentarios? No dudes en contactarnos, estamos aquí para servirte.</p>
      <!-- Información de contacto aquí... -->
    </div>
  </section>
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
<footer>
<div id="cookieConsentContainer" class="cookie-consent-container">
    <div class="cookie-consent-content">
        <p>Este sitio utiliza cookies para mejorar la experiencia del usuario. Al utilizar nuestro sitio web, aceptas todas las cookies de acuerdo con nuestra <a href="politica-de-cookies.html">Política de Cookies</a> y nuestra <a href="politica-de-proteccion-de-datos.html">Política de Protección de Datos</a>.</p>
    </div>
    <div class="cookie-consent-buttons">
        <button id="acceptCookieConsent">Aceptar</button>
        <button id="declineCookieConsent">Rechazar</button>
    </div>
</div>
</footer>
</body>
</html>
