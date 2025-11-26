<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$success = '';
$error = '';


$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$user = $conn->query($sql)->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = sanitize($_POST['bio']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . 'profiles/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                if ($user['profile_picture'] != 'default-avatar.png') {
                    @unlink(UPLOAD_DIR . 'profiles/' . $user['profile_picture']);
                }
                
                $update_pic = "UPDATE users SET profile_picture = '$new_filename' WHERE id = $user_id";
                $conn->query($update_pic);
                $_SESSION['profile_picture'] = $new_filename;
            }
        }
    }
    

    $update_bio = "UPDATE users SET bio = '$bio' WHERE id = $user_id";
    $conn->query($update_bio);
    
    if (!empty($current_password) && !empty($new_password)) {
        if (password_verify($current_password, $user['password'])) {
            if (strlen($new_password) >= 6) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
                $conn->query($update_pass);
                $success = 'Password updated successfully';
            } else {
                $error = 'New password must be at least 6 characters';
            }
        } else {
            $error = 'Current password is incorrect';
        }
    }
    
    if (empty($error)) {
        $success = 'Profile updated successfully';
        $user = $conn->query($sql)->fetch_assoc();
    }
}

$posts_sql = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts = $conn->query($posts_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Social App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="profile-container">
            <h1>My Profile</h1>
            
            <?php if ($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="profile-card">
                <div class="profile-header">
                    <img src="<?php echo UPLOAD_DIR . 'profiles/' . $user['profile_picture']; ?>" 
                         class="profile-avatar">
                    <div>
                        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                        <p class="email"><?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="joined">Joined: <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="profile-form">
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <hr>
                    <h3>Change Password</h3>
                    
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password">
                    </div>
                    
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <div class="user-posts">
                <h2>My Posts (<?php echo $posts->num_rows; ?>)</h2>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <div class="post">
                        <div class="post-content">
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if ($post['image']): ?>
                                <img src="<?php echo UPLOAD_DIR . 'posts/' . $post['image']; ?>" class="post-image">
                            <?php endif; ?>
                        </div>
                        <div class="post-footer">
                            <span><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                            <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                               onclick="return confirm('Delete this post?')" 
                               class="btn-delete">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>