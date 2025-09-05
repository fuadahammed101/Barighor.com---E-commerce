<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^01\d{9}$/', $phone_number)) {
        $error = "Phone number must be 11 digits starting with 01.";
    } else {
        // Check if phone number exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmt->bind_param("s", $phone_number);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Phone number already exists.";
        } else {
            $stmt->close();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, phone_number, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $phone_number, $hashed_password);
            if ($insert_stmt->execute()) {
                $success = "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $insert_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - E-Commerce Store</title>
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
                    <li><a href="index.php#about">About</a></li>
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
        <h1>Register</h1>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
<form method="post" action="register.php">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" required />
    <label for="phone_number">Phone Number</label>
    <input type="text" name="phone_number" id="phone_number" pattern="^01\d{9}$" title="Phone number must be 11 digits starting with 01" required />
    <label for="password">Password</label>
    <input type="password" name="password" id="password" required />
    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" required />
    <button type="submit">Register</button>
</form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
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
