<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$deleted_id = (int)$_GET['id'];

// Fetch deleted tutorial
$stmt = $pdo->prepare('SELECT * FROM deleted_tutorials WHERE id = ?');
$stmt->execute([$deleted_id]);
$deleted_tutorial = $stmt->fetch();

if (!$deleted_tutorial) {
    header('Location: admin_dashboard.php');
    exit;
}

// Restore to tutorials
$stmt = $pdo->prepare('INSERT INTO tutorials (title, description, category, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $deleted_tutorial['title'],
    $deleted_tutorial['description'],
    $deleted_tutorial['category'],
    $deleted_tutorial['created_by'],
    $deleted_tutorial['created_at'],
    $deleted_tutorial['updated_at']
]);

// Remove from deleted_tutorials
$stmt = $pdo->prepare('DELETE FROM deleted_tutorials WHERE id = ?');
$stmt->execute([$deleted_id]);

header('Location: admin_dashboard.php');
exit;
