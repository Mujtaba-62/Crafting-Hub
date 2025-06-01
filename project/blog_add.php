<?php
// c:\xampp\htdocs\newp\blog_add.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$title = $content = $link = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $link = trim($_POST['link']);
    $author = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];
    $image = '';

    if (empty($title) || empty($content)) {
        $errors[] = 'Title and content are required.';
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($img_ext, $allowed)) {
            $errors[] = 'Only JPG, JPEG, PNG, GIF images allowed.';
        } else {
            $new_name = uniqid('blog_', true) . '.' . $img_ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir);
            $target = $upload_dir . $new_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $new_name;
            } else {
                $errors[] = 'Image upload failed.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO blogs (title, content, link, author, image, user_id) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt->execute([$title, $content, $link, $author, $image, $user_id])) {
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Failed to add blog.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog - CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pro-style.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="mb-4">Add New Blog</h3>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($content) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link (optional)</label>
                            <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($link) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Featured Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Blog</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
