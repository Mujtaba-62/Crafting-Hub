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
$stmt = $pdo->prepare('DELETE FROM deleted_blogs WHERE id = ?');
$stmt->execute([$deleted_id]);

header('Location: admin_dashboard.php');
exit;
?>
