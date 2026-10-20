<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $pass = md5(trim($_POST['password']));
    
    $sql = "INSERT INTO users (fullname, email, password, contact) VALUES ('$name', '$email', '$pass', '$contact')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center mt-5'>Registered! <a href='login.php'>Login here</a></div>";
    } else {
        $error = "Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; }
        .btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-4 offset-md-4">
            <h3 class="text-center mb-4">Create Account</h3>
            <div class="card p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Register</button>
                    <div class="mt-3 text-center">
                        Have account? <a href="login.php">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>