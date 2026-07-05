<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    header("Location: orders.php?updated=1");
    exit;
}

// Fetch Orders
$stmt = $pdo->query("SELECT o.*, u.name as user_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.date DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Orders | Shophoria</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; background: #f4f6f9; color: #333; min-height: 100vh; margin: 0; font-family: 'Outfit', sans-serif; }
        .sidebar { width: 250px; background: #222; color: white; padding: 20px 0; }
        .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-header h2 { color: #c5a059; font-size: 1.5rem; letter-spacing: 1px; margin:0;}
        .nav-link { display: block; color: #ccc; padding: 12px 20px; text-decoration: none; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; border-left: 3px solid #c5a059; }
        .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #fafafa; font-weight: 500; color: #555; }
        .btn { padding: 8px 15px; background: #111; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9rem; border: none; cursor: pointer; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Shophoria</h2>
        </div>
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a>
        <a href="../index.php" class="nav-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);"><i class="fas fa-store"></i> View Store</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Orders</h1>
        </div>

        <div class="card">
            <?php if(isset($_GET['updated'])) echo "<div style='color:green;margin-bottom:15px;'>Order status updated!</div>"; ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td><?php echo htmlspecialchars($o['user_name']); ?><br><small style="color:#888;"><?php echo htmlspecialchars($o['email']); ?></small></td>
                        <td>$<?php echo number_format($o['total'], 2); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($o['date'])); ?></td>
                        <td>
                            <form method="POST" style="display:flex;gap:5px;">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" style="padding:5px; border-radius:4px; border:1px solid #ccc;">
                                    <option value="pending" <?php if($o['status']=='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="completed" <?php if($o['status']=='completed') echo 'selected'; ?>>Completed</option>
                                    <option value="cancelled" <?php if($o['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size:0.8rem;"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                        <td>
                            <button class="btn" style="background:#555; padding: 5px 10px; font-size:0.8rem;" onclick="alert('Phone: <?php echo addslashes($o['phone']); ?>\\nAddress: <?php echo addslashes($o['address']); ?>');">Details</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
