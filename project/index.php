<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub - Online Crafting Instruction Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .hero {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') center/cover no-repeat;
            color: #fff;
            padding: 100px 0 80px 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.5rem;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #6c63ff;
        }
    </style>
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
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blogs.php">All Blogs</a></li>
        <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
        <?php if ($is_logged_in): ?>
            <?php if ($user_role !== 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php endif; ?>
            <?php if ($user_role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
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

<section class="hero text-center">
    <div class="container">
        <h1>Welcome to CraftHub</h1>
        <p>Your one-stop hub for crafting tutorials, blogs, and events!</p>
        <?php if ($is_logged_in): ?>
            <div class="mt-3 fs-3 fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</div>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary btn-lg mt-3">Join Now</a>
        <?php endif; ?>
    </div>
</section>

<section class="container py-5">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="feature-icon mb-3">📝</div>
                    <h5 class="card-title">Blogs with Images</h5>
                    <p class="card-text">Share your crafting journey with featured images and manage your own posts.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="feature-icon mb-3">🎉</div>
                    <h5 class="card-title">Events & Workshops</h5>
                    <p class="card-text">Stay updated with upcoming crafting events. Admins can add or remove events easily.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="feature-icon mb-3">📚</div>
                    <h5 class="card-title">Tutorials by Category</h5>
                    <p class="card-text">Explore tutorials by category: Knitting, Crochet, Pottery, Sewing, Woodworking, Jewelry.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white text-center py-3">
    &copy; 2025 CraftHub. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>