<?php
// c:\xampp\htdocs\newp\admin_event_add.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$title = $event_date = $location = $description = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $created_by = $_SESSION['user_id'];

    // Validate fields
    if (empty($title) || empty($event_date)) {
        $errors[] = 'Title and event date are required.';
    }
    // Validate event date: at least 30 minutes ahead
    $now = new DateTime('now', new DateTimeZone('Asia/Karachi'));
    $event_dt = DateTime::createFromFormat('Y-m-d\TH:i', $event_date, new DateTimeZone('Asia/Karachi'));
    if (!$event_dt) {
        $errors[] = 'Invalid date/time format.';
    } else {
        $diff = $event_dt->getTimestamp() - $now->getTimestamp();
        if ($diff < 1800) {
            $errors[] = 'Event time must be at least 30 minutes ahead of current time.';
        }
    }
    if (empty($errors) && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = $pdo->prepare('INSERT INTO events (title, event_date, location, description, created_by) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$title, $event_dt->format('Y-m-d H:i:s'), $location, $description, $created_by])) {
            $success = true;
            $title = $event_date = $location = $description = '';
        } else {
            $errors[] = 'Failed to add event.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Admin | CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
    <style>
        /* Old custom styles removed; now handled by pro-style.css */
    </style>
    <script>
    function showConfirmModal(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        document.getElementById('eventForm').reportValidity();
        if (document.getElementById('eventForm').checkValidity()) {
            modal.show();
        }
    }
    function submitConfirmed() {
        document.getElementById('confirmInput').value = 'yes';
        document.getElementById('eventForm').submit();
    }
    </script>
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
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar End -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Add New Event</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success">Event added successfully!</div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
                    </div>
                <?php endif; ?>
                <form id="eventForm" method="post" onsubmit="showConfirmModal(event)">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Date & Time</label>
                        <input type="datetime-local" name="event_date" class="form-control" value="<?= htmlspecialchars($event_date) ?>" required>
                        <div class="form-text">Must be at least 30 minutes ahead of current time.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                    <input type="hidden" name="confirm" id="confirmInput" value="">
                    <button type="submit" class="btn btn-primary">Add Event</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm Event Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to set this event time?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitConfirmed()">Yes, Confirm</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
