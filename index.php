<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$posts_sql = "SELECT p.*, u.username, u.profile_picture, 
              (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
              (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = {$_SESSION['user_id']}) as user_liked
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              ORDER BY p.created_at DESC";
$posts_result = $conn->query($posts_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Social App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="feed">
            <!--  Form Untuk postingan -->
            <div class="post-create">
                <h2>What's on your mind?</h2>
                <form action="create_post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="content" placeholder="Share something..." required></textarea>
                    <div class="post-actions">
                        <input type="file" name="image" accept="image/*" id="post-image">
                        <label for="post-image" class="btn-secondary">📷 Photo</label>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
            
            <!-- Front Page (fyp nyahh) -->
            <div class="posts">
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <div class="post" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-header">
                            <img src="<?php echo UPLOAD_DIR . 'profiles/' . $post['profile_picture']; ?>" class="avatar">
                            <div class="post-info">
                                <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                <span class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                            </div>
                            <?php if ($post['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                                <div class="post-menu">
                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                       onclick="return confirm('Delete this post?')" 
                                       class="btn-delete">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-content">
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if ($post['image']): ?>
                                <img src="<?php echo UPLOAD_DIR . 'posts/' . $post['image']; ?>" class="post-image">
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-stats">
                            <span><?php echo $post['likes_count']; ?> likes</span>
                            <span><?php echo $post['comment_count']; ?> comments</span>
                        </div>
                        
                        <div class="post-actions">
                            <button class="like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>" 
                                    data-post-id="<?php echo $post['id']; ?>">
                                ❤️ Like
                            </button>
                            <button class="comment-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                💬 Comment
                            </button>
                        </div>
                        
                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>">
                            <form class="comment-form" data-post-id="<?php echo $post['id']; ?>">
                                <input type="text" placeholder="Write a comment..." required>
                                <button type="submit">Send</button>
                            </form>
                            <div class="comments-list"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="sidebar">
            <div class="user-card">
                <img src="<?php echo UPLOAD_DIR . 'profiles/' . ($_SESSION['profile_picture'] ?? 'default-avatar.png'); ?>" class="avatar-large">
                <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <a href="profile.php" class="btn btn-secondary">View Profile</a>
            </div>
            
            <?php if (isAdmin()): ?>
                <div class="admin-card">
                    <h3>Admin Panel</h3>
                    <a href="admin.php" class="btn btn-primary">Manage Users & Posts</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>