<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = trim($_POST['image']);
    $description = trim($_POST['description']);
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $category, $image, $description]);
    header("Location: products.php?success=1");
    exit;
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?deleted=1");
    exit;
}

// Fetch Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Products | Shophoria</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reusing some admin styles */
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
        .btn-danger { background: #cc0000; }
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Shophoria</h2>
        </div>
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="products.php" class="nav-link active"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a>
        <a href="../index.php" class="nav-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);"><i class="fas fa-store"></i> View Store</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Products</h1>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Add New Product</h2>
            <form method="POST" action="products.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="Men">Men</option>
                        <option value="Women">Women</option>
                        <option value="Unisex">Unisex</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" class="form-control" placeholder="https:// images..." required>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <button type="submit" class="btn" style="background:#c5a059;">Add Product</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Product List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><img src="<?php echo htmlspecialchars($p['image']); ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                        <td>$<?php echo number_format($p['price'], 2); ?></td>
                        <td>
                            <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
