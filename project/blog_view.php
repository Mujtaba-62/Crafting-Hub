<?php
// c:\xampp\htdocs\newp\blog_view.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$blog_id = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM blogs WHERE id = ?');
$stmt->execute([$blog_id]);
$blog = $stmt->fetch();

if (!$blog) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Blog not found.</div></div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?> - Blog | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">CraftHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blogs.php">All Blogs</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <a href="<?= ($_SESSION['user_role'] === 'admin') ? 'admin_dashboard.php' : 'dashboard.php' ?>" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <div class="card shadow">
        <?php if ($blog['image']): ?>
            <img src="uploads/<?= htmlspecialchars($blog['image']) ?>" class="card-img-top" alt="Featured Image">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title mb-3"><?= htmlspecialchars($blog['title']) ?></h2>
            <p class="text-muted">By <?= htmlspecialchars($blog['author']) ?> | <?= htmlspecialchars($blog['created_at']) ?></p>
            <div class="mb-3">
                <?= nl2br(htmlspecialchars($blog['content'])) ?>
            </div>
            <?php if ($blog['link']): ?>
                <a href="<?= htmlspecialchars($blog['link']) ?>" target="_blank" class="btn btn-info">Related Link</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
