<?php
require_once '../includes/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        // Fetch product to ensure it exists
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Check if already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity
                ];
            }
        }
    } 
    elseif ($action === 'update') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        if ($quantity <= 0) {
            $action = 'remove';
        } else {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
        }
    }
    
    if ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($_SESSION['cart'][$key]);
                // Reindex array
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    }
    
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    }
    
    // Redirect back to cart or previous page
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../cart.php';
    if (strpos($redirect, 'cart_action.php') !== false) {
        $redirect = '../cart.php';
    }
    
    // If adding from anywhere other than cart, can go to cart or back
    if ($action === 'add') {
        header("Location: ../cart.php");
    } else {
        header("Location: $redirect");
    }
    exit;
}
?>
