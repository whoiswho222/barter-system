<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        sendError("Not logged in", 401);
    }
    
    $user_id = $_SESSION['user_id'];
    
    $res = mysqli_query($conn, "SELECT c.*, u.fullname AS sender_name, i.item_name, i.user_id AS owner_id 
                                FROM comments c
                                JOIN users u ON c.user_id = u.id
                                JOIN items i ON c.item_id = i.id
                                WHERE i.user_id = '$user_id' AND c.is_reply = 0
                                ORDER BY c.created_at DESC");
    
    $offers = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $offers[] = $row;
    }
    
    sendResponse(['count' => count($offers), 'offers' => $offers]);
}

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
    $message = mysqli_real_escape_string($conn, trim($input['message']));
    $offered_item = mysqli_real_escape_string($conn, trim($input['offered_item']));
    
    if (empty($message) || empty($item_id)) {
        sendError("Item ID and message are required");
    }
    
    $sql = "INSERT INTO comments (item_id, user_id, message, offered_item, is_reply) 
            VALUES ('$item_id', '$user_id', '$message', '$offered_item', 0)";
    
    if (mysqli_query($conn, $sql)) {
        sendResponse(['message' => 'Offer sent successfully', 'comment_id' => mysqli_insert_id($conn)], 201);
    } else {
        sendError("Failed to send offer");
    }
}
?>
