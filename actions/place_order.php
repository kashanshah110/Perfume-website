<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: ../cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, address, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $total, $address, $phone]);
        $order_id = $pdo->lastInsertId();
        
        // Insert Order Items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart as $item) {
            $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }
        
        $pdo->commit();
        
        // Clear Cart
        $_SESSION['cart'] = [];
        
        // Redirect to success page or orders page
        header("Location: ../orders.php?success=1");
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Order placement failed: " . $e->getMessage());
    }
} else {
    header("Location: ../checkout.php");
}
?>
