<?php
// Iniciar sesión en la base de datos
$host = '127.0.0.1:3307'; // Nombre del servidor de la base de datos
$db_usuario = 'root'; // Usuario de la base de datos
$db_contrasena = ''; // Contraseña de la base de datos
$db_nombre = 'alameda_motor'; // Nombre de la base de datos

$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro'])) {
    $dni = $db->real_escape_string($_POST['dni']);
    $nombre = $db->real_escape_string($_POST['nombre']);
    $apellido1 = $db->real_escape_string($_POST['apellido1']);
    $apellido2 = $db->real_escape_string($_POST['apellido2']);
    $correo = $db->real_escape_string($_POST['email']);
    $telefono = $db->real_escape_string($_POST['telefono']);
    $contraseña = $db->real_escape_string($_POST['password']);
    $confirmacion_contraseña = $db->real_escape_string($_POST['confirm_password']);

    // Verificar si el correo ya está registrado
    $verificacion = $db->prepare("SELECT correo FROM usuarios WHERE correo = ?");
    $verificacion->bind_param("s", $correo);
    $verificacion->execute();
    $resultado = $verificacion->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "El correo electrónico ya está registrado.";
    } elseif ($contraseña !== $confirmacion_contraseña) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Hashear la contraseña antes de guardarla
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO usuarios (dni, nombre, apellido1, apellido2, correo, telefono, contraseña) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $dni, $nombre, $apellido1, $apellido2, $correo, $telefono, $contraseña_hash);

        if ($stmt->execute()) {
            $mensaje = "Nuevo registro creado con éxito.";
        } else {
            $mensaje = "Error: Se ha producido un error al registrar la solicitud. " . $stmt->error;
        }
        $stmt->close();
    }
    $verificacion->close();
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
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a la hoja de estilos -->
</head>

<body>
    <!--Mensaje Registo -->
    
<div id="mensajeRegistro" class="mensaje-registro" style="<?php if (!isset($mensaje))
    echo 'display: none;'; ?>">
    <?php if (isset($mensaje))
        echo htmlspecialchars($mensaje); ?>
    <button onclick="document.getElementById('mensajeRegistro').style.display='none'">&times;</button>
</div>

    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Alameda Motor</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
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

    <!-- Formulario -->
    <div class="container">
    <form class="registration-form" action="register.php" method="post">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido1">Primer Apellido:</label>
                <input type="text" id="apellido1" name="apellido1" required>
            </div>
            <div class="form-group">
                <label for="apellido2">Segundo Apellido:</label>
                <input type="text" id="apellido2" name="apellido2" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="registro">Registrar</button>
        </form>
    </div>
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


    <!-- Enlaces a JS de Bootstrap y dependencias aquí ... -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="main.js"></script>
</body>

</html>