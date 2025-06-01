<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$tutorial_id = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT tutorials.*, users.name as creator FROM tutorials LEFT JOIN users ON tutorials.created_by = users.id WHERE tutorials.id = ?');
$stmt->execute([$tutorial_id]);
$tutorial = $stmt->fetch();

if (!$tutorial) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Tutorial not found.</div></div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tutorial['title']) ?> - Tutorial | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
</head>
<body>
<div class="container mt-5">
    <a href="<?= ($_SESSION['user_role'] === 'admin') ? 'admin_dashboard.php' : 'dashboard.php' ?>" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title mb-3"><?= htmlspecialchars($tutorial['title']) ?></h2>
            <span class="badge bg-primary mb-2"><?= htmlspecialchars($tutorial['category']) ?></span>
            <div class="mb-2 text-muted small">
                By <?= htmlspecialchars($tutorial['creator']) ?> | <?= htmlspecialchars($tutorial['created_at']) ?>
            </div>
            <div class="mb-3">
                <?= nl2br(htmlspecialchars($tutorial['description'])) ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
