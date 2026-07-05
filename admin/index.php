<?php
require_once '../includes/db.php';

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Stats
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'");
$total_revenue = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Shophoria</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-black: #111;
            --color-gold: #c5a059;
            --color-sidebar: #222;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { display: flex; background: #f4f6f9; color: #333; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--color-sidebar); color: white; padding: 20px 0; }
        .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-header h2 { color: var(--color-gold); font-size: 1.5rem; letter-spacing: 1px; }
        .nav-link { display: block; color: #ccc; padding: 12px 20px; text-decoration: none; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; border-left: 3px solid var(--color-gold); }
        .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 8px 15px; background: var(--color-black); color: white; text-decoration: none; border-radius: 4px; font-size: 0.9rem; border: none; cursor: pointer; }
        .btn:hover { background: var(--color-gold); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 4px solid var(--color-gold); }
        .stat-card h3 { font-size: 0.9rem; color: #777; margin-bottom: 10px; text-transform: uppercase; }
        .stat-card .value { font-size: 1.8rem; font-weight: 600; color: var(--color-black); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #fafafa; font-weight: 500; color: #555; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card h2 { margin-bottom: 20px; font-size: 1.2rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Shophoria</h2>
            <p style="font-size: 0.8rem; color: #999;">Admin Panel</p>
        </div>
        <a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a>
        <a href="../index.php" class="nav-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);"><i class="fas fa-store"></i> View Store</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <div>
                Welcome, Admin | <a href="../actions/logout.php" style="color: #666; margin-left: 10px;">Logout</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value">$<?php echo number_format($total_revenue ?: 0, 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="value"><?php echo $total_orders; ?></div>
            </div>
            <div class="stat-card">
                <h3>Products</h3>
                <div class="value"><?php echo $total_products; ?></div>
            </div>
            <div class="stat-card">
                <h3>Users</h3>
                <div class="value"><?php echo $total_users; ?></div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <h2>Recent Orders</h2>
            <?php
            $stmt = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.date DESC LIMIT 5");
            $recent_orders = $stmt->fetchAll();
            ?>
            <?php if(empty($recent_orders)): ?>
                <p>No recent orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['date'])); ?></td>
                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <span style="background: <?php echo $order['status']=='pending' ? '#fff3cd' : '#d4edda'; ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
