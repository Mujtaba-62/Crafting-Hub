<?php
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

$deleted_id = (int)$_GET['id'];

// Fetch deleted event
$stmt = $pdo->prepare('SELECT * FROM deleted_events WHERE id = ?');
$stmt->execute([$deleted_id]);
$deleted_event = $stmt->fetch();

if (!$deleted_event) {
    header('Location: admin_dashboard.php');
    exit;
}

// Restore to events
$stmt = $pdo->prepare('INSERT INTO events (title, event_date, location, description, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
$stmt->execute([
    $deleted_event['title'],
    $deleted_event['event_date'],
    $deleted_event['location'],
    $deleted_event['description'],
    $deleted_event['created_by']
]);

// Remove from deleted_events
$stmt = $pdo->prepare('DELETE FROM deleted_events WHERE id = ?');
$stmt->execute([$deleted_id]);

header('Location: admin_dashboard.php');
exit;
?>
