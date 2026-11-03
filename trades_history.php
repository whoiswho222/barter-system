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
$fullname = $_SESSION['fullname'];

// Items YOU posted & got traded
$my_traded = mysqli_query($conn, "
    SELECT * FROM items 
    WHERE user_id = '$user_id' AND status = 'traded' 
    ORDER BY posted_at DESC
");

// Items YOU received / got from others
$others_traded = mysqli_query($conn, "
    SELECT i.*, u.fullname as owner_name 
    FROM items i 
    JOIN users u ON i.user_id = u.id 
    WHERE i.status = 'traded' AND i.user_id != '$user_id' 
    ORDER BY posted_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade History — Barter System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

       body {
    min-height: 100vh;
    background: #f0fdf4;
    display: flex;
    margin-left: 240px;

        }

        .sidebar {
            width: 240px;
            background: #ffffff;
            border-right: 2px solid #22c55e;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            height: 100vh;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 40px;
        }

        .sidebar-logo img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            margin-right: 8px;
        }

        .sidebar-logo span {
            color: #22c55e;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            color: #374151;
            text-decoration: none;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid transparent;
        }

        .nav-link.active {
            background: #dcfce7;
            border-color: #22c55e;
            color: #16a34a;
            font-weight: 500;
        }

        .sidebar-bottom {
            padding-top: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-details .name {
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
        }

        .user-details .status {
            font-size: 11px;
            color: #6b7280;
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 8px;
            border: 1px solid #ef4444;
            color: #ef4444;
            background: transparent;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 240px;
            padding: 30px 40px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 6px;
        }

        .page-header p {
            font-size: 15px;
            color: #6b7280;
        }

        .history-section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #bbf7d0;
        }

        .history-card {
            background: white;
            border: 2px solid #bbf7d0;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        .history-card:hover {
            border-color: #22c55e;
        }

        .history-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
        }

        .history-meta {
            font-size: 13px;
            color: #6b7280;
        }

        .empty-box {
            background: white;
            border: 2px dashed #bbf7d0;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-box p {
            margin-bottom: 10px;
        }

        .link-btn {
            color: #22c55e;
            text-decoration: none;
            font-weight: 500;
        }

        .link-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
   <?php include 'sidebar.php'; ?>
            <div class="sidebar-logo">
                <img src="logo.jpg" alt="Logo">
                Barter<span>System</span>
            </div>

            <nav class="nav-links">
                <a href="dashboard.php" class="nav-link">
                    <span>🏠</span> Dashboard
                </a>
                <a href="index.php" class="nav-link">
                    <span>📦</span> Browse Items
                </a>
                <a href="post_item.php" class="nav-link">
                    <span>+</span> Post Item
                </a>
                <a href="tradehistory.php" class="nav-link active">
                    <span>📋</span> Trade History
                </a>
                <a href="profile.php" class="nav-link">
                    <span>👤</span> My Profile
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($fullname, 0, 1)); ?></div>
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($fullname); ?></div>
                    <div class="status">Logged in</div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>Trade History</h1>
            <p>All items that have been successfully traded</p>
        </div>

        <!-- Items YOU Posted & Traded -->
        <div class="history-section">
            <h2 class="section-title">Items You Posted & Traded</h2>
            <?php if (mysqli_num_rows($my_traded) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($my_traded)): ?>
                <div class="history-card">
                    <h3 class="history-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="history-meta">Posted on: <?php echo date('F j, Y g:i A', strtotime($item['posted_at'])); ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-box">
                    <p>No items traded yet from your posts.</p>
                    <a href="index.php" class="link-btn">Browse available items →</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Items Traded FROM OTHERS -->
        <div class="history-section">
            <h2 class="section-title">Items Traded From Others</h2>
            <?php if (mysqli_num_rows($others_traded) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($others_traded)): ?>
                <div class="history-card">
                    <h3 class="history-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="history-meta">From: <?php echo htmlspecialchars($item['owner_name']); ?> — <?php echo date('F j, Y', strtotime($item['posted_at'])); ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-box">
                    <p>No trades made with others yet.</p>
                    <a href="index.php" class="link-btn">Start browsing items →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
