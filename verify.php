<?php
include 'config.php';

if (isset($_GET['code'])) {
    $code = trim($_GET['code']);
    
    $sql = "UPDATE users SET is_verified = 1, verification_code = NULL WHERE verification_code = '$code'";
    
    if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
        $message = "Your account has been verified successfully. You may now login.";
        $type = "success";
    } else {
        $message = "Verification link is invalid or has expired.";
        $type = "danger";
    }
} else {
    header("Location: register.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Email Verification — Barter System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f0e6; font-family: Arial, sans-serif; }
    .container { max-width: 450px; margin-top: 100px; }
  </style>
</head>
<body>
  <div class="container text-center">
    <div class="card p-4">
      <h3 class="mb-4" style="color: #5c3c1e;">Email Verification</h3>
      <div class="alert alert-<?php echo $type; ?>"><?php echo $message; ?></div>
      <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>
  </div>
</body>
</html>