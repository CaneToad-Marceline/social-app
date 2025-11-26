<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

if (isset($_GET['export'])) {
    $filename = 'posts_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Post ID', 'Username', 'Content', 'Likes', 'Comments', 'Created At']);
    
    $export_sql = "SELECT p.id, u.username, p.content, p.likes_count, 
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
                   p.created_at
                   FROM posts p 
                   JOIN users u ON p.user_id = u.id 
                   ORDER BY p.created_at DESC";
    $export_result = $conn->query($export_sql);
    
    while ($row = $export_result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['username'],
            $row['content'],
            $row['likes_count'],
            $row['comment_count'],
            $row['created_at']
        ]);
    }
    
    fclose($output);
    exit();
}

$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];

$users_sql = "SELECT u.*, 
              (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count,
              (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comment_count
              FROM users u 
              ORDER BY u.created_at DESC";
$users = $conn->query($users_sql);

$posts_sql = "SELECT p.*, u.username, 
              (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              ORDER BY p.created_at DESC";
$posts = $conn->query($posts_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Social App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="admin-panel">
            <h1>Admin Dashboard</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Posts</h3>
                    <p class="stat-number"><?php echo $total_posts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Comments</h3>
                    <p class="stat-number"><?php echo $total_comments; ?></p>
                </div>
            </div>
            
            <div class="admin-section">
                <div class="section-header">
                    <h2>All Posts</h2>
                    <a href="?export=1" class="btn btn-primary">📥 Export Posts to CSV</a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Content</th>
                            <th>Likes</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = $posts->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td>
                                <td><?php echo htmlspecialchars($post['username']); ?></td>
                                <td class="content-cell"><?php echo substr(htmlspecialchars($post['content']), 0, 50) . '...'; ?></td>
                                <td><?php echo $post['likes_count']; ?></td>
                                <td><?php echo $post['comment_count']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                       onclick="return confirm('Delete this post?')" 
                                       class="btn-small btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="admin-section">
                <h2>All Users</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Posts</th>
                            <th>Comments</th>
                            <th>Admin</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['post_count']; ?></td>
                                <td><?php echo $user['comment_count']; ?></td>
                                <td><?php echo $user['is_admin'] ? '✓' : ''; ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                           onclick="return confirm('Delete this user and all their content?')" 
                                           class="btn-small btn-danger">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>