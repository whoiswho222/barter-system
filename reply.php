<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        sendError("Not logged in", 401);
    }
    
    $user_id = $_SESSION['user_id'];
    $item_id = intval($input['item_id']);
    $parent_id = intval($input['parent_id']);
    $message = mysqli_real_escape_string($conn, trim($input['message']));
    
    if (empty($message) || empty($item_id) || empty($parent_id)) {
        sendError("Item ID, parent ID and message are required");
    }
    
    $sql = "INSERT INTO comments (item_id, user_id, message, parent_id, is_reply) 
            VALUES ('$item_id', '$user_id', '$message', '$parent_id', 1)";
    
    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE items SET status = 'traded' WHERE id = '$item_id'");
        sendResponse(['message' => 'Reply sent, item marked as traded']);
    } else {
        sendError("Failed to send reply");
    }
}
?>