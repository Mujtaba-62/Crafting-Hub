<?php
session_start();
require_once 'db.php';

// Fetch all blogs with user name
$stmt = $pdo->query('SELECT blogs.*, users.name as user_name FROM blogs LEFT JOIN users ON blogs.user_id = users.id ORDER BY blogs.created_at DESC');
$blogs = $stmt->fetchAll();

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 1rem; }
        .card-title { color: #6c63ff; }
        .blog-excerpt { min-height: 60px; }
        /* Ensure card-body is flex column and View button is always at bottom */
        .card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .blog-view-btn-row {
            margin-top: auto;
        }
    </style>
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
        <li class="nav-item"><a class="nav-link active" href="blogs.php">All Blogs</a></li>
        <?php if ($is_logged_in): ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar End -->
<div class="container py-5">
    <h2 class="mb-4 text-center">All Blogs</h2>
    <div class="row">
        <?php if ($blogs): foreach ($blogs as $blog): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($blog['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($blog['image']) ?>" class="card-img-top" alt="Featured Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($blog['title']) ?></h5>
                        <div class="text-muted small mb-2">
                            By <?= htmlspecialchars($blog['author'] ?: $blog['user_name']) ?> | <?= htmlspecialchars($blog['created_at']) ?>
                        </div>
                        <div class="blog-excerpt mb-2"><?= nl2br(htmlspecialchars(mb_strimwidth($blog['content'], 0, 120, '...'))) ?></div>
                        <div class="blog-view-btn-row">
                            <a href="blog_view.php?id=<?= $blog['id'] ?>" class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="alert alert-info">No blogs found.</div></div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
