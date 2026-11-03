<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (fullname, email, contact, password, created_at) 
            VALUES ('$fullname', '$email', '$contact', '$password', NOW())");
        
        if ($insert) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f0e6; font-family: Arial, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px 0; }
        .reg-card { background: white; border-radius: 12px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); width: 100%; max-width: 450px; }
        .btn-reg { background-color: #2e7d32; border: none; padding: 10px; color: white; font-weight: bold; width: 100%; }
        .btn-reg:hover { background-color: #276c2b; color: white; }
    </style>
</head>
<body>
    <div class="reg-card">
        <h3 class="text-center mb-4" style="color: #2e7d32;">Create Account</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control">
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-reg">Register</button>
            <p class="text-center mt-3 mb-0">
                Already have an account? <a href="login.php" style="color: #2e7d32;">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>