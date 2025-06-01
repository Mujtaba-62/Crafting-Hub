<?php
// c:\xampp\htdocs\newp\admin_dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Auto-delete events that are more than 3 hours in the past
$now = new DateTime('now', new DateTimeZone('Asia/Karachi'));
$stmt = $pdo->prepare('SELECT * FROM events WHERE event_date < ?');
$threshold = $now->modify('-3 hours')->format('Y-m-d H:i:s');
$stmt->execute([$threshold]);
$expired_events = $stmt->fetchAll();
foreach ($expired_events as $event) {
    // Move to deleted_events before deleting
    $pdo->prepare('INSERT INTO deleted_events (event_id, title, event_date, location, description, created_by, deleted_by) VALUES (?, ?, ?, ?, ?, ?, ?)
')
        ->execute([
            $event['id'],
            $event['title'],
            $event['event_date'],
            $event['location'],
            $event['description'],
            $event['created_by'],
            null // deleted_by is null since it's auto-deleted
        ]);
    $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$event['id']]);
}

// Fetch all blogs
$stmt = $pdo->query('SELECT blogs.*, users.name as user_name FROM blogs LEFT JOIN users ON blogs.user_id = users.id ORDER BY blogs.created_at DESC');
$blogs = $stmt->fetchAll();
// Fetch all events
$stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC');
$events = $stmt->fetchAll();
// Fetch deleted blogs
$stmt = $pdo->query('SELECT deleted_blogs.*, users.name as user_name FROM deleted_blogs LEFT JOIN users ON deleted_blogs.user_id = users.id ORDER BY deleted_blogs.deleted_at DESC');
$deleted_blogs = $stmt->fetchAll();
// Fetch deleted events
$stmt = $pdo->query('SELECT * FROM deleted_events ORDER BY deleted_at DESC');
$deleted_events = $stmt->fetchAll();
// Fetch all tutorials
$stmt = $pdo->query('SELECT tutorials.*, users.name as creator FROM tutorials LEFT JOIN users ON tutorials.created_by = users.id ORDER BY tutorials.created_at DESC');
$tutorials = $stmt->fetchAll();
// Fetch deleted tutorials
$stmt = $pdo->query('SELECT deleted_tutorials.*, users.name as creator FROM deleted_tutorials LEFT JOIN users ON deleted_tutorials.created_by = users.id ORDER BY deleted_tutorials.deleted_at DESC');
$deleted_tutorials = $stmt->fetchAll();
// Fetch all contacts/messages
$stmt = $pdo->query('SELECT contacts.*, users.name as user_name FROM contacts LEFT JOIN users ON contacts.user_id = users.id ORDER BY contacts.created_at DESC');
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CraftHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="tutorials.php">Tutorials</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
      <form class="d-flex ms-3" method="get" action="admin_dashboard.php">
        <input class="form-control me-2" type="search" name="q" placeholder="Search blogs, events, tutorials..." aria-label="Search" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
<!-- Navbar End -->
<?php
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
    // Search blogs
    $stmt = $pdo->prepare("SELECT blogs.*, users.name as user_name FROM blogs LEFT JOIN users ON blogs.user_id = users.id WHERE blogs.title LIKE ? OR blogs.content LIKE ? ORDER BY blogs.created_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $blogs = $stmt->fetchAll();

    // Search events
    $stmt = $pdo->prepare("SELECT * FROM events WHERE title LIKE ? OR description LIKE ? ORDER BY event_date ASC");
    $stmt->execute(["%$q%", "%$q%"]);
    $events = $stmt->fetchAll();

    // Search tutorials
    $stmt = $pdo->prepare("SELECT tutorials.*, users.name as creator FROM tutorials LEFT JOIN users ON tutorials.created_by = users.id WHERE tutorials.title LIKE ? OR tutorials.description LIKE ? ORDER BY tutorials.created_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $tutorials = $stmt->fetchAll();

    // Search deleted blogs
    $stmt = $pdo->prepare("SELECT deleted_blogs.*, users.name as user_name FROM deleted_blogs LEFT JOIN users ON deleted_blogs.user_id = users.id WHERE deleted_blogs.title LIKE ? OR deleted_blogs.content LIKE ? ORDER BY deleted_blogs.deleted_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $deleted_blogs = $stmt->fetchAll();

    // Search deleted events
    $stmt = $pdo->prepare("SELECT * FROM deleted_events WHERE title LIKE ? OR description LIKE ? ORDER BY deleted_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $deleted_events = $stmt->fetchAll();

    // Search deleted tutorials
    $stmt = $pdo->prepare("SELECT deleted_tutorials.*, users.name as creator FROM deleted_tutorials LEFT JOIN users ON deleted_tutorials.created_by = users.id WHERE deleted_tutorials.title LIKE ? OR deleted_tutorials.description LIKE ? ORDER BY deleted_tutorials.deleted_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $deleted_tutorials = $stmt->fetchAll();

    // Search contacts/messages
    $stmt = $pdo->prepare("SELECT contacts.*, users.name as user_name FROM contacts LEFT JOIN users ON contacts.user_id = users.id WHERE contacts.email LIKE ? OR contacts.subject LIKE ? OR contacts.message LIKE ? ORDER BY contacts.created_at DESC");
    $stmt->execute(["%$q%", "%$q%", "%$q%"]);
    $contacts = $stmt->fetchAll();
}
?>
<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="mb-4 text-center">
        <a href="admin_blog_add.php" class="btn btn-primary mx-1">Add New Blog</a>
        <a href="admin_event_add.php" class="btn btn-primary mx-1">Add New Event</a>
    </div>
    <hr>
    <h4>All Content</h4>
    <div class="mb-4">
        <h5>Blogs</h5>
        <?php if ($blogs): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>User Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td><?= htmlspecialchars($blog['title']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($blog['author']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($blog['user_name']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($blog['created_at']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($blog['updated_at']) ?: '-' ?></td>
                        <td>
                            <?php if ($blog['user_id'] == $_SESSION['user_id']): ?>
                                <a href="edit_blog.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php endif; ?>
                            <a href="blog_view.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-info">View</a>
                            <a href="admin_blog_delete.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No blogs found.</p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h5>Events</h5>
        <?php if ($events): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($event['event_date']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($event['location']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($event['description']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($event['created_at']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($event['updated_at']) ?: '-' ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="admin_event_delete.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No events found.</p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h5>Tutorials</h5>
        <?php if ($tutorials): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Creator</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tutorials as $tutorial): ?>
                    <tr>
                        <td><?= htmlspecialchars($tutorial['title']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($tutorial['creator']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($tutorial['created_at']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($tutorial['updated_at']) ?: '-' ?></td>
                        <td>
                            <?php if ($tutorial['created_by'] == $_SESSION['user_id']): ?>
                                <a href="edit_tutorial.php?id=<?= $tutorial['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php endif; ?>
                            <a href="admin_tutorial_delete.php?id=<?= $tutorial['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tutorial?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No tutorials found.</p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h5>Contact Messages</h5>
        <?php if ($contacts): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?= htmlspecialchars($contact['user_name']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($contact['email']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($contact['subject']) ?: '-' ?></td>
                        <td><?= nl2br(htmlspecialchars($contact['message'])) ?: '-' ?></td>
                        <td><?= htmlspecialchars($contact['created_at']) ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No contact messages found.</p>
        <?php endif; ?>
    </div>
    <hr>
    <h4>Deleted Content</h4>
    <div class="mb-4">
        <h5>Deleted Blogs</h5>
        <?php if ($deleted_blogs): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>User Name</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($deleted_blogs as $deleted_blog): ?>
                    <tr>
                        <td><?= htmlspecialchars($deleted_blog['title']) ?></td>
                        <td><?= htmlspecialchars($deleted_blog['author']) ?></td>
                        <td><?= isset($deleted_blog['user_name']) ? htmlspecialchars($deleted_blog['user_name']) : '' ?></td>
                        <td><?= htmlspecialchars($deleted_blog['deleted_at']) ?></td>
                        <td>
                            <a href="admin_restore_blog.php?id=<?= $deleted_blog['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Restore this blog?')">Restore</a>
                            <a href="admin_permanently_delete_blog.php?id=<?= $deleted_blog['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this blog? This cannot be undone!')">Delete Forever</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No deleted blogs found.</p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h5>Deleted Events</h5>
        <?php if ($deleted_events): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($deleted_events as $deleted_event): ?>
                    <tr>
                        <td><?= htmlspecialchars($deleted_event['title']) ?></td>
                        <td><?= htmlspecialchars($deleted_event['event_date']) ?></td>
                        <td><?= htmlspecialchars($deleted_event['deleted_at']) ?></td>
                        <td>
                            <a href="admin_restore_event.php?id=<?= $deleted_event['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Restore this event?')">Restore</a>
                            <a href="admin_permanently_delete_event.php?id=<?= $deleted_event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this event? This cannot be undone!')">Delete Forever</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No deleted events found.</p>
        <?php endif; ?>
    </div>
    <div class="mb-4">
        <h5>Deleted Tutorials</h5>
        <?php if ($deleted_tutorials): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Creator</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($deleted_tutorials as $deleted_tutorial): ?>
                    <tr>
                        <td><?= htmlspecialchars($deleted_tutorial['title']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($deleted_tutorial['category']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($deleted_tutorial['creator']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($deleted_tutorial['created_at']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($deleted_tutorial['updated_at']) ?: '-' ?></td>
                        <td><?= htmlspecialchars($deleted_tutorial['deleted_at']) ?: '-' ?></td>
                        <td>
                            <a href="admin_restore_tutorial.php?id=<?= $deleted_tutorial['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Restore this tutorial?')">Restore</a>
                            <a href="admin_permanently_delete_tutorial.php?id=<?= $deleted_tutorial['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this tutorial? This cannot be undone!')">Delete Forever</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No deleted tutorials found.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
