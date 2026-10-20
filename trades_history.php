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

// Traded items you posted
$my_traded = mysqli_query($conn, "
    SELECT i.*, 
           (SELECT COUNT(*) FROM comments WHERE item_id = i.id AND is_reply = 0) AS offer_count,
           (SELECT message FROM comments WHERE item_id = i.id AND is_reply = 1 ORDER BY created_at DESC LIMIT 1) AS reply_msg
    FROM items i 
    WHERE i.user_id = '$user_id' AND i.status = 'traded' 
    ORDER BY posted_at DESC
");

// Items you received offers from & traded
$others_traded = mysqli_query($conn, "
    SELECT DISTINCT i.*, u.fullname AS owner_name, c.offered_item, c.message AS offer_msg
    FROM items i
    JOIN comments c ON i.id = c.item_id
    JOIN users u ON i.user_id = u.id
    WHERE i.status = 'traded' AND c.user_id = '$user_id'
    ORDER BY c.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Trades History — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .text-theme { color: #198754; }
        .btn-success { background-color: #198754; border-color: #198754; }
        .card-traded { border-left: 4px solid #198754; }
        .card-received { border-left: 4px solid #0d6efd; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Barter System</a>
            <div>
                <a href="profile.php" class="btn btn-light btn-sm me-2">👤 My Profile</a>
                <a href="dashboard.php" class="btn btn-light btn-sm">Back</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-theme mb-4"> My Trades History</h3>

        <!-- ITEMS YOU TRADED AWAY -->
        <h5 class="mb-3"> Items You Traded Away</h5>
        <?php if (mysqli_num_rows($my_traded) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($my_traded)): ?>
                <div class="card mb-3 card-traded">
                    <div class="card-body">
                        <h5 class="card-title text-theme mb-1"><?= htmlspecialchars($item['item_name']) ?></h5>
                        <span class="badge bg-success mb-2">✅ Traded</span>
                        <p class="mb-1"><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
                        <p class="mb-1"><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                        <p class="mb-1"><strong>Wanted:</strong> <?= htmlspecialchars($item['wanted_item'] ?: 'Any offer') ?></p>
                        <p class="mb-1 small text-muted"> Posted: <?= date('M d, Y h:i A', strtotime($item['posted_at'])) ?></p>
                        <p class="mb-0 small text-muted">💬 Offers received: <?= $item['offer_count'] ?></p>
                        <?php if (!empty($item['reply_msg'])): ?>
                            <p class="mt-2 mb-0 small"><strong>Your reply:</strong> <?= htmlspecialchars($item['reply_msg']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info mb-4">No items have been traded yet.</div>
        <?php endif; ?>

        <!-- ITEMS YOU RECEIVED / TRADED WITH OTHERS -->
        <h5 class="mb-3 mt-5"> Items You Traded For</h5>
        <?php if (mysqli_num_rows($others_traded) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($others_traded)): ?>
                <div class="card mb-3 card-received">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-1"><?= htmlspecialchars($item['item_name']) ?></h5>
                        <span class="badge bg-primary mb-2">✅ Completed</span>
                        <p class="mb-1"><strong>From:</strong> <?= htmlspecialchars($item['owner_name']) ?></p>
                        <p class="mb-1"><strong>Your offer:</strong> <?= htmlspecialchars($item['offered_item'] ?: 'See message') ?></p>
                        <p class="mb-1"><strong>Your message:</strong> <?= htmlspecialchars($item['offer_msg']) ?></p>
                        <p class="mb-0 small text-muted"> Item posted: <?= date('M d, Y h:i A', strtotime($item['posted_at'])) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">You haven't traded for any items yet.</div>
        <?php endif; ?>
    </div>
</body>
</html>