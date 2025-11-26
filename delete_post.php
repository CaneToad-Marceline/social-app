<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    

    $check_sql = "SELECT * FROM posts WHERE id = $post_id";
    $post = $conn->query($check_sql)->fetch_assoc();
    
    if ($post && ($post['user_id'] == $_SESSION['user_id'] || isAdmin())) {
        if ($post['image']) {
            @unlink(UPLOAD_DIR . 'posts/' . $post['image']);
        }
        
        $delete_sql = "DELETE FROM posts WHERE id = $post_id";
        $conn->query($delete_sql);
    }
}


$redirect_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header("Location: $redirect_page");
exit();
?>