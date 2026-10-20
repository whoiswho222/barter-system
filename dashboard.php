<?php
include 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($res);

$count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM items WHERE user_id = '$user_id' AND status = 'available'");
$count_row = mysqli_fetch_assoc($count_res);
$total_items = $count_row['total'];

$offer_count_res = mysqli_query($conn, "SELECT COUNT(DISTINCT c.id) AS total 
                                          FROM comments c
                                          INNER JOIN items i ON c.item_id = i.id
                                          WHERE i.user_id = '$user_id' AND i.status = 'available' AND c.is_reply = 0");
$offer_count_row = mysqli_fetch_assoc($offer_count_res);
$total_offers = $offer_count_row['total'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_reply'])) {
    $item_id = intval($_POST['item_id']);
    $parent_id = intval($_POST['parent_id']);
    $reply_msg = trim($_POST['reply_message']);
    
    if (!empty($reply_msg)) {
        $reply_msg = mysqli_real_escape_string($conn, $reply_msg);
        $sql = "INSERT INTO comments (item_id, user_id, message, parent_id, is_reply) 
                VALUES ('$item_id', '$user_id', '$reply_msg', '$parent_id', 1)";
        mysqli_query($conn, $sql);
        
        mysqli_query($conn, "UPDATE items SET status = 'traded' WHERE id = '$item_id'");
    }
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .text-theme { color: #198754; }
        .btn-success { background-color: #198754; border-color: #198754; }
        .btn-success:hover { background-color: #157347; border-color: #146c43; }
        .stat-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
        }
        .stat-card:hover {
            border-color: #198754;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card-icon { font-size: 28px; color: #198754; }
        .stat-card-number { font-size: 28px; font-weight: 700; color: #198754; }
        .card-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }
        .card-content.open {
            max-height: 3000px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .offer-item {
            background-color: #f8f9fa;
            border-left: 3px solid #198754;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 0 6px 6px 0;
        }
        .reply-item {
            background-color: #e8f5e9;
            border-left: 3px solid #0d6efd;
            padding: 12px;
            margin: 10px 0 5px 20px;
            border-radius: 0 6px 6px 0;
        }
        .reply-form {
            margin: 10px 0 10px 20px;
            display: none;
        }
        .reply-form.show { display: block; }
        .toggle-icon { transition: transform 0.3s ease; }
        .toggle-icon.rotated { transform: rotate(180deg); }
        .text-muted-small { font-size: 13px; color: #6c757d; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">📦 Barter System</a>
            <div>
                <a href="profile.php" class="btn btn-light btn-sm me-2">👤 Profile</a>
                <a href="trades_history.php" class="btn btn-light btn-sm me-2">📜 History</a>
                <span class="navbar-text me-2">Hello, <strong><?= htmlspecialchars($user['fullname']) ?></strong></span>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-theme mb-4">Your Dashboard</h3>

        <!-- ITEMS POSTED -->
        <div class="stat-card" id="itemsCard" onclick="toggleCard('itemsContent', 'itemsIcon')">
            <div class="stat-card-header">
                <div class="d-flex align-items-center">
                    <span class="stat-card-icon me-3">📋</span>
                    <div>
                        <h5 class="mb-1">Items Posted</h5>
                        <span class="stat-card-number"><?= $total_items ?></span>
                        <span class="text-muted-small ms-1">Available only</span>
                    </div>
                </div>
                <span class="toggle-icon" id="itemsIcon">▼</span>
            </div>
            <div class="card-content" id="itemsContent">
                <?php
                $my_items = mysqli_query($conn, "SELECT * FROM items WHERE user_id = '$user_id' ORDER BY posted_at DESC");
                if (mysqli_num_rows($my_items) > 0):
                ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($my_items)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category']) ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Traded / Replied</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'available'): ?>
                                            <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <?php else: ?>
                                            <span class="text-muted-small">No longer editable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">You haven't posted any items yet.</p>
                    <a href="post_item.php" class="btn btn-success btn-sm mt-2">Post Your First Item</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- OFFERS RECEIVED -->
        <div class="stat-card" id="offersCard" onclick="toggleCard('offersContent', 'offersIcon')">
            <div class="stat-card-header">
                <div class="d-flex align-items-center">
                    <span class="stat-card-icon me-3">💬</span>
                    <div>
                        <h5 class="mb-1">Offers Received</h5>
                        <span class="stat-card-number"><?= $total_offers ?></span>
                        <span class="text-muted-small ms-1">Available items only</span>
                    </div>
                </div>
                <span class="toggle-icon" id="offersIcon">▼</span>
            </div>
            <div class="card-content" id="offersContent">
                <?php
                $my_items_offers = mysqli_query($conn, "SELECT i.*, c.*, u.fullname AS sender_name 
                                                          FROM items i
                                                          LEFT JOIN comments c ON i.id = c.item_id AND c.is_reply = 0
                                                          LEFT JOIN users u ON c.user_id = u.id
                                                          WHERE i.user_id = '$user_id' AND i.status = 'available'
                                                          ORDER BY i.posted_at DESC, c.created_at ASC");
                
                if (mysqli_num_rows($my_items_offers) > 0):
                    $current_item = null;
                    $has_offers = false;
                ?>
                    <div>
                        <?php while ($row = mysqli_fetch_assoc($my_items_offers)): ?>
                            <?php if ($row['id'] != $current_item): ?>
                                <?php if ($current_item !== null): ?></div><?php endif; ?>
                                <h6 class="text-theme mb-2 mt-3">📦 <?= htmlspecialchars($row['item_name']) ?></h6>
                                <?php $current_item = $row['id']; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['message'])): ?>
                                <?php $has_offers = true; ?>
                                <div class="offer-item">
                                    <strong class="text-dark"><?= htmlspecialchars($row['sender_name']) ?>:</strong>
                                    <p class="mb-1 mt-1"><?= htmlspecialchars($row['message']) ?></p>
                                    <?php if (!empty($row['offered_item'])): ?>
                                        <span class="badge bg-success">Offered: <?= htmlspecialchars($row['offered_item']) ?></span>
                                    <?php endif; ?>
                                    <small class="text-muted d-block mt-1">
                                        <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?>
                                    </small>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                            onclick="document.getElementById('reply-form-<?= $row['id'] ?>').classList.toggle('show')">
                                        Reply & Mark as Traded
                                    </button>
                                    
                                    <div class="reply-form" id="reply-form-<?= $row['id'] ?>">
                                        <form method="post">
                                            <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                                            <input type="hidden" name="parent_id" value="<?= $row['id'] ?>">
                                            <div class="mb-2">
                                                <textarea name="reply_message" class="form-control" rows="2" 
                                                          placeholder="Write your reply — this will mark the item as traded!" required></textarea>
                                            </div>
                                            <button type="submit" name="send_reply" class="btn btn-primary btn-sm">
                                                Send & Mark as Traded
                                            </button>
                                        </form>
                                        
                                        <?php
                                        $reply_res = mysqli_query($conn, "SELECT c.*, u.fullname 
                                                                          FROM comments c 
                                                                          JOIN users u ON c.user_id = u.id 
                                                                          WHERE c.parent_id = {$row['id']} 
                                                                          ORDER BY c.created_at ASC");
                                        while ($reply = mysqli_fetch_assoc($reply_res)):
                                        ?>
                                            <div class="reply-item">
                                                <strong class="text-primary"><?= htmlspecialchars($reply['fullname']) ?>:</strong>
                                                <p class="mb-1 mt-1"><?= htmlspecialchars($reply['message']) ?></p>
                                                <small class="text-muted">
                                                    <?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?>
                                                </small>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if (!$has_offers): ?>
                        <p class="text-muted mb-0">No offers received yet.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">No offers received yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <h5 class="text-theme mt-4 mb-3">Quick Actions</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card p-4">
                    <h5 class="card-title text-theme"> Post New Item</h5>
                    <p class="card-text">Add an item you want to trade.</p>
                    <a href="post_item.php" class="btn btn-success">Go to Post</a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card p-4">
                    <h5 class="card-title text-theme"> Browse All Items</h5>
                    <p class="card-text">See items available for barter from others.</p>
                    <a href="items.php" class="btn btn-primary">Browse Items</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCard(contentId, iconId) {
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            content.classList.toggle('open');
            icon.classList.toggle('rotated');
        }
    </script>
</body>
</html>