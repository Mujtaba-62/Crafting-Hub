<?php
session_start();
require_once 'db.php';

$categories = ['Knitting', 'Crochet', 'Pottery', 'Sewing', 'Woodworking', 'Jewelry'];
$selected = isset($_GET['category']) && in_array($_GET['category'], $categories) ? $_GET['category'] : '';

if ($selected) {
    $stmt = $pdo->prepare('SELECT tutorials.*, users.name as creator FROM tutorials LEFT JOIN users ON tutorials.created_by = users.id WHERE category = ? ORDER BY created_at DESC');
    $stmt->execute([$selected]);
} else {
    $stmt = $pdo->query('SELECT tutorials.*, users.name as creator FROM tutorials LEFT JOIN users ON tutorials.created_by = users.id ORDER BY created_at DESC');
}
$tutorials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorials | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 1rem; }
        .category-btn.active, .category-btn:focus { background: #6c63ff; color: #fff; }
        .category-btn { margin: 0 0.25rem 0.5rem 0; }
        .card-title { color: #6c63ff; }
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
        <li class="nav-item"><a class="nav-link" href="blogs.php">All Blogs</a></li>
        <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
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
    <h2 class="mb-4 text-center">Tutorials</h2>
    <div class="mb-4 text-center">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="tutorials_add.php" class="btn btn-success mb-2">Add Tutorial</a>
        <?php endif; ?>
        <div class="d-flex flex-wrap justify-content-center">
            <a href="tutorials.php" class="btn btn-outline-primary category-btn<?= $selected ? '' : ' active' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="tutorials.php?category=<?= urlencode($cat) ?>" class="btn btn-outline-primary category-btn<?= $selected === $cat ? ' active' : '' ?>"><?= htmlspecialchars($cat) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="row">
        <?php if ($tutorials): foreach ($tutorials as $tut): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($tut['title']) ?></h5>
                        <span class="badge bg-primary mb-2"><?= htmlspecialchars($tut['category']) ?></span>
                        <p class="card-text mb-2"><?= nl2br(htmlspecialchars(mb_strimwidth($tut['description'], 0, 120, '...'))) ?></p>
                        <div class="text-muted small mb-2">By <?= htmlspecialchars($tut['creator']) ?> | <?= htmlspecialchars($tut['created_at']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="alert alert-info">No tutorials found.</div></div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
