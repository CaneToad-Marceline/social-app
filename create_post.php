<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = sanitize($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $image = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . 'posts/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            }
        }
    }
    
    $sql = "INSERT INTO posts (user_id, content, image) VALUES ($user_id, '$content', " . ($image ? "'$image'" : "NULL") . ")";
    
    if ($conn->query($sql)) {
        redirect('index.php');
    } else {
        echo "Error creating post";
    }
}
?>