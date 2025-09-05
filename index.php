<?php
session_start();
include 'db.php';

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>BariGhor.com - Tiles and Electronics Store</title>
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
                    <li><a href="#featured">Featured</a></li>
                    <li><a href="#tiles">Tiles</a></li>
                    <li><a href="#electronics">Electronics</a></li>
                    <li><a href="#categories">Categories</a></li>
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

    <section class="hero-section">
        <div class="container hero-content">
            <h1>আপনার বাড়ির জন্য সেরা পণ্যসমূহ</h1>
            <p>টাইলস এবং ইলেকট্রনিক্সের উন্নত সংগ্রহ এখন এক জায়গায়</p>
            <a href="#featured" class="btn-primary">Visit Products</a>
        </div>
    </section>

    <main class="container main-content">
        <section id="featured" class="product-section">
            <h2>Featured Products</h2>
            <div class="products">
            <?php
                $featured = array_slice($products, 0, 6);
            ?>
            <?php if (!empty($featured)): ?>
                    <?php foreach ($featured as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'https://via.placeholder.com/220x160?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price"><?php echo htmlspecialchars($product['price']); ?> <?php echo htmlspecialchars($product['unit']); ?></p>
                            <p>Color: <?php echo htmlspecialchars($product['color']); ?></p>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <button type="submit" class="btn-add-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured products available.</p>
            <?php endif; ?>
            </div>
        </section>

        <section id="tiles" class="product-section">
            <h2>টাইলস</h2>
            <div class="products">
            <?php
                $tiles = array_filter($products, fn($p) => $p['category'] === 'tiles');
            ?>
            <?php if (!empty($tiles)): ?>
                <?php foreach ($tiles as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price"><?php echo htmlspecialchars($product['price']); ?> <?php echo htmlspecialchars($product['unit']); ?></p>
                            <p>Color: <?php echo htmlspecialchars($product['color']); ?></p>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <button type="submit" class="btn-add-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tiles available.</p>
            <?php endif; ?>
            </div>
        </section>

        <section id="electronics" class="product-section">
            <h2>Electronics</h2>
            <div class="products">
            <?php
                $electronics = array_filter($products, fn($p) => $p['category'] === 'electronics');
            ?>
            <?php if (!empty($electronics)): ?>
                <?php foreach ($electronics as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price"><?php echo htmlspecialchars($product['price']); ?> <?php echo htmlspecialchars($product['unit']); ?></p>
                            <p>Color: <?php echo htmlspecialchars($product['color']); ?></p>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <button type="submit" class="btn-add-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No electronics available.</p>
            <?php endif; ?>
            </div>
        </section>

        <section id="categories" class="categories-section">
            <h2>Categories</h2>
            <div class="categories">
                <a href="#tiles" class="category-card">Tiles</a>
                <a href="#electronics" class="category-card">Electronics</a>
            </div>
        </section>

        <section id="about" class="about-section">
            <h2>About Us</h2>
            <p>আমাদের বাড়িঘর.কম একটি বিশ্বস্ত ই-কমার্স স্টোর যা আপনার প্রয়োজনীয় টাইলস এবং ইলেকট্রনিক্স পণ্য সরবরাহ করে। আমাদের লক্ষ্য গ্রাহকদের সর্বোত্তম পণ্য এবং সেবা প্রদান করা।</p>
        </section>
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

</body>
</html>
