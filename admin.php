<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    // Handle login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch admin user from DB
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin'] = $username;
                header("Location: admin.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
} else {
    // Handle logout
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_destroy();
        header("Location: admin.php");
        exit;
    }

    // Handle add product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $color = $_POST['color'] ?? '';
        $details = $_POST['details'] ?? '';
        $price = $_POST['price'] ?? 0;
        $unit = $_POST['unit'] ?? '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $tmp_name = $_FILES['image']['tmp_name'];
            $filename = basename($_FILES['image']['name']);
            $target_file = $upload_dir . time() . '_' . $filename;
            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_path = $target_file;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Image is required.";
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, image, color, details, price, unit) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssis", $name, $category, $image_path, $color, $details, $price, $unit);
            if ($stmt->execute()) {
                $success = "Product added successfully.";
            } else {
                $error = "Failed to add product.";
            }
            $stmt->close();
        }
    }

    // Fetch products for listing
    $products = [];
    $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
    <style>
        .admin-content { padding: 2rem 0; }
        .error { color: red; }
        .success { color: green; }
        form { margin-bottom: 2rem; }
        label { display: block; margin-top: 0.5rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; margin-top: 0.2rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background-color: #f4f4f4; }
        a.button { padding: 0.3rem 0.6rem; background-color: #007bff; color: white; text-decoration: none; border-radius: 3px; }
        a.button:hover { background-color: #0056b3; }
    </style>
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
                    <li><a href="index.php#about">About</a></li>
                    <?php if (isset($_SESSION['admin'])): ?>
                    <li><a href="admin.php">Admn</a></li>
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

    <main class="container admin-content">
<?php if (!isset($_SESSION['admin'])): ?>
    <h1>Admin Login</h1>
    <?php if (isset($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form method="post" action="admin.php">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required />
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required />
        <button type="submit">Login</button>
    </form>
<?php else: ?>
    <h1>Admin Panel</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?> | <a href="admin.php?action=logout">Logout</a> | <a href="change_password.php">Change Password</a></p>

    <?php if (isset($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if (isset($success)): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

    <h2>Add New Product</h2>
    <form method="post" action="admin.php" enctype="multipart/form-data">
        <input type="hidden" name="add_product" value="1" />
        <label for="name">Product Name</label>
        <input type="text" name="name" id="name" required />
        <label for="category">Category</label>
        <select name="category" id="category" required>
            <option value="tiles">Tiles</option>
            <option value="electronics">Electronics</option>
        </select>
        <label for="image">Product Image (required)</label>
        <input type="file" name="image" id="image" accept="image/*" required />
        <label for="color">Color / Color Code</label>
        <input type="text" name="color" id="color" />
        <label for="details">Details</label>
        <textarea name="details" id="details"></textarea>
        <label for="price">Price (BDT)</label>
        <input type="number" step="0.01" name="price" id="price" />
        <label for="unit">Unit (e.g., BDT/sq ft)</label>
        <input type="text" name="unit" id="unit" />
        <button type="submit">Add Product</button>
    </form>

    <h2>Existing Products</h2>
    <?php if (!empty($products)): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Category</th><th>Image</th><th>Color</th><th>Details</th><th>Price</th><th>Unit</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['id']); ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="max-width: 50px;"/></td>
                    <td><?php echo htmlspecialchars($p['color']); ?></td>
                    <td><?php echo htmlspecialchars($p['details']); ?></td>
                    <td><?php echo htmlspecialchars($p['price']); ?></td>
                    <td><?php echo htmlspecialchars($p['unit']); ?></td>
                    <td>
                    <a class="button" href="edit_product.php?id=<?php echo $p['id']; ?>">Edit</a>
                    <a class="button" href="delete_product.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
<?php endif; ?>
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
