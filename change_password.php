<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['admin']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            if (password_verify($current_password, $hashed_password)) {
                // Update password
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
                $update_stmt->bind_param("ss", $new_hashed, $_SESSION['admin']);
                if ($update_stmt->execute()) {
                    $success = "Password changed successfully.";
                } else {
                    $error = "Failed to update password.";
                }
                $update_stmt->close();
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "User not found.";
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
    <title>Change Password - Admin Panel</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
    <style>
        .change-password-content { padding: 2rem 0; }
        .error { color: red; }
        .success { color: green; }
        form { max-width: 400px; }
        label { display: block; margin-top: 0.5rem; }
        input { width: 100%; padding: 0.5rem; margin-top: 0.2rem; }
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

    <main class="container change-password-content">
        <h1>Change Password</h1>
        <p><a href="admin.php">Back to Admin Panel</a></p>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
    <form method="post" action="change_password.php">
        <label for="current_password">Current Password</label>
        <input type="password" name="current_password" id="current_password" required />
        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" required />
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required />
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
