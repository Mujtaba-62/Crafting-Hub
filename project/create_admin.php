<?php
// c:\xampp\htdocs\newp\create_admin.php
// Run this ONCE to create or update an admin user, then delete this file for security!
require_once 'db.php';

$name = 'Admin';
$email = 'admin@example.com';
$password = 'admin123'; // Change this to your desired password
$role = 'admin';

$hash = password_hash($password, PASSWORD_DEFAULT);

// Check if admin exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    // Update existing admin
    $stmt = $pdo->prepare('UPDATE users SET name=?, password=?, role=? WHERE email=?');
    $stmt->execute([$name, $hash, $role, $email]);
    echo "Admin user updated!<br>Email: $email<br>Password: $password";
} else {
    // Insert new admin
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    if ($stmt->execute([$name, $email, $hash, $role])) {
        echo "Admin user created!<br>Email: $email<br>Password: $password";
    } else {
        echo "Failed to create admin user.";
    }
}
?>
