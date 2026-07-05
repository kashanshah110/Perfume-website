<?php
// Calculate cart total items
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shophoria | Luxury Fragrances</title>
    <!-- Google Fonts: Outfit for modern look, Playfair Display for luxury headings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
    <!-- Page Loader Animation -->
    <div class="page-loader">
        <div class="loader-spinner"></div>
    </div>
    
    <!-- Navigation Bar -->
    <header class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="logo">Shophoria</a>
            
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Shop</a>
                <a href="products.php?category=Men">Men</a>
                <a href="products.php?category=Women">Women</a>
                <a href="recommendations.php" style="color: var(--color-gold);"><i class="fas fa-magic"></i> AI Matches</a>
            </nav>
            
            <div class="nav-icons">
                <a href="#" class="search-icon"><i class="fas fa-search"></i></a>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>
                
                <div class="user-dropdown">
                    <a href="<?php echo isset($_SESSION['user_id']) ? '#' : 'login.php'; ?>" class="user-icon"><i class="far fa-user"></i></a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown-menu">
                        <a href="profile.php">My Profile</a>
                        <a href="orders.php">My Orders</a>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin/index.php">Admin Panel</a>
                        <?php endif; ?>
                        <a href="actions/logout.php">Logout</a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </header>
    
    <!-- Main Content wrapper starts here -->
    <main>
