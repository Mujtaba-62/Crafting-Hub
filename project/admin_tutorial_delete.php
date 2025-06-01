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

$tutorial_id = (int)$_GET['id'];
$admin_id = $_SESSION['user_id'];

// Fetch the tutorial to delete
$stmt = $pdo->prepare('SELECT * FROM tutorials WHERE id = ?');
$stmt->execute([$tutorial_id]);
$tutorial = $stmt->fetch();

if (!$tutorial) {
    header('Location: admin_dashboard.php');
    exit;
}

// Move to deleted_tutorials
$stmt = $pdo->prepare('INSERT INTO deleted_tutorials (tutorial_id, title, description, category, created_by, deleted_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $tutorial['id'],
    $tutorial['title'],
    $tutorial['description'],
    $tutorial['category'],
    $tutorial['created_by'],
    $admin_id,
    $tutorial['created_at'],
    $tutorial['updated_at']
]);

// Delete from tutorials
$stmt = $pdo->prepare('DELETE FROM tutorials WHERE id = ?');
$stmt->execute([$tutorial_id]);

header('Location: admin_dashboard.php');
exit;
?>
