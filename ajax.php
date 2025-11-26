<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action == 'toggle_like') {
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    
    $check = $conn->query("SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id");
    
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id");
        $conn->query("UPDATE posts SET likes_count = likes_count - 1 WHERE id = $post_id");
        $liked = false;
    } else {
        $conn->query("INSERT INTO likes (post_id, user_id) VALUES ($post_id, $user_id)");
        $conn->query("UPDATE posts SET likes_count = likes_count + 1 WHERE id = $post_id");
        $liked = true;
    }
    
    $likes = $conn->query("SELECT likes_count FROM posts WHERE id = $post_id")->fetch_assoc()['likes_count'];
    
    echo json_encode(['success' => true, 'liked' => $liked, 'likes_count' => $likes]);
}

elseif ($action == 'add_comment') {
    $post_id = (int)$_POST['post_id'];
    $content = sanitize($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES ($post_id, $user_id, '$content')";
    
    if ($conn->query($sql)) {
        $comment_id = $conn->insert_id;
        
        $comment_sql = "SELECT c.*, u.username, u.profile_picture 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.id = $comment_id";
        $comment = $conn->query($comment_sql)->fetch_assoc();
        
        echo json_encode(['success' => true, 'comment' => $comment]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding comment']);
    }
}

elseif ($action == 'get_comments') {
    $post_id = (int)$_POST['post_id'];
    
    $sql = "SELECT c.*, u.username, u.profile_picture 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = $post_id 
            ORDER BY c.created_at ASC";
    
    $result = $conn->query($sql);
    $comments = [];
    
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    
    echo json_encode(['success' => true, 'comments' => $comments]);
}
?>