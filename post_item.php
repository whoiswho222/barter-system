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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $wanted_item = mysqli_real_escape_string($conn, trim($_POST['wanted_item']));
    
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name);
    }

    if (!empty($item_name) && !empty($description)) {
        $sql = "INSERT INTO items (user_id, item_name, category, description, wanted_item, image, status) 
                VALUES ('$user_id', '$item_name', '$category', '$description', '$wanted_item', '$image_name', 'available')";
        mysqli_query($conn, $sql);
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post New Item — Barter System</title>
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
            <a href="dashboard.php" class="navbar-brand">Barter System</a>
            <a href="dashboard.php" class="btn btn-light btn-sm">Back</a>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-theme mb-4"> Post New Item</h3>
        
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Item Name *</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Category *</label>
                <select name="category" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <option value="Clothing & Accessories"> Clothing & Accessories</option>
                    <option value="Electronics & Gadgets"> Electronics & Gadgets</option>
                    <option value="Home & Living"> Home & Living</option>
                    <option value="Beauty & Personal Care"> Beauty & Personal Care</option>
                    <option value="Sports & Outdoors"> Sports & Outdoors</option>
                    <option value="Books & Education"> Books & Education</option>
                    <option value="Toys, Games & Hobbies"> Toys, Games & Hobbies</option>
                    <option value="Food & Groceries"> Food & Groceries</option>
                    <option value="Services & Skills"> Services & Skills</option>
                    <option value="Vouchers & Gift Cards"> Vouchers & Gift Cards</option>
                    <option value="Others"> Others</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description *</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe the item — condition, brand, size, etc." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">What You Want in Exchange</label>
                <input type="text" name="wanted_item" class="form-control" placeholder="Item or service you want to receive">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Upload Photo</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success">Post Item</button>
        </form>
    </div>
</body>
</html>