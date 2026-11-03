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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barter System — Dashboard</title>
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
            gap: 6px;
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
            transition: all 0.15s;
        }

        .nav-link.active {
            background: #dcfce7;
            border: 1px solid #22c55e;
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

        .welcome-card {
            background: #ffffff;
            border: 2px solid #22c55e;
            border-radius: 12px;
            padding: 40px 30px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .welcome-card h2 {
            font-size: 24px;
            color: #111827;
            margin-bottom: 12px;
        }

        .welcome-card h2 span {
            color: #22c55e;
        }

        .welcome-card p {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .button-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .button-grid.row-2 {
            grid-template-columns: repeat(1, 200px);
            justify-content: center;
        }

        .welcome-btn {
            padding: 14px 20px;
            border: 2px solid #22c55e;
            background: transparent;
            color: #22c55e;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .welcome-btn:hover {
            background: #22c55e;
            color: white;
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
                <a href="dashboard.php" class="nav-link active">
                    <span>🏠</span> Dashboard
                </a>
                <a href="index.php" class="nav-link">
                    <span>📦</span> Browse Items
                </a>
                <a href="post_item.php" class="nav-link">
                    <span>+</span> Post Item
                </a>
                <a href="tradehistory.php" class="nav-link">
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
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($fullname); ?>!</p>
        </div>

        <div class="welcome-card">
            <h2>Welcome to <span>Barter System</span></h2>
            <p>Start exchanging items with others in your community</p>
            
            <div class="button-grid">
                <a href="index.php" class="welcome-btn">Browse Items</a>
                <a href="post_item.php" class="welcome-btn">Post Item</a>
                <a href="tradehistory.php" class="welcome-btn">Trade History</a>
            </div>
            <div class="button-grid row-2">
                <a href="profile.php" class="welcome-btn">My Profile</a>
            </div>
        </div>
    </div>
</body>
</html>
