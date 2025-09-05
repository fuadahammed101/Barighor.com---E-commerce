<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile info
$stmt = $conn->prepare("SELECT username, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $phone_number);
$stmt->fetch();
$stmt->close();

// Handle remove from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: customer_dashboard.php");
    exit;
}

// Fetch cart items
$sql = "SELECT cart.id as cart_id, products.* FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Dashboard - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body { padding: 1rem; font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background-color: #f4f4f4; }
        a.button { padding: 0.3rem 0.6rem; background-color: #dc3545; color: white; text-decoration: none; border-radius: 3px; }
        a.button:hover { background-color: #c82333; }
        .profile-info { margin-bottom: 2rem; }
        .profile-info p { margin: 0.2rem 0; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
    <div class="profile-info">
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
    </div>
    <p><a href="logout.php">Logout</a></p>

    <h2>Your Cart</h2>
    <?php if (!empty($cart_items)): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product</th><th>Price</th><th>Color</th><th>Details</th><th>Unit</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['color']); ?></td>
                    <td><?php echo htmlspecialchars($item['details']); ?></td>
                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                    <td><a class="button" href="customer_dashboard.php?remove=<?php echo $item['cart_id']; ?>" onclick="return confirm('Remove this item from cart?');">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
