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
$item_id = intval($_GET['id'] ?? 0);

// Kunin ang detalye ng item
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE id = '$item_id'"));

if (!$item) {
    die("Item not found.");
}

// Kunin ang lahat ng offer/komento dito
$offers = mysqli_query($conn, "
    SELECT c.*, u.fullname, u.profile_pic 
    FROM comments c 
    INNER JOIN users u ON c.user_id = u.id 
    WHERE c.item_id = '$item_id' AND c.is_reply = 0
    ORDER BY c.id DESC
");

// Kapag nag-send ng offer
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $offered_item = trim($_POST['offered_item'] ?? '');
    
    mysqli_query($conn, "
        INSERT INTO comments (item_id, user_id, message, offered_item, created_at)
        VALUES ('$item_id', '$user_id', '$msg', '$offered_item', NOW())
    ");
    
    header("Location: item.php?id=$item_id");
    exit;
}

// Kapag minarkahang Traded
if (isset($_GET['mark_traded']) && $item['user_id'] == $user_id) {
    mysqli_query($conn, "UPDATE items SET status = 'traded' WHERE id = '$item_id'");
    header("Location: item.php?id=$item_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($item['item_name']) ?> — Barter System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 50%, #81c784 100%);
            min-height: 100vh;
            padding: 30px;
        }
        .back-btn {
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn:hover { text-decoration: underline; }
        .card {
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            border: none;
            margin-bottom: 25px;
        }
        .item-img {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 12px;
        }
        .badge-available { background-color: #2e7d32; }
        .badge-traded { background-color: #6c757d; }
        .offer-card {
            border-left: 4px solid #2e7d32;
            margin-bottom: 15px;
        }
        .btn-green {
            background: linear-gradient(90deg, #2e7d32, #43a047);
            color: white;
            border: none;
        }
        .btn-green:hover {
            color: white;
            background: linear-gradient(90deg, #1b5e20, #2e7d32);
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

    <div class="container">
        <!-- DETALYE NG ITEM -->
        <div class="card p-4">
            <div class="row">
                <div class="col-md-5">
                    <?php 
                    $img = !empty($item['image']) ? "uploads/".$item['image'] : "https://via.placeholder.com/350";
                    ?>
                    <img src="<?= $img ?>" class="item-img" alt="Item Image">
                </div>
                <div class="col-md-7">
                    <h2 class="mb-2"><?= htmlspecialchars($item['item_name']) ?></h2>
                    <span class="badge <?= ($item['status'] ?? 'available') == 'traded' ? 'badge-traded' : 'badge-available' ?> fs-6 mb-3">
                        <?= ($item['status'] ?? 'available') == 'traded' ? 'Traded' : 'Available' ?>
                    </span>
                    
                    <p class="text-muted mb-1"><strong>Category:</strong> <?= htmlspecialchars($item['category'] ?? 'Not specified') ?></p>
                    <p class="mb-3"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                    <p class="small text-muted">Posted: <?= date('F d, Y h:i A', strtotime($item['posted_at'] ?? 'NOW')) ?></p>
                    
                    <?php if ($item['user_id'] == $user_id && ($item['status'] ?? 'available') != 'traded'): ?>
                        <a href="item.php?id=<?= $item_id ?>&mark_traded=1" class="btn btn-secondary">Mark as Traded</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- MAG-SEND NG OFFER -->
        <?php if ($item['user_id'] != $user_id && ($item['status'] ?? 'available') != 'traded'): ?>
        <div class="card p-4">
            <h5 class="mb-3">Send an Offer</h5>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Your Message / Offer</label>
                    <textarea name="message" class="form-control" rows="3" required placeholder="Tell them what you think..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item you are offering (optional)</label>
                    <input type="text" name="offered_item" class="form-control" placeholder="e.g., Pink Tumbler">
                </div>
                <button type="submit" class="btn btn-green">Submit Offer</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- MGA OFFER NA NATANGGAP -->
        <div class="card p-4">
            <h5 class="mb-3">Offers (<?= mysqli_num_rows($offers) ?>)</h5>
            
            <?php if (mysqli_num_rows($offers) > 0): ?>
                <?php while ($offer = mysqli_fetch_assoc($offers)): ?>
                    <div class="card offer-card p-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <?php 
                            $avt = !empty($offer['profile_pic']) ? "uploads/".$offer['profile_pic'] : "default.png";
                            ?>
                            <img src="<?= $avt ?>" style="width:35px; height:35px; border-radius:50%; object-fit:cover;">
                            <div>
                                <strong><?= htmlspecialchars($offer['fullname']) ?></strong>
                                <div class="small text-muted"><?= date('M d, Y h:i A', strtotime($offer['created_at'])) ?></div>
                            </div>
                        </div>
                        <p class="mb-1"><?= nl2br(htmlspecialchars($offer['message'])) ?></p>
                        <?php if (!empty($offer['offered_item'])): ?>
                            <p class="mb-0 text-success"><strong>Offered:</strong> <?= htmlspecialchars($offer['offered_item']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center py-3">No offers yet. Be the first to offer!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
