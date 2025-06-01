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

// Fetch deleted blog
$stmt = $pdo->prepare('SELECT * FROM deleted_blogs WHERE id = ?');
$stmt->execute([$deleted_id]);
$deleted_blog = $stmt->fetch();

if (!$deleted_blog) {
    header('Location: admin_dashboard.php');
    exit;
}

// Restore to blogs
$stmt = $pdo->prepare('INSERT INTO blogs (title, content, link, author, image, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
$stmt->execute([
    $deleted_blog['title'],
    $deleted_blog['content'],
    $deleted_blog['link'],
    $deleted_blog['author'],
    $deleted_blog['image'],
    $deleted_blog['user_id']
]);

// Remove from deleted_blogs
$stmt = $pdo->prepare('DELETE FROM deleted_blogs WHERE id = ?');
$stmt->execute([$deleted_id]);

header('Location: admin_dashboard.php');
exit;
?>
