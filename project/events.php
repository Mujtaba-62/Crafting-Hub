<?php
session_start();
require_once 'db.php';

// Filter logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$now = new DateTime('now', new DateTimeZone('Asia/Karachi'));
$where = 'event_date >= ?';
$params = [$now->format('Y-m-d H:i:s')];

if ($filter === '2days') {
    $end = (clone $now)->modify('+2 days');
    $where .= ' AND event_date <= ?';
    $params[] = $end->format('Y-m-d H:i:s');
} elseif ($filter === 'week') {
    $end = (clone $now)->modify('+7 days');
    $where .= ' AND event_date <= ?';
    $params[] = $end->format('Y-m-d H:i:s');
} elseif ($filter === 'month') {
    $end = (clone $now)->modify('+1 month');
    $where .= ' AND event_date <= ?';
    $params[] = $end->format('Y-m-d H:i:s');
} elseif ($filter === 'year') {
    $end = (clone $now)->modify('+1 year');
    $where .= ' AND event_date <= ?';
    $params[] = $end->format('Y-m-d H:i:s');
}

$stmt = $pdo->prepare("SELECT events.*, users.name as creator FROM events LEFT JOIN users ON events.created_by = users.id WHERE $where ORDER BY event_date ASC");
$stmt->execute($params);
$events = $stmt->fetchAll();

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 1rem; }
        .filter-btn.active, .filter-btn:focus { background: #6c63ff; color: #fff; }
        .filter-btn { margin: 0 0.25rem 0.5rem 0; }
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
        <li class="nav-item"><a class="nav-link active" href="events.php">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
        <?php if ($is_logged_in): ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
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
    <h2 class="mb-4 text-center">Upcoming Events</h2>
    <div class="mb-4 text-center">
        <div class="d-flex flex-wrap justify-content-center">
            <a href="events.php" class="btn btn-outline-primary filter-btn<?= $filter === 'all' ? ' active' : '' ?>">All</a>
            <a href="events.php?filter=2days" class="btn btn-outline-primary filter-btn<?= $filter === '2days' ? ' active' : '' ?>">Within 2 Days</a>
            <a href="events.php?filter=week" class="btn btn-outline-primary filter-btn<?= $filter === 'week' ? ' active' : '' ?>">Within a Week</a>
            <a href="events.php?filter=month" class="btn btn-outline-primary filter-btn<?= $filter === 'month' ? ' active' : '' ?>">Within a Month</a>
            <a href="events.php?filter=year" class="btn btn-outline-primary filter-btn<?= $filter === 'year' ? ' active' : '' ?>">Within a Year</a>
        </div>
    </div>
    <div class="row">
        <?php if ($events): foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($event['title']) ?></h5>
                        <span class="badge bg-primary mb-2"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['event_date']))) ?></span>
                        <div class="mb-2 text-muted small">
                            <?php if ($event['location']): ?>
                                <span>Location: <?= htmlspecialchars($event['location']) ?></span><br>
                            <?php endif; ?>
                            <?php if ($event['creator']): ?>
                                <span>By <?= htmlspecialchars($event['creator']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="card-text mb-2"><?= nl2br(htmlspecialchars(mb_strimwidth($event['description'], 0, 120, '...'))) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="alert alert-info">No upcoming events found.</div></div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
