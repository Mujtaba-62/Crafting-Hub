<?php
// c:\xampp\htdocs\newp\admin_event_delete.php
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

$event_id = (int)$_GET['id'];
$admin_id = $_SESSION['user_id'];

// Fetch the event to delete
$stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: admin_dashboard.php');
    exit;
}

// Move to deleted_events
$stmt = $pdo->prepare('INSERT INTO deleted_events (event_id, title, event_date, location, description, created_by, deleted_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $event['id'], $event['title'], $event['event_date'], $event['location'], $event['description'], $event['created_by'], $admin_id
]);

// Delete from events
$stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
$stmt->execute([$event_id]);

header('Location: admin_dashboard.php');
exit;
?>
