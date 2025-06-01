<?php
// c:\xampp\htdocs\newp\contact.php
session_start();
require_once 'db.php';

$email = $subject = $message = '';
$errors = [];
$success = false;
$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Fetch user email
    $stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $email = $user['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    if (!$user_id) {
        $errors[] = 'You must be logged in to contact us.';
    }
    if (empty($subject) || empty($message)) {
        $errors[] = 'Subject and message are required.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO contacts (user_id, email, subject, message) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$user_id, $email, $subject, $message])) {
            $success = true;
            $subject = $message = '';
        } else {
            $errors[] = 'Failed to send message.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 1rem; }
        .btn-primary { background: #6c63ff; border: none; }
        .btn-primary:hover { background: #5548c8; }
        .form-label { font-weight: 500; }
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
        <?php if (isset($_SESSION['user_id'])): ?>
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
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Contact Us</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success">Your message has been sent!</div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Your Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($message) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
                <div class="mt-3 text-muted small">* Only registered users can contact us.</div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
