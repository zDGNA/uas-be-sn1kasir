
<?php
require_once '../controllers/AuthController.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/TransactionController.php';

$auth = new AuthController();
$auth->requireLogin();

$current_user = $auth->getCurrentUser();
$productController = new ProductController();
$transactionController = new TransactionController();

// Get statistics
$today = date('Y-m-d');
$dailySales = $transactionController->getDailySalesReport($today);
$lowStockProducts = $productController->getLowStock();
$bestSellingProducts = $transactionController->getBestSellingProducts(5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Kasir</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar-menu a:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .card-header h3 {
            color: #333;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-danger {
            background: #fee;
            color: #d63384;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd8;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar-content {
                flex-direction: column;
                gap: 10px;
            }
            
            .navbar-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">Sistem Kasir</div>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="pos.php">Point of Sale</a></li>
                <li><a href="products.php">Produk</a></li>
                <li><a href="transactions.php">Transaksi</a></li>
                <?php if($current_user['role'] == 'admin'): ?>
                <li><a href="users.php">Users</a></li>
                <li><a href="reports.php">Laporan</a></li>
                <?php endif; ?>
            </ul>
            <div class="user-info">
                <span>Halo, <?php echo htmlspecialchars($current_user['full_name']); ?></span>
                <a href="../controllers/AuthController.php?action=logout" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Selamat datang di sistem kasir</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Penjualan Hari Ini</h3>
                <div class="value">Rp <?php echo number_format($dailySales['total_sales'] ?? 0, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <h3>Transaksi Hari Ini</h3>
                <div class="value"><?php echo $dailySales['total_transactions'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Produk Stok Rendah</h3>
                <div class="value"><?php echo count($lowStockProducts); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pajak Hari Ini</h3>
                <div class="value">Rp <?php echo number_format($dailySales['total_tax'] ?? 0, 0, ',', '.'); ?></div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Produk Stok Rendah</h3>
                </div>
                <div class="card-body">
                    <?php if(empty($lowStockProducts)): ?>
                        <p>Tidak ada produk dengan stok rendah.</p>
                    <?php else: ?>
                        <?php foreach($lowStockProducts as $product): ?>
                        <div class="list-item">
                            <div>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                <small>Stok: <?php echo $product['stock']; ?> <?php echo $product['unit']; ?></small>
                            </div>
                            <span class="badge badge-danger">Stok Rendah</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Produk Terlaris</h3>
                </div>
                <div class="card-body">
                    <?php if(empty($bestSellingProducts)): ?>
                        <p>Belum ada data penjualan.</p>
                    <?php else: ?>
                        <?php foreach($bestSellingProducts as $product): ?>
                        <div class="list-item">
                            <div>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                <small>Terjual: <?php echo $product['total_sold']; ?> unit</small>
                            </div>
                            <div class="text-right">
                                <small>Rp <?php echo number_format($product['total_revenue'], 0, ',', '.'); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>