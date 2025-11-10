<?php
// =============================
// Session & Access Control
// =============================
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}
$email = $_SESSION['user_email'];
$nama  = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $email;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir CoffeeShop</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/kasir.css">
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <h1 class="brand-title">Enjoy The Most Delicious Coffee</h1>
      <p class="brand-subtitle">Point of Sale System</p>
      <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <!-- User Info (optional) -->
    <div style="text-align:center; margin-bottom:20px; font-family:'Montserrat', sans-serif; color:#555;">
      👋 Welcome, <strong><?php echo htmlspecialchars($nama); ?></strong> (<?php echo htmlspecialchars($email); ?>)
    </div>

    <form method="POST" action="proses_kasir_sederhana.php" id="kasirForm">
      <div class="form-container">
        
        <!-- Customer Info -->
        <div class="form-section">
          <h3 class="section-title">Customer Information</h3>
          
          <div class="input-group">
            <label>Customer Name <span style="color: #C7945D;">*</span></label>
            <input type="text" name="customer" placeholder="Enter customer name" required>
          </div>

          <div class="input-group">
            <label>Discount Coupon</label>
            <input type="text" name="voucher" placeholder="HEMAT10 or MAHASISWA5" style="text-transform: uppercase;">
            <small>💡 Available codes: <strong>HEMAT10</strong> (10% off) or <strong>MAHASISWA5</strong> (5% off)</small>
          </div>
        </div>

        <!-- Menu Section -->
        <div class="form-section">
          <h3 class="section-title">Select Your Order</h3>
          
          <div class="menu-grid">
            <!-- Americano -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1551030173-122aabc4489c?w=400&h=300&fit=crop" alt="Americano" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Coffee</div>
                <div class="menu-name">Americano</div>
                <div class="rating">★★★★★ (4.9)</div>
                <div class="menu-price">Rp 20,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[americano]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Cappuccino -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=300&fit=crop" alt="Cappuccino" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Coffee</div>
                <div class="menu-name">Cappuccino</div>
                <div class="rating">★★★★★ (4.8)</div>
                <div class="menu-price">Rp 23,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[cappuccino]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Latte -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1561882468-9110e03e0f78?w=400&h=300&fit=crop" alt="Latte" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Coffee</div>
                <div class="menu-name">Caffè Latte</div>
                <div class="rating">★★★★★ (4.9)</div>
                <div class="menu-price">Rp 25,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[latte]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Matcha Latte -->
            <div class="menu-card">
              <img src="https://img.freepik.com/premium-photo/iced-matcha-latte-closeup-dark-background_172415-7525.jpg" alt="Matcha" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Coffee</div>
                <div class="menu-name">Matcha Latte <span class="badge-new">New</span></div>
                <div class="rating">★★★★★ (4.9)</div>
                <div class="menu-price">Rp 28,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[matcha]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Croissant -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=300&fit=crop" alt="Croissant" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Pastry</div>
                <div class="menu-name">Croissant</div>
                <div class="rating">★★★★☆ (4.7)</div>
                <div class="menu-price">Rp 15,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[croissant]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Donut -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=300&fit=crop" alt="Donut" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Pastry</div>
                <div class="menu-name">Donut</div>
                <div class="rating">★★★★☆ (4.6)</div>
                <div class="menu-price">Rp 12,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[donut]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Extra Shot -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&h=300&fit=crop" alt="Extra Shot" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Add-on</div>
                <div class="menu-name">Extra Shot</div>
                <div class="rating">★★★★★ (5.0)</div>
                <div class="menu-price">Rp 5,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[extrashot]" min="0" value="0" class="qty-input">
              </div>
            </div>

            <!-- Air Mineral -->
            <div class="menu-card">
              <img src="https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&h=300&fit=crop" alt="Water" class="menu-image">
              <div class="menu-card-header">
                <div class="menu-category">Beverage</div>
                <div class="menu-name">Mineral Water</div>
                <div class="rating">★★★★☆ (4.5)</div>
                <div class="menu-price">Rp 3,000</div>
              </div>
              <div class="qty-control">
                <span class="qty-label">Qty:</span>
                <input type="number" name="qty[mineral]" min="0" value="0" class="qty-input">
              </div>
            </div>
          </div>

          <!-- Total Display -->
          <div class="total-display">
            <div class="total-label">Estimated Total</div>
            <div class="total-amount" id="estimatedTotal">Rp 0</div>
          </div>
        </div>

        <!-- Payment Section -->
        <div class="form-section">
          <h3 class="section-title">Payment Details</h3>
          
          <div class="payment-grid">
            <div class="input-group">
              <label>Payment Method</label>
              <select name="payment">
                <option value="cash">💵 Cash</option>
                <option value="qris">📱 QRIS</option>
                <option value="debit">💳 Debit Card</option>
              </select>
            </div>

            <div class="input-group">
              <label>Amount Paid (Rp) <span style="color: #C7945D;">*</span></label>
              <input type="number" name="paid" id="paidInput" min="0" value="0" placeholder="0" required>
              <div class="quick-amount">
                <button type="button" class="quick-btn" onclick="setAmount(50000)">50K</button>
                <button type="button" class="quick-btn" onclick="setAmount(100000)">100K</button>
                <button type="button" class="quick-btn" onclick="setAmount(200000)">200K</button>
                <button type="button" class="quick-btn" onclick="setAmount(500000)">500K</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="button-group">
          <button type="submit" class="btn btn-primary">Process Transaction</button>
          <button type="reset" class="btn btn-secondary" onclick="resetCalculator()">Reset</button>
        </div>

        <!-- Logout -->
        <div class="logout-container">
          <form method="post" action="logout.php">
            <button type="submit" class="btn-danger">Logout</button>
          </form>
        </div>

      </div>
    </form>
  </div>

  <script>
    const prices = {
      americano: 20000,
      cappuccino: 23000,
      latte: 25000,
      matcha: 28000,
      croissant: 15000,
      donut: 12000,
      extrashot: 5000,
      mineral: 3000
    };

    function formatRupiah(number) {
      return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function calculateTotal() {
      let total = 0;
      const qtyInputs = document.querySelectorAll('.qty-input');
      qtyInputs.forEach(input => {
        const menuKey = input.name.match(/\[(\w+)\]/)[1];
        const qty = parseInt(input.value) || 0;
        total += prices[menuKey] * qty;
      });
      document.getElementById('estimatedTotal').textContent = formatRupiah(total);
    }

    function setAmount(amount) {
      document.getElementById('paidInput').value = amount;
    }

    function resetCalculator() {
      setTimeout(calculateTotal, 100);
    }

    document.addEventListener('DOMContentLoaded', function() {
      const qtyInputs = document.querySelectorAll('.qty-input');
      qtyInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
        input.addEventListener('change', calculateTotal);
      });
      document.getElementById('kasirForm').addEventListener('submit', function(e) {
        const total = parseInt(document.getElementById('estimatedTotal').textContent.replace(/\D/g, ''));
        if (total === 0) {
          e.preventDefault();
          alert('⚠️ Please select at least 1 item before processing the transaction!');
        }
      });
    });
  </script>
</body>
</html>
