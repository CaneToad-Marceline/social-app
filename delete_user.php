<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    if ($user_id != $_SESSION['user_id']) {
        $user = $conn->query("SELECT profile_picture FROM users WHERE id = $user_id")->fetch_assoc();
        
        if ($user && $user['profile_picture'] != 'default-avatar.png') {
            @unlink(UPLOAD_DIR . 'profiles/' . $user['profile_picture']);
        }
        
        $conn->query("DELETE FROM users WHERE id = $user_id");
    }
}

redirect('admin.php');
?>