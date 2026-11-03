<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($res);
    
    if (!$user) {
        $error = "Email address not found.";
    } else {
        $valid = false;
        if (password_verify($password, $user['password'])) {
            $valid = true;
        } elseif (md5($password) === $user['password']) {
            $valid = true;
        }
        
        if ($valid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barter System — Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0fff4 0%, #d9f9e9 100%);
            padding: 20px;
        }

        .login-modal {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(34, 197, 94, 0.15);
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: 2px solid #22c55e;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            color: #22c55e;
            transition: all 0.2s;
        }

        .close-btn:hover {
            background: #22c55e;
            color: #ffffff;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .logo-img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .logo-text {
            font-size: 26px;
            font-weight: 700;
            color: #1f2937;
        }

        .logo-text span {
            color: #22c55e;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: #f9fafb;
            border: 2px solid #22c55e;
            border-radius: 12px;
            color: #1f2937;
            font-size: 15px;
            transition: border 0.3s, background 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #16a34a;
            background: #ffffff;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .password-wrapper {
            position: relative;
        }

        .show-pass-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 2px solid #22c55e;
            color: #22c55e;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .show-pass-toggle:hover {
            background: #22c55e;
            color: #ffffff;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: transparent;
            color: #22c55e;
            border: 3px solid #22c55e;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .login-btn:hover {
            background: #22c55e;
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.35);
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
        }

        .register-link a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-modal">
        <button class="close-btn" onclick="window.location.href='dashboard.php'">&times;</button>
        
        <div class="logo-section">
            <img src="logo.jpg" alt="Barter System Logo" class="logo-img" onerror="this.style.display='none'">
            <div class="logo-text">Barter<span>System</span></div>
        </div>

        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to continue to your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="show-pass-toggle" onclick="togglePassword()">Show</button>
                </div>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
    </div>

<script>
function togglePassword() {
    const passInput = document.getElementById('password');
    const btn = document.querySelector('.show-pass-toggle');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        btn.textContent = 'Hide';
    } else {
        passInput.type = 'password';
        btn.textContent = 'Show';
    }
}
</script>
</body>
</html>
