<?php
// c:\xampp\htdocs\newp\admin_blog_delete.php
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

$blog_id = (int)$_GET['id'];
$admin_id = $_SESSION['user_id'];

// Fetch the blog to delete
$stmt = $pdo->prepare('SELECT * FROM blogs WHERE id = ?');
$stmt->execute([$blog_id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: admin_dashboard.php');
    exit;
}

// Move to deleted_blogs
$stmt = $pdo->prepare('INSERT INTO deleted_blogs (blog_id, title, content, link, author, image, user_id, deleted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $blog['id'], $blog['title'], $blog['content'], $blog['link'], $blog['author'], $blog['image'], $blog['user_id'], $admin_id
]);

// Delete from blogs
$stmt = $pdo->prepare('DELETE FROM blogs WHERE id = ?');
$stmt->execute([$blog_id]);

header('Location: admin_dashboard.php');
exit;
?>
