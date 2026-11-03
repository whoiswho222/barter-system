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

// Get user data
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'"));

// Handle profile update & picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $profile_pic = $user['profile_pic'] ?? '';

    // Upload new profile picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $profile_pic = 'profile_'.$user_id.'_'.time().'.'.$ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/'.$profile_pic);
        }
    }

    mysqli_query($conn, "UPDATE users SET fullname = '$new_name', email = '$new_email', profile_pic = '$profile_pic' WHERE id = '$user_id'");
    $_SESSION['fullname'] = $new_name;
    $_SESSION['profile_pic'] = $profile_pic;
    $success = "Profile updated successfully!";
    
    // Refresh user data
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — Barter System</title>
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
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .success-msg {
            background: #dcfce7;
            border: 1px solid #22c55e;
            color: #16a34a;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .profile-card {
            background: white;
            border: 2px solid #22c55e;
            border-radius: 12px;
            padding: 35px;
            max-width: 500px;
            margin: 0 auto;
        }

        .profile-avatar-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin: 0 auto 15px;
            overflow: hidden;
            border: 3px solid #bbf7d0;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-bottom: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border: 2px solid #bbf7d0;
            background: white;
            color: #374151;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            width: 260px;
            justify-content: flex-start;
        }

        .action-btn:hover {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .action-btn .icon {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn.primary {
            border-color: #22c55e;
            color: #22c55e;
        }

        .action-btn.primary:hover {
            background: #22c55e;
            color: white;
        }

        .profile-join {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 5px;
            margin-bottom: 20px;
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

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #bbf7d0;
            border-radius: 8px;
            font-size: 15px;
            transition: border 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #22c55e;
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
            transition: background 0.2s;
        }

        .save-btn:hover {
            background: #16a34a;
        }

        #pic-input {
            display: none;
        }

        .view-pic-link {
            text-decoration: none;
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
                    <span class="icon">🏠</span>
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
                <a href="post_item.php" class="nav-link">
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
                <a href="profile.php" class="nav-link active">
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
                        <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" alt="">
                    <?php else: ?>
                        <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                    <?php endif; ?>
                </div>
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
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>

        <?php if (isset($success)): ?>
        <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-avatar-section">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_pic'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                    <?php endif; ?>
                </div>
                
                <div class="profile-actions">
                    <?php if (!empty($user['profile_pic'])): ?>
                    <a href="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" target="_blank" class="view-pic-link">
                        <button type="button" class="action-btn">
                            <span class="icon">
                                <svg class="icon-svg" viewBox="0 0 24 24">
                                    <path d="M3 9v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2h-1.17l-1.24-1.35c-.37-.41-.91-.65-1.46-.65H8.87c-.55 0-1.09.24-1.46.65L6.17 7H5c-1.1 0-2 .9-2 2zm9 7c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                                </svg>
                            </span>
                            See profile picture
                        </button>
                    </a>
                    <?php endif; ?>
                    
                    <label for="pic-input" class="action-btn primary">
                        <span class="icon">
                            <svg class="icon-svg" viewBox="0 0 24 24">
                                <path d="M3 9v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2h-1.17l-1.24-1.35c-.37-.41-.91-.65-1.46-.65H8.87c-.55 0-1.09.24-1.46.65L6.17 7H5c-1.1 0-2 .9-2 2zm9 7c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                            </svg>
                        </span>
                        Choose profile picture
                    </label>
                </div>
                
                <p class="profile-join">Member since: <?php echo isset($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : '---'; ?></p>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_pic" id="pic-input" accept="image/*">
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-input" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="tel" name="contact_number" class="form-input" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" placeholder="09XX-XXX-XXXX" required>
                </div>

    <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>

</body>
</html>
