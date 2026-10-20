<?php
include 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_credentials'])) {
    $email = trim($_POST['email']);
    $pass = md5(trim($_POST['password']));
    
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$pass'");
    
    if (mysqli_num_rows($res) === 1) {
        die("SUCCESS");
    } else {
        die("FAILED");
    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pass = md5(trim($_POST['password']));
    
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$pass'");
    
    if (mysqli_num_rows($res) === 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Barter System — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; }
        .btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
        .toggle-icon {
            cursor: pointer;
            color: #65676b;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
        }
        .toggle-icon svg {
            width: 20px;
            height: 20px;
        }
        .status-valid { color: #198754; font-size: 14px; }
        .status-invalid { color: #dc3545; font-size: 14px; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-4 offset-md-4">
            <h3 class="text-center mb-4">Web-Based Barter System</h3>
            <div class="card p-4">
                <h5 class="text-center mb-3">Login</h5>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="post" id="loginForm">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-2">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <span class="input-group-text bg-white border-start-0">
                                <span id="toggleBtn" class="toggle-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <div id="passwordStatus" class="mb-3"></div>
                    
                    <button class="btn btn-primary w-100">Sign In</button>
                    <div class="mt-3 text-center">
                        No account? <a href="register.php">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const emailInput = document.getElementById('email');
        const statusDiv = document.getElementById('passwordStatus');
        const toggleBtn = document.getElementById('toggleBtn');
        
        const eyeHidden = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;
        
        const eyeShown = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 0 0-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
        `;
        
        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.innerHTML = eyeShown;
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerHTML = eyeHidden;
            }
        });
        
        passwordInput.addEventListener('input', checkCredentials);
        emailInput.addEventListener('input', checkCredentials);
        
        function checkCredentials() {
            const email = emailInput.value.trim();
            const pass = passwordInput.value;
            
            if (email.length > 5 && pass.length >= 4) {
                statusDiv.textContent = "Checking...";
                statusDiv.className = "mb-3 text-muted";
                
                const formData = new FormData();
                formData.append('check_credentials', '1');
                formData.append('email', email);
                formData.append('password', pass);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(html => {
                    if (html.includes('SUCCESS')) {
                        statusDiv.textContent = "Correct email and password";
                        statusDiv.className = "mb-3 status-valid";
                    } else {
                        statusDiv.textContent = "Email or password does not match";
                        statusDiv.className = "mb-3 status-invalid";
                    }
                });
            } else {
                statusDiv.textContent = "";
                statusDiv.className = "mb-3";
            }
        }
    </script>
</body>
</html>