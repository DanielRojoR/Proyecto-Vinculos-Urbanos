<!-- index.php - Página principal -->
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VendeTodo Chile - Compra y venta online</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">OutdoorPartner.cl</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="categorias.php">Categorías</a></li>
                    <li><a href="publicar.php">Publicar aviso</a></li>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <a href="perfil.php" class="btn">Mi cuenta</a>
                    <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Ingresar</a>
                    <a href="registro.php" class="btn btn-secondary">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="search-section">
        <div class="container">
            <form action="busqueda.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="¿Qué estás buscando?">
                <select name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php
                    // Conexión a la base de datos
                    include "config/db.php";
                    
                    // Consulta para obtener categorías
                    $query = "SELECT * FROM categorias ORDER BY nombre ASC";
                    $result = mysqli_query($conn, $query);
                    
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                    }
                    ?>
                </select>
                <select name="region">
                    <option value="">Todo Chile</option>
                    <option value="RM">Región Metropolitana</option>
                    <option value="V">Valparaíso</option>
                    <option value="VIII">Bío Bío</option>
                    <!-- Agregar más regiones -->
                </select>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>
    </div>

    <section class="featured-products">
        <div class="container">
            <h2>Anuncios destacados de la semana</h2>
            <div class="products-grid">
                <?php
                // Consulta para obtener los productos más clickeados de la semana
                $fecha_inicio = date('Y-m-d', strtotime('-7 days'));
                $query = "SELECT p.*, u.nombre as vendedor, u.region, c.nombre as categoria 
                          FROM producto p 
                          JOIN usuario u ON p.usuario_id = u.id 
                          JOIN categorias c ON p.categoria_id = c.id
                          WHERE p.fecha_publicacion >= '$fecha_inicio'
                          ORDER BY p.visitas DESC 
                          LIMIT 12";
                
                $result = mysqli_query($conn, $query);
                
                if(mysqli_num_rows($result) > 0) {
                    while($producto = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="product-card">
                            <a href="producto.php?id=<?php echo $producto['id']; ?>">
                                <div class="product-image">
                                    <?php if(!empty($producto['imagen_principal'])): ?>
                                        <img src="uploads/<?php echo $producto['imagen_principal']; ?>" alt="<?php echo $producto['titulo']; ?>">
                                    <?php else: ?>
                                        <img src="img/no-image.png" alt="Sin imagen">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo $producto['titulo']; ?></h3>
                                    <p class="price">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
                                    <p class="location"><?php echo $producto['region']; ?></p>
                                    <p class="category"><?php echo $producto['categoria']; ?></p>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No hay productos destacados esta semana.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section class="categories-overview">
        <div class="container">
            <h2>Explora por categorías</h2>
            <div class="categories-grid">
                <?php
                // Reiniciar la consulta para mostrar categorías con conteo de productos
                $query = "SELECT c.id, c.nombre, c.icono, COUNT(p.id) as total 
                          FROM categorias c
                          LEFT JOIN producto p ON c.id = p.categoria_id AND p.estado = 'activo'
                          GROUP BY c.id
                          ORDER BY total DESC
                          LIMIT 8";
                
                $result = mysqli_query($conn, $query);
                
                while($categoria = mysqli_fetch_assoc($result)) {
                    ?>
                    <a href="categorias.php?id=<?php echo $categoria['id']; ?>" class="category-card">
                        <div class="category-icon"><?php echo $categoria['icono']; ?></div>
                        <h3><?php echo $categoria['nombre']; ?></h3>
                        <p><?php echo $categoria['total']; ?> anuncios</p>
                    </a>
                    <?php
                }
                ?>
            </div>
            <div class="view-all">
                <a href="categorias.php" class="btn">Ver todas las categorías</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>VendeTodo.cl</h3>
                    <p>La plataforma de compra y venta más confiable de Chile.</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="categorias.php">Categorías</a></li>
                        <li><a href="publicar.php">Publicar aviso</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Ayuda</h3>
                    <ul>
                        <li><a href="como-funciona.php">Cómo funciona</a></li>
                        <li><a href="preguntas-frecuentes.php">Preguntas frecuentes</a></li>
                        <li><a href="seguridad.php">Consejos de seguridad</a></li>
                        <li><a href="terminos.php">Términos y condiciones</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p>Email: contacto@vendetodo.cl</p>
                    <p>Teléfono: +56 2 2123 4567</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">Facebook</a>
                        <a href="#" class="social-icon">Instagram</a>
                        <a href="#" class="social-icon">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> VendeTodo.cl - Todos los derechos reservados</p>
            </div>
        </div>
    </footer>
</body>
</html>