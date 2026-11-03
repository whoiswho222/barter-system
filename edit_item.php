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
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$res = mysqli_query($conn, "SELECT * FROM items WHERE id = '$item_id' AND user_id = '$user_id'");
$item = mysqli_fetch_assoc($res);

if (!$item) {
    echo "Item not found or not yours.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $wanted_item = mysqli_real_escape_string($conn, trim($_POST['wanted_item']));

    if (!empty($item_name) && !empty($description)) {
        $sql = "UPDATE items SET 
                    item_name = '$item_name',
                    category = '$category',
                    description = '$description',
                    wanted_item = '$wanted_item'
                WHERE id = '$item_id' AND user_id = '$user_id'";
        
        mysqli_query($conn, $sql);
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Item — Barter System</title>
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
            <a href="dashboard.php" class="btn btn-light btn-sm">Back</a>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-theme mb-4"> Edit Your Item</h3>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Item Name *</label>
                <input type="text" name="item_name" class="form-control" 
                       value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Category *</label>
                <select name="category" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <option value="Clothing & Accessories" <?= $item['category'] == 'Clothing & Accessories' ? 'selected' : '' ?>> Clothing & Accessories</option>
                    <option value="Electronics & Gadgets" <?= $item['category'] == 'Electronics & Gadgets' ? 'selected' : '' ?>> Electronics & Gadgets</option>
                    <option value="Home & Living" <?= $item['category'] == 'Home & Living' ? 'selected' : '' ?>> Home & Living</option>
                    <option value="Beauty & Personal Care" <?= $item['category'] == 'Beauty & Personal Care' ? 'selected' : '' ?>> Beauty & Personal Care</option>
                    <option value="Sports & Outdoors" <?= $item['category'] == 'Sports & Outdoors' ? 'selected' : '' ?>> Sports & Outdoors</option>
                    <option value="Books & Education" <?= $item['category'] == 'Books & Education' ? 'selected' : '' ?>> Books & Education</option>
                    <option value="Toys, Games & Hobbies" <?= $item['category'] == 'Toys, Games & Hobbies' ? 'selected' : '' ?>> Toys, Games & Hobbies</option>
                    <option value="Food & Groceries" <?= $item['category'] == 'Food & Groceries' ? 'selected' : '' ?>> Food & Groceries</option>
                    <option value="Services & Skills" <?= $item['category'] == 'Services & Skills' ? 'selected' : '' ?>> Services & Skills</option>
                    <option value="Vouchers & Gift Cards" <?= $item['category'] == 'Vouchers & Gift Cards' ? 'selected' : '' ?>> Vouchers & Gift Cards</option>
                    <option value="Others" <?= $item['category'] == 'Others' ? 'selected' : '' ?>> Others</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description *</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">What You Want in Exchange</label>
                <input type="text" name="wanted_item" class="form-control" 
                       value="<?= htmlspecialchars($item['wanted_item']) ?>">
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</body>
</html>
