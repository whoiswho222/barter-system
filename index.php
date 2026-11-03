<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

$items = mysqli_query($conn, "
    SELECT i.*, u.fullname 
    FROM items i JOIN users u ON i.user_id = u.id 
    WHERE i.status = 'available' ORDER BY i.posted_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Items — Barter System</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body {
    min-height: 100vh;
    background: #f0fdf4;
    display: flex;
    margin-left: 240px;
}

        .sidebar{width:240px;background:#fff;border-right:2px solid #22c55e;padding:30px 20px;display:flex;flex-direction:column;justify-content:space-between;position:fixed;height:100vh;}
        .sidebar-logo{display:flex;align-items:center;font-size:24px;font-weight:700;color:#111827;margin-bottom:40px;}
        .sidebar-logo img{width:32px;height:32px;object-fit:contain;margin-right:8px;}
        .sidebar-logo span{color:#22c55e;}
        .nav-links{display:flex;flex-direction:column;gap:8px;}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 14px;color:#374151;text-decoration:none;font-size:15px;border-radius:6px;border:1px solid transparent;}
        .nav-link.active{background:#dcfce7;border-color:#22c55e;color:#16a34a;font-weight:500;}
        .sidebar-bottom{padding-top:30px;}
        .user-info{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .user-avatar{width:32px;height:32px;background:#22c55e;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:14px;}
        .user-details .name{font-size:14px;font-weight:500;color:#1f2937;}
        .user-details .status{font-size:11px;color:#6b7280;}
        .logout-btn{display:block;width:100%;padding:8px;border:1px solid #ef4444;color:#ef4444;background:transparent;border-radius:6px;text-decoration:none;font-size:13px;text-align:center;}
        .main-content{flex:1;margin-left:240px;padding:30px 40px;}
        .page-header{margin-bottom:30px;}
        .page-header h1{font-size:28px;color:#111827;margin-bottom:6px;}
        .page-header p{font-size:15px;color:#6b7280;}
        .items-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;}
        .item-card{background:#fff;border:2px solid #bbf7d0;border-radius:12px;overflow:hidden;transition:all .2s;}
        .item-card:hover{border-color:#22c55e;}
        .item-image{width:100%;height:180px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:40px;color:#22c55e;}
        .item-image img{width:100%;height:100%;object-fit:cover;}
        .item-body{padding:18px;}
        .item-title{font-size:18px;font-weight:600;color:#111827;margin-bottom:8px;}
        .item-desc{font-size:14px;color:#6b7280;margin-bottom:12px;}
        .item-owner{font-size:13px;color:#9ca3af;margin-bottom:14px;}
        .view-btn{display:block;width:100%;padding:10px;border:2px solid #22c55e;background:transparent;color:#22c55e;border-radius:6px;text-decoration:none;font-weight:500;text-align:center;transition:all .2s;}
        .view-btn:hover{background:#22c55e;color:#fff;}
        .empty-state{text-align:center;padding:60px 20px;color:#9ca3af;}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
            <div class="sidebar-logo">
                <img src="logo.jpg" alt="Logo">
                Barter<span>System</span>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-link"><span>🏠</span> Dashboard</a>
                <a href="index.php" class="nav-link active"><span>📦</span> Browse Items</a>
                <a href="post_item.php" class="nav-link"><span>+</span> Post Item</a>
                <a href="tradehistory.php" class="nav-link"><span>📋</span> Trade History</a>
                <a href="profile.php" class="nav-link"><span>👤</span> My Profile</a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['fullname'],0,1)); ?></div>
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
                    <div class="status">Logged in</div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>
    <div class="main-content">
        <div class="page-header">
            <h1>Browse Items</h1>
            <p>Find items available for trade in your community</p>
        </div>
        <?php if(mysqli_num_rows($items)>0): ?>
        <div class="items-grid">
            <?php while($item=mysqli_fetch_assoc($items)): ?>
            <div class="item-card">
                <div class="item-image">
                    <?php if(!empty($item['image'])): ?><img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt=""><?php else: ?>📦<?php endif; ?>
                </div>
                <div class="item-body">
                    <h3 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="item-desc"><?php echo htmlspecialchars(substr($item['description'],0,60)); ?>...</p>
                    <p class="item-owner">Posted by <?php echo htmlspecialchars($item['fullname']); ?></p>
                    <a href="item.php?id=<?php echo $item['id']; ?>" class="view-btn">View & Make Offer</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h3>No items available yet</h3>
            <p>Be the first to post something to trade!</p><br>
            <a href="post_item.php" class="view-btn" style="display:inline-block;width:auto;padding:10px 24px;">Post an Item</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
