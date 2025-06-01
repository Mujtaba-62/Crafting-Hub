<?php
// c:\xampp\htdocs\newp\blog_delete.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$blog_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the blog to delete
$stmt = $pdo->prepare('SELECT * FROM blogs WHERE id = ? AND user_id = ?');
$stmt->execute([$blog_id, $user_id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: dashboard.php');
    exit;
}

// Move to deleted_blogs
$stmt = $pdo->prepare('INSERT INTO deleted_blogs (blog_id, title, content, link, author, image, user_id, deleted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $blog['id'], $blog['title'], $blog['content'], $blog['link'], $blog['author'], $blog['image'], $blog['user_id'], $user_id
]);

// Delete from blogs
$stmt = $pdo->prepare('DELETE FROM blogs WHERE id = ?');
$stmt->execute([$blog_id]);

header('Location: dashboard.php');
exit;
?>
