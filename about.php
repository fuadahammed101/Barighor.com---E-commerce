<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">BariGhor.com</a>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#tiles">Tiles</a></li>
                    <li><a href="index.php#electronics">Electronics</a></li>
                    <li><a href="about.php">About</a></li>
                    <?php if (isset($_SESSION['admin'])): ?>
                    <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="customer_dashboard.php">Dashboard (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a> | 
                            <a href="logout.php">Logout</a>
                        <?php else: ?>
                            <a href="login.php">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>About Us</h1>
        <p>Welcome to BariGhor.com, your trusted Tiles store.</p>
        <p>Our location:</p>
        <div style="width: 100%; max-width: 600px; height: 400px; margin-bottom: 1rem;">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.1234567890123!2d90.1234567890123!3d23.1234567890123!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c123456789ab%3A0x123456789abcdef!2sYour%20Store%20Location!5e0!3m2!1sen!2sbd!4v1690000000000!5m2!1sen!2sbd" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <p><a href="https://maps.app.goo.gl/wUtdnVCXA9WFVFht8" target="_blank" rel="noopener noreferrer">View on Google Maps</a></p>
    </main>
    <div class="site-footer">
        <div class="footer-container">
            <p>&copy; 2025 E-Commerce Store </p>
            <div> <a href="https://github.com/fuadahammed101">Developed by Fuad Ahammed</a></div>
            <div class="social-links">
                <a href="#" aria-label="Facebook" class="social-icon facebook">Facebook</a>
                <a href="#" aria-label="Twitter" class="social-icon twitter">Twitter</a>
                <a href="#" aria-label="Instagram" class="social-icon instagram">Instagram</a>
            </div>
        </div>
    </div>
</body>
</html>
