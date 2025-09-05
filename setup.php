<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prabon_db";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    color VARCHAR(100),
    details TEXT,
    price DECIMAL(10,2),
    unit VARCHAR(50)
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating products table: " . $conn->error);
}

// Create admin_users table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating admin_users table: " . $conn->error);
}

// Create users table for customers
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating users table: " . $conn->error);
}

// Create cart table
$sql = "CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating cart table: " . $conn->error);
}

// Insert default admin user if not exists
$default_username = 'admin';
$default_password = password_hash('admin', PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $default_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    $insert_stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $default_username, $default_password);
    if (!$insert_stmt->execute()) {
        die("Error inserting default admin user: " . $conn->error);
    }
    $insert_stmt->close();
} else {
    $stmt->close();
}

echo "Setup completed successfully.";
$conn->close();
?>
