<?php
require_once 'api/config.php';
require_once 'api/tracking_helper.php';
$db = new Database();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activiha - Premium COD Store</title>
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://activiha.com/">
    <meta property="og:title" content="Activiha - Premium Products in Algeria">
    <meta property="og:description"
        content="Fast delivery & Cash on Delivery available nationwide. Shop premium products now!">
    <meta property="og:image" content="https://activiha.com/logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://activiha.com/">
    <meta property="twitter:title" content="Activiha - Premium Products in Algeria">
    <meta property="twitter:description"
        content="Fast delivery & Cash on Delivery available nationwide. Shop premium products now!">
    <meta property="twitter:image" content="https://activiha.com/logo.png">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo getTrackingScripts($db); ?>
</head>

<body>
    <header>
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <img src="logo.png" alt="Activiha" style="height: 40px;">
            </a>
            <nav class="nav-links">
                <a href="index.html">Home</a>
                <a href="#shop">Shop</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </nav>
            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-count">0</span>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Premium Products in Algeria</h1>
                <p>Fast delivery & Cash on Delivery available nationwide.</p>
            </div>
        </section>

        <section class="shop-section" id="shop">
            <div class="container">
                <div class="product-grid" id="product-grid">
                    <!-- Products will be loaded here via JS -->
                    <div class="loading">Loading products...</div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <img src="logo.png" alt="Activiha Logo"
                        style="height: 30px; margin-bottom: 1rem; filter: brightness(0) invert(1);">
                    <p>The best products with the best prices in Algeria.</p>
                </div>
                <div>
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Shop</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3>Contact Us</h3>
                    <p>WhatsApp: +213 555 123 456</p>
                    <p>Email: contact@richdigit.dz</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Activiha. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/app.js"></script>
</body>

</html>