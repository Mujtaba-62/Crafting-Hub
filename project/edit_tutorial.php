<?php
// c:\xampp\htdocs\newp\edit_tutorial.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid tutorial ID.');
}
$tutorial_id = (int)$_GET['id'];

// Fetch tutorial
$stmt = $pdo->prepare('SELECT * FROM tutorials WHERE id = ?');
$stmt->execute([$tutorial_id]);
$tutorial = $stmt->fetch();
if (!$tutorial) {
    die('Tutorial not found.');
}

// Only admin or the tutorial creator can edit
if ($_SESSION['user_role'] !== 'admin' && $tutorial['created_by'] != $_SESSION['user_id']) {
    die('You do not have permission to edit this tutorial.');
}

$title = $tutorial['title'];
$description = $tutorial['description'];
$category = $tutorial['category'];
$errors = [];
$success = false;

// Categories for dropdown
$categories = ['Knitting', 'Crochet', 'Pottery', 'Sewing', 'Woodworking', 'Jewelry'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    if (empty($title) || empty($description) || empty($category)) {
        $errors[] = 'All fields are required.';
    }
    if (!in_array($category, $categories)) {
        $errors[] = 'Invalid category.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE tutorials SET title = ?, description = ?, category = ?, updated_at = NOW() WHERE id = ?');
        if ($stmt->execute([$title, $description, $category, $tutorial_id])) {
            $success = true;
        } else {
            $errors[] = 'Failed to update tutorial.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tutorial - Admin | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Edit Tutorial</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success">Tutorial updated successfully!</div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Tutorial</button>
                    <a href="<?= ($_SESSION['user_role'] === 'admin') ? 'admin_dashboard.php' : 'dashboard.php' ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
