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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $want_exchange = trim($_POST['want_exchange']);
    
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $image = 'item_'.time().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/'.$image);
        }
    }
    
    if (!empty($title)) {
        mysqli_query($conn, "INSERT INTO items (user_id, title, description, category, want_exchange, image, status, posted_at) 
            VALUES ('$user_id', '$title', '$description', '$category', '$want_exchange', '$image', 'available', NOW())");
        $success = "Item posted successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Item — Barter System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 40px;
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

        .nav-link .icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
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
            padding: 40px;
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

        .success-msg {
            background: #dcfce7;
            border: 1px solid #22c55e;
            color: #16a34a;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-card {
            background: white;
            border: 2px solid #22c55e;
            border-radius: 12px;
            padding: 35px;
            max-width: 600px;
        }

        .form-card h2 {
            text-align: center;
            color: #111827;
            margin-bottom: 25px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #bbf7d0;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #22c55e;
        }

        .form-textarea {
            min-height: 90px;
            resize: vertical;
        }

        .save-btn {
            width: 100%;
            padding: 12px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
        }

        .save-btn:hover {
            background: #16a34a;
        }

        .icon-svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
            <div class="sidebar-logo">
                Barter<span>System</span>
            </div>

            <nav class="nav-links">
                <a href="dashboard.php" class="nav-link">
                    <span class="icon">
                        <svg class="icon-svg" viewBox="0 0 24 24">
                            <path d="M3 3h6v6H3V3zm0 12h6v6H3v-6zm12-12h6v3h-6V3zm0 9h6v9h-6v-9z"/>
                        </svg>
                    </span>
                    Dashboard
                </a>
                <a href="index.php" class="nav-link">
                    <span class="icon">
                        <svg class="icon-svg" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </span>
                    Browse Items
                </a>
                <a href="post_item.php" class="nav-link active">
                    <span class="icon">+</span>
                    Post Item
                </a>
                <a href="tradehistory.php" class="nav-link">
                    <span class="icon">
                        <svg class="icon-svg" viewBox="0 0 24 24">
                            <path d="M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6a7 7 0 0 1 7-7c3.53 0 6.44 2.61 6.92 6H17v2h6v-2c0-4.97-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                        </svg>
                    </span>
                    Trade History
                </a>
                <a href="profile.php" class="nav-link">
                    <span class="icon">
                        <svg class="icon-svg" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    My Profile
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <div class="user-info">
                <div class="user-avatar">
                    <?php if (!empty($_SESSION['profile_pic'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <?php echo strtoupper(substr($_SESSION['fullname'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
                    <div style="font-size:11px;color:#6b7280;">Logged in</div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>Post Item</h1>
            <p>Share something you want to trade</p>
        </div>

        <?php if (isset($success)): ?>
        <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <h2>Post a New Item</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Item Title</label>
                    <input type="text" name="title" class="form-input" placeholder="What are you trading?" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Describe the item..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">What do you want in exchange?</label>
                    <textarea name="want_exchange" class="form-textarea" placeholder="Example: A bag, books, or anything useful..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">-- Select a category --</option>
                        <option value="Antiques & Collectibles">Antiques & Collectibles</option>
                        <option value="Arts & Crafts">Arts & Crafts</option>
                        <option value="Auto Parts">Auto Parts</option>
                        <option value="Baby">Baby</option>
                        <option value="Books, Movies & Music">Books, Movies & Music</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Free">Free</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Garage Sale">Garage Sale</option>
                        <option value="Health & Beauty">Health & Beauty</option>
                        <option value="Home & Kitchen">Home & Kitchen</option>
                        <option value="Home Improvement">Home Improvement</option>
                        <option value="Jewelry & Watches">Jewelry & Watches</option>
                        <option value="Kids & Baby">Kids & Baby</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                        <option value="Musical Instruments">Musical Instruments</option>
                        <option value="Pet Supplies">Pet Supplies</option>
                        <option value="Sporting Goods">Sporting Goods</option>
                        <option value="Toys & Games">Toys & Games</option>
                        <option value="Vehicles">Vehicles</option>
                        <option value="Women's Items">Women's Items</option>
                        <option value="Men's Items">Men's Items</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Item Image</label>
                    <input type="file" name="image" class="form-input" accept="image/*">
                </div>

                <button type="submit" class="save-btn">Post Item</button>
            </form>
        </div>
    </div>
</body>
</html>
