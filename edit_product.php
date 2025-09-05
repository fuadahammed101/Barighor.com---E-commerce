<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $color = $_POST['color'] ?? '';
    $details = $_POST['details'] ?? '';
    $price = $_POST['price'] ?? 0;
    $unit = $_POST['unit'] ?? '';
    $image_path = $product['image'];

    // Handle image upload if new image provided
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
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, image=?, color=?, details=?, price=?, unit=? WHERE id=?");
        $stmt->bind_param("ssssdsii", $name, $category, $image_path, $color, $details, $price, $unit, $id);
        if ($stmt->execute()) {
            $success = "Product updated successfully.";
            // Refresh product data
            $product['name'] = $name;
            $product['category'] = $category;
            $product['image'] = $image_path;
            $product['color'] = $color;
            $product['details'] = $details;
            $product['price'] = $price;
            $product['unit'] = $unit;
        } else {
            $error = "Failed to update product.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Product - Admin Panel</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
    <style>
        .edit-content { padding: 2rem 0; }
        .error { color: red; }
        .success { color: green; }
        form { max-width: 500px; }
        label { display: block; margin-top: 0.5rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; margin-top: 0.2rem; }
        img { max-width: 100px; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">আমাদের বাড়িঘর.কম</a>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#tiles">টাইলস</a></li>
                    <li><a href="index.php#electronics">ইলেকট্রনিক্স</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <?php if (isset($_SESSION['admin'])): ?>
                    <li><a href="admin.php">অ্যাডমিন</a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="customer_dashboard.php">ড্যাশবোর্ড (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a> | 
                            <a href="logout.php">লগআউট</a>
                        <?php else: ?>
                            <a href="login.php">লগইন</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container edit-content">
        <h1>Edit Product</h1>
        <p><a href="admin.php">Back to Admin Panel</a></p>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
    <form method="post" action="edit_product.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required />
        <label for="category">Category</label>
        <select name="category" id="category" required>
            <option value="tiles" <?php if ($product['category'] === 'tiles') echo 'selected'; ?>>Tiles</option>
            <option value="electronics" <?php if ($product['category'] === 'electronics') echo 'selected'; ?>>Electronics</option>
        </select>
        <label for="image">Product Image (leave blank to keep current)</label>
        <input type="file" name="image" id="image" accept="image/*" />
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
        <label for="color">Color / Color Code</label>
        <input type="text" name="color" id="color" value="<?php echo htmlspecialchars($product['color']); ?>" />
        <label for="details">Details</label>
        <textarea name="details" id="details"><?php echo htmlspecialchars($product['details']); ?></textarea>
        <label for="price">Price (BDT)</label>
        <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" />
        <label for="unit">Unit (e.g., BDT/sq ft)</label>
        <input type="text" name="unit" id="unit" value="<?php echo htmlspecialchars($product['unit']); ?>" />
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
