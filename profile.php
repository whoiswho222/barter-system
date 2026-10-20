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
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    
    if (!empty($fullname) && !empty($email)) {
        $sql = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone' WHERE id = '$user_id'";
        if (mysqli_query($conn, $sql)) {
            $msg = '<div class="alert alert-success">✅ Profile updated successfully!</div>';
        } else {
            $msg = '<div class="alert alert-danger">❌ Failed to update profile.</div>';
        }
    }
}

$res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .text-theme { color: #198754; }
        .btn-success { background-color: #198754; border-color: #198754; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand"> Barter System</a>
            <div>
                <a href="trades_history.php" class="btn btn-light btn-sm me-2"> Trades History</a>
                <a href="dashboard.php" class="btn btn-light btn-sm">Back</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-theme mb-4">👤 My Profile</h3>
        
        <?= $msg ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name *</label>
                <input type="text" name="fullname" class="form-control" 
                       value="<?= htmlspecialchars($user['fullname']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address *</label>
                <input type="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Phone / Contact Number</label>
                <input type="text" name="phone" class="form-control" 
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                       placeholder="Optional — for faster communication">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Account Created</label>
                <input type="text" class="form-control" 
                       value="<?= isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'Not available' ?>" disabled>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</body>
</html>