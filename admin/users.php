<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Users | Shophoria</title>
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
        <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="users.php" class="nav-link active"><i class="fas fa-users"></i> Users</a>
        <a href="../index.php" class="nav-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);"><i class="fas fa-store"></i> View Store</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Users</h1>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span style="background: <?php echo $u['role']=='admin' ? '#c5a059' : '#eee'; ?>; color: <?php echo $u['role']=='admin' ? '#fff' : '#333'; ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                <?php echo ucfirst($u['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
