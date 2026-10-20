<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $status = isset($_GET['status']) ? $_GET['status'] : 'available';
    
    if ($status === 'all') {
        $res = mysqli_query($conn, "SELECT i.*, u.fullname 
                                    FROM items i 
                                    JOIN users u ON i.user_id = u.id 
                                    ORDER BY posted_at DESC");
    } else {
        $res = mysqli_query($conn, "SELECT i.*, u.fullname 
                                    FROM items i 
                                    JOIN users u ON i.user_id = u.id 
                                    WHERE i.status = '$status' 
                                    ORDER BY posted_at DESC");
    }
    
    $items = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = $row;
    }
    
    sendResponse(['count' => count($items), 'items' => $items]);
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
    $item_name = mysqli_real_escape_string($conn, trim($input['item_name']));
    $category = mysqli_real_escape_string($conn, trim($input['category']));
    $description = mysqli_real_escape_string($conn, trim($input['description']));
    $wanted_item = mysqli_real_escape_string($conn, trim($input['wanted_item']));
    
    if (empty($item_name) || empty($description)) {
        sendError("Item name and description are required");
    }
    
    $sql = "INSERT INTO items (user_id, item_name, category, description, wanted_item, status) 
            VALUES ('$user_id', '$item_name', '$category', '$description', '$wanted_item', 'available')";
    
    if (mysqli_query($conn, $sql)) {
        sendResponse(['message' => 'Item posted successfully', 'item_id' => mysqli_insert_id($conn)], 201);
    } else {
        sendError("Failed to post item");
    }
}
?>