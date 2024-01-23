<?php
session_start(); // Inicia o continúa la sesión

// Conexión a la base de datos
$host = '127.0.0.1:3307';
$db_usuario = 'root';
$db_contrasena = '';
$db_nombre = 'alameda_motor';

$db = new mysqli($host, $db_usuario, $db_contrasena, $db_nombre);
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}

$allowedPriceOrders = ['asc', 'desc'];
$allowedCategories = ['Mantenimiento', 'Mecánica', 'Herramientas'];

$priceOrder = isset($_POST['priceOrder']) && in_array($_POST['priceOrder'], $allowedPriceOrders) ? $_POST['priceOrder'] : 'asc';
$category = isset($_POST['category']) && in_array($_POST['category'], $allowedCategories) ? $_POST['category'] : 'all';

$query = "SELECT * FROM productos";
$params = [];
$types = '';

if ($category !== 'all') {
    $query .= " WHERE categoria = ?";
    $params[] = $category;
    $types .= 's'; // 's' indica que el parámetro es una cadena (string)
}

$query .= " ORDER BY precio " . ($priceOrder === 'asc' ? 'ASC' : 'DESC');

$stmt = $db->prepare($query);

if ($category !== 'all') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Mostrar mensaje si existe
if (isset($_SESSION['mensaje'])) {
    echo "<p>" . $_SESSION['mensaje'] . "</p>";
    unset($_SESSION['mensaje']);
}

// Verificar si hay un mensaje para mostrar
if (isset($_SESSION['mensaje_carrito'])) {
    $mensajeCarrito = $_SESSION['mensaje_carrito'];
    // Luego de mostrarlo, eliminar el mensaje de la sesión
    unset($_SESSION['mensaje_carrito']);
} else {
    $mensajeCarrito = '';
}

// Procesar $result aquí, por ejemplo, mostrar los productos

$stmt->close();
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

    <!-- Mensaje de añadido al carrito -->
    <?php if (!empty($mensajeCarrito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $mensajeCarrito; ?>
            <button type="button" class="close" aria-label="Close" onclick="this.parentElement.style.display='none';">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Llamada a la función que actualiza el carrito  -->
    <?php if (isset($_SESSION['carrito'])): ?>
    <script>
        window.onload = function() {
            actualizarContadorCarrito(<?php echo $_SESSION['carrito']; ?>);
        };
    </script>
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
    <!-- Banner de Bienvenida -->
    <section class="register-banner">
        <div class="small-banner">
            <h1>Bienvenidos a Alameda Motor</h1>
            <p>Tu parada confiable en el camino, ofreciendo combustible de alta calidad y servicios excepcionales.</p>
        </div>
    </section>

    <!-- Menú de Ordenamiento -->
    <form action="tienda.php" method="POST">
        <div class="sorting-buttons-container">
            <div>
                <label for="price-sort">Ordenar por Precio:</label>
                <select id="price-sort" name="priceOrder" onchange="this.form.submit()">
                    <option value="asc" <?php echo $priceOrder == 'asc' ? 'selected' : ''; ?>>Menor a Mayor</option>
                    <option value="desc" <?php echo $priceOrder == 'desc' ? 'selected' : ''; ?>>Mayor a Menor</option>
                </select>
            </div>
            <div>
                <label for="category-sort">Ordenar por Categoría:</label>
                <select id="category-sort" name="category" onchange="this.form.submit()">
                    <option value="Todas" <?php echo $category == 'Todas' ? 'selected' : ''; ?>>Todas</option>
                    <option value="Mantenimiento" <?php echo $category == 'Mantenimiento' ? 'selected' : ''; ?>>
                        Mantenimiento</option>
                    <option value="Mecánica" <?php echo $category == 'Mecánica' ? 'selected' : ''; ?>>Mecánica</option>
                    <option value="Herramientas" <?php echo $category == 'Herramientas' ? 'selected' : ''; ?>>Herramientas
                    </option>
                </select>
            </div>
        </div>
    </form>

    <!-- Mostrar Productos -->
    <div class="products-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-item">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($row['imagen_producto']); ?>" alt="Imagen del producto">
                </div>
                <div class="product-info">
                    <p class="product-name">
                        <?php echo htmlspecialchars($row['nombre_producto']); ?>
                    </p>
                    <p class="product-price">
                        <?php echo htmlspecialchars($row['precio']); ?>€
                    </p>
                    <div>
                    <a href="add_to_cart.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="add-to-cart-btn">Añadir
                        al Carrito</a>
                    </div>    
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Paginación -->
    <!-- Agregado al final del div con clase "products-grid" -->
    <div class="pagination">
        <a href="#" class="pagination-link">Anterior</a>
        <a href="#" class="pagination-link active">1</a>
        <a href="tienda_page_2.html" class="pagination-link">2</a>
        <a href="#" class="pagination-link">3</a>
        <a href="#" class="pagination-link">Siguiente</a>
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