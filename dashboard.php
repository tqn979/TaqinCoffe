<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php'); 
    exit;
}
$email = $_SESSION['user_email'];
$nama  = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $email;

// ============================================
// BACA DATA TRANSAKSI HARI INI
// ============================================
$dirData = __DIR__ . '/data';
$fileTransaksi = $dirData . '/transactions.txt';

$todayOrders = 0;
$todayRevenue = 0;
$itemCount = [];

if (file_exists($fileTransaksi)) {
    $lines = file($fileTransaksi, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $today = date('Y-m-d');
    
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && isset($data['date']) && $data['date'] === $today) {
            $todayOrders++;
            $todayRevenue += $data['total'];
            
            // Hitung item untuk best seller
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $itemName = $item['name'];
                    if (!isset($itemCount[$itemName])) {
                        $itemCount[$itemName] = 0;
                    }
                    $itemCount[$itemName] += $item['qty'];
                }
            }
        }
    }
}

// Cari best seller
$bestSeller = '-';
if (!empty($itemCount)) {
    arsort($itemCount);
    $bestSeller = key($itemCount);
}

function rupiah($n) {
    return "Rp " . number_format((float)$n, 0, ",", ".");
}

// ============================================
// DATA PRODUK
// ============================================
$products = [
    [
        'name' => 'Americano',
        'price' => 20000,
        'description' => 'Espresso shot yang dicampur air panas, menghasilkan kopi yang kuat namun smooth dengan rasa yang kaya',
        'category' => 'Hot Coffee',
        'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400'
    ],
    [
        'name' => 'Cappuccino',
        'price' => 23000,
        'description' => 'Kombinasi sempurna antara espresso, steamed milk, dan milk foam yang creamy dengan taburan cokelat',
        'category' => 'Hot Coffee',
        'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400'
    ],
    [
        'name' => 'Caffè Latte',
        'price' => 25000,
        'description' => 'Espresso dengan steamed milk yang lembut, menciptakan rasa yang creamy dan tidak terlalu kuat',
        'category' => 'Hot Coffee',
        'image' => 'https://images.unsplash.com/photo-1561882468-9110e03e0f78?w=400'
    ],
    [
        'name' => 'Matcha Latte',
        'price' => 28000,
        'description' => 'Green tea matcha premium dari Jepang dengan susu segar, kaya antioksidan dan rasa yang unik',
        'category' => 'Non Coffee',
        'image' => 'https://img.freepik.com/premium-photo/iced-matcha-latte-closeup-dark-background_172415-7525.jpg'
    ],
    [
        'name' => 'Croissant',
        'price' => 15000,
        'description' => 'Pastry Prancis yang renyah di luar dan lembut di dalam, sempurna untuk menemani kopi pagi Anda',
        'category' => 'Food',
        'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400'
    ],
    [
        'name' => 'Donut',
        'price' => 12000,
        'description' => 'Donut lembut dengan berbagai topping manis yang menggugah selera, fresh every day',
        'category' => 'Food',
        'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400'
    ]
];

// ============================================
// DATA STAFF
// ============================================
$staff = [
    [
        'name' => 'Kim Sya Unch',
        'position' => 'Owner',
        'description' => 'Pemilik sekaligus pengelola utama yang bertanggung jawab atas visi, konsep, dan operasional kafe. Fokus pada pengalaman pelanggan serta pengembangan bisnis dan brand café.',
        'image' => 'aifaceswap-de77ab21f728719fe13c07b59a65145b.jpg'
    ],
    [
        'name' => 'Lee Kin Hoo',
        'position' => 'Head Barista',
        'description' => 'Pemimpin tim barista yang memastikan kualitas setiap sajian kopi tetap konsisten. Berpengalaman dalam teknik brewing, latte art, dan pengembangan resep kopi.',
        'image' => 'aifaceswap-f63059d1f2df53cee1b4fa4c3f82d98c.jpg'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CoffeeShop POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=2070');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -2;
        }
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 60px;
            color: white;
        }
        .brand-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 72px;
            font-weight: 300;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .brand-subtitle {
            font-family: 'Cormorant Garamond', serif;
            font-size: 18px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .tagline {
            font-size: 13px;
            letter-spacing: 2px;
            margin-bottom: 50px;
            opacity: 0.8;
        }

        /* User Bar */
        .user-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            border-radius: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        
        .user-details h3,
        .user-details p {
            color: white;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #2c2c2c;
        }

        /* Navigation */
        .nav-menu {
            text-align: center;
            margin-bottom: 40px;
        }
        .nav-links {
            display: inline-flex;
            gap: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 50px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }
        .nav-link:hover {
            opacity: 0.7;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 1px;
            background: white;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        /* Section Container */
        .content-section {
            display: none;
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }
        .content-section.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Report Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        .stat-card h4 {
            color: #faf9f9ff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .stat-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 300;
            color: #f7f0f0ff;
            margin-bottom: 8px;
        }
        .stat-label {
            color: #fdfafaff;
            font-size: 12px;
            letter-spacing: 1px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-info {
            padding: 20px;
            color: white;
        }
        .product-category {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #c49b63;
            margin-bottom: 8px;
        }
        .product-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 400;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 20px;
            font-weight: 600;
            color: #c49b63;
            margin-bottom: 12px;
        }
        .product-description {
            font-size: 13px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Staff Grid */
        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .staff-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .staff-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .staff-info {
            padding: 25px;
            color: white;
            text-align: center;
        }
        .staff-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 5px;
        }
        .staff-position {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #c49b63;
            margin-bottom: 15px;
        }
        .staff-description {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Social Icons */
        .social-icons {
            text-align: center;
            margin-top: 30px;
        }
        .social-icon {
            display: inline-flex;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            margin: 0 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 20px;
        }
        .social-icon.instagram {
            background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
        }
        .social-icon.whatsapp {
            background-color: #25D366;
        }
        .social-icon:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .brand-title {
                font-size: 42px;
                letter-spacing: 4px;
            }
            .nav-links {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }
            .user-bar {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }
            .products-grid,
            .staff-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1 class="brand-title">KOPI MURAH CUY</h1>
        <p class="brand-subtitle">Coffee & Tea</p>
        <p class="tagline">Mau kopi enak dan murah? Ya kesini aja</p>
    </div>

    <!-- User Bar -->
    <div class="user-bar">
        <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($nama, 0, 1)); ?></div>
            <div class="user-details">
                <h3><?php echo htmlspecialchars($nama); ?></h3>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Navigation -->
    <div class="nav-menu">
        <div class="nav-links">
            <a href="form_kasir_sederhana.php" class="nav-link">Order Ahead</a>
            <span class="nav-link" id="btnReports">Reports</span>
            <span class="nav-link" id="btnProducts">Products</span>
            <span class="nav-link" id="btnStaff">Staff</span>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="content-section" id="reportsSection">
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Today's Orders</h4>
                <div class="stat-value"><?php echo $todayOrders; ?></div>
                <div class="stat-label"><?php echo $todayOrders > 0 ? 'Transactions completed' : 'No transactions yet'; ?></div>
            </div>
            <div class="stat-card">
                <h4>Total Revenue</h4>
                <div class="stat-value"><?php echo rupiah($todayRevenue); ?></div>
                <div class="stat-label">Today</div>
            </div>
            <div class="stat-card">
                <h4>Best Seller</h4>
                <div class="stat-value" style="font-size: 28px;"><?php echo htmlspecialchars($bestSeller); ?></div>
                <div class="stat-label"><?php echo $bestSeller !== '-' ? $itemCount[$bestSeller] . ' sold today' : 'No data available'; ?></div>
            </div>
            <div class="stat-card">
                <h4>System Status</h4>
                <div class="stat-value" style="color: #43e97b;">✓</div>
                <div class="stat-label">All systems operational</div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="content-section" id="productsSection">
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                <div class="product-info">
                    <div class="product-category"><?php echo $product['category']; ?></div>
                    <div class="product-name"><?php echo $product['name']; ?></div>
                    <div class="product-price"><?php echo rupiah($product['price']); ?></div>
                    <div class="product-description"><?php echo $product['description']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Staff Section -->
    <div class="content-section" id="staffSection">
        <div class="staff-grid">
            <?php foreach ($staff as $member): ?>
            <div class="staff-card">
                <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="staff-image">
                <div class="staff-info">
                    <div class="staff-name"><?php echo $member['name']; ?></div>
                    <div class="staff-position"><?php echo $member['position']; ?></div>
                    <div class="staff-description"><?php echo $member['description']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Social Icons -->
    <div class="social-icons">
        <a href="https://www.instagram.com/kikitqnn" target="_blank" class="social-icon instagram" title="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://wa.me/6285367080237" target="_blank" class="social-icon whatsapp" title="Chat via WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>
</div>

<!-- JS -->
<script>
// Function to hide all sections
function hideAllSections() {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('show');
    });
}

// Reports button
document.getElementById("btnReports").addEventListener("click", function(e) {
    e.preventDefault();
    const section = document.getElementById("reportsSection");
    
    if (section.classList.contains('show')) {
        section.classList.remove('show');
    } else {
        hideAllSections();
        section.classList.add('show');
        setTimeout(() => {
            window.scrollTo({ top: section.offsetTop - 100, behavior: 'smooth' });
        }, 100);
    }
});

// Products button
document.getElementById("btnProducts").addEventListener("click", function(e) {
    e.preventDefault();
    const section = document.getElementById("productsSection");
    
    if (section.classList.contains('show')) {
        section.classList.remove('show');
    } else {
        hideAllSections();
        section.classList.add('show');
        setTimeout(() => {
            window.scrollTo({ top: section.offsetTop - 100, behavior: 'smooth' });
        }, 100);
    }
});

// Staff button
document.getElementById("btnStaff").addEventListener("click", function(e) {
    e.preventDefault();
    const section = document.getElementById("staffSection");
    
    if (section.classList.contains('show')) {
        section.classList.remove('show');
    } else {
        hideAllSections();
        section.classList.add('show');
        setTimeout(() => {
            window.scrollTo({ top: section.offsetTop - 100, behavior: 'smooth' });
        }, 100);
    }
});
</script>
</body>
</html>