<?php
// c:\xampp\htdocs\newp\dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch user's blogs
$stmt = $pdo->prepare('SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$blogs = $stmt->fetchAll();

// Fetch user's tutorials
$stmt = $pdo->prepare('SELECT * FROM tutorials WHERE created_by = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$tutorials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
</head>
<body>
<!-- Navbar Start -->
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
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
        <?php if ($_SESSION['user_role'] !== 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar End -->
<div class="container mt-4">
    <h1 class="text-center fade-in display-4 mb-5" style="font-weight:700;letter-spacing:1px;animation-delay:0.1s;animation-fill-mode:both;">
        Welcome, <?= htmlspecialchars($user_name) ?>!
    </h1>
    <hr>
    <h4>Your Blogs</h4>
    <a href="blog_add.php" class="btn btn-success mb-3">Add New Blog</a>
    <?php if ($blogs): ?>
        <div class="row">
        <?php foreach ($blogs as $blog): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <?php if ($blog['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($blog['image']) ?>" class="card-img-top" alt="Featured Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($blog['title']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars(mb_strimwidth($blog['content'], 0, 120, '...'))) ?></p>
                        <a href="blog_view.php?id=<?= $blog['id'] ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="edit_blog.php?id=<?= $blog['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="blog_delete.php?id=<?= $blog['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have not posted any blogs yet.</p>
    <?php endif; ?>

    <hr>
    <h4>Your Tutorials</h4>
    <a href="tutorials_add.php" class="btn btn-success mb-3">Add New Tutorial</a>
    <?php if ($tutorials): ?>
        <div class="row">
        <?php foreach ($tutorials as $tutorial): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($tutorial['title']) ?></h5>
                        <span class="badge bg-primary mb-2"><?= htmlspecialchars($tutorial['category']) ?></span>
                        <p class="card-text"><?= nl2br(htmlspecialchars(mb_strimwidth($tutorial['description'], 0, 120, '...'))) ?></p>
                        <a href="tutorial_view.php?id=<?= $tutorial['id'] ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="edit_tutorial.php?id=<?= $tutorial['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="tutorial_delete.php?id=<?= $tutorial['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this tutorial?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have not posted any tutorials yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
