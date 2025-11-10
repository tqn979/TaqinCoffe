<?php
// Fungsi bantu format rupiah sederhana
function rupiah($n) {
    return "Rp " . number_format((float)$n, 0, ",", ".");
}

// Daftar harga di server (lebih aman)
$priceList = [
  'americano'  => ['label' => 'Americano',    'price' => 20000, 'category' => 'coffee'],
  'cappuccino' => ['label' => 'Cappuccino',   'price' => 23000, 'category' => 'coffee'],
  'latte'      => ['label' => 'Caffè Latte',  'price' => 25000, 'category' => 'coffee'],
  'matcha'     => ['label' => 'Matcha Latte', 'price' => 28000, 'category' => 'coffee'],
  'croissant'  => ['label' => 'Croissant',    'price' => 15000, 'category' => 'food'],
  'donut'      => ['label' => 'Donut',        'price' => 12000, 'category' => 'food'],
  'extrashot'  => ['label' => 'Extra Shot',   'price' =>  5000, 'category' => 'addon'],
  'mineral'    => ['label' => 'Mineral',      'price' =>  3000, 'category' => 'drink'],
];

// Ambil input
$qty      = isset($_POST['qty']) ? $_POST['qty'] : [];
$voucher  = isset($_POST['voucher']) ? strtoupper(trim($_POST['voucher'])) : '';
$payment  = isset($_POST['payment']) ? $_POST['payment'] : 'cash';
$paid     = isset($_POST['paid']) ? (int)$_POST['paid'] : 0;
$customer = isset($_POST['customer']) ? trim($_POST['customer']) : '';

// Generate nomor transaksi
$transactionNo = 'TRX' . date('Ymd') . substr(str_shuffle('0123456789'), 0, 4);

// Hitung item dan subtotal
$items = [];
$subtotal = 0;
foreach ($priceList as $key => $row) {
    $q = isset($qty[$key]) ? (int)$qty[$key] : 0;
    if ($q > 0) {
        $line = $q * $row['price'];
        $items[] = [
            'name'     => $row['label'],
            'price'    => $row['price'],
            'qty'      => $q,
            'total'    => $line,
            'category' => $row['category']
        ];
        $subtotal += $line;
    }
}

// Jika tidak ada item
if ($subtotal === 0) {
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Error - Tidak Ada Item</title>
      <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
      <style>
        body { 
          font-family: "Montserrat", sans-serif;
          background: linear-gradient(to bottom right, #1a1410, #2c2420); 
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .error-container { 
          max-width: 500px; 
          background: rgba(255, 255, 255, 0.95); 
          padding: 50px 40px; 
          border-radius: 15px; 
          text-align: center; 
          box-shadow: 0 20px 60px rgba(0,0,0,0.5); 
        }
        .error-icon { font-size: 70px; margin-bottom: 20px; }
        .error-title { 
          font-family: "Cormorant Garamond", serif;
          font-size: 32px; 
          color: #2c2c2c; 
          margin-bottom: 15px; 
          font-weight: 600; 
        }
        .error-message { 
          color: #666; 
          margin-bottom: 35px; 
          font-size: 15px; 
          line-height: 1.6;
        }
        .btn-back { 
          display: inline-block; 
          padding: 15px 40px; 
          background: linear-gradient(135deg, #8B4513, #D2691E); 
          color: white; 
          text-decoration: none; 
          border-radius: 8px; 
          font-weight: 700; 
          font-size: 13px;
          letter-spacing: 1px;
          text-transform: uppercase;
          transition: all 0.3s ease; 
        }
        .btn-back:hover { 
          transform: translateY(-3px); 
          box-shadow: 0 10px 25px rgba(139, 69, 19, 0.4); 
        }
      </style>
    </head>
    <body>
      <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h2 class="error-title">No Items Selected</h2>
        <p class="error-message">Please select at least 1 item to proceed with the transaction.</p>
        <a href="form_kasir_sederhana.php" class="btn-back">← Back to Cashier</a>
      </div>
    </body>
    </html>';
    exit;
}

// Diskon via kode (opsional)
$discRate = 0.0;
$voucherName = '';
if ($voucher === 'HEMAT10') {
    $discRate = 0.10;
    $voucherName = 'HEMAT10 (10%)';
} elseif ($voucher === 'MAHASISWA5') {
    $discRate = 0.05;
    $voucherName = 'MAHASISWA5 (5%)';
}
$discount = $subtotal * $discRate;

// Pajak 11% dari (subtotal - discount)
$taxBase = max(0, $subtotal - $discount);
$tax = $taxBase * 0.11;

// Total akhir
$total = $taxBase + $tax;

// Kembalian / kurang bayar
$kembalian = $paid - $total;
$kurangBayar = $kembalian < 0 ? abs($kembalian) : 0;

// Label metode bayar
$paymentLabel = [
  'cash'  => '💵 Cash',
  'qris'  => '📱 QRIS',
  'debit' => '💳 Debit Card'
];
$paymentTxt = isset($paymentLabel[$payment]) ? $paymentLabel[$payment] : '💵 Cash';

// Status pembayaran
$paymentStatus = $kembalian >= 0 ? 'PAID' : 'INSUFFICIENT PAYMENT';
$statusClass = $kembalian >= 0 ? 'success' : 'warning';

// ============================================
// SIMPAN TRANSAKSI KE FILE
// ============================================
$dirData = __DIR__ . '/data';
if (!is_dir($dirData)) {
    mkdir($dirData, 0755, true);
}

$fileTransaksi = $dirData . '/transactions.txt';

// Format: transactionNo|date|time|customer|payment|subtotal|discount|tax|total|paid|change|items_json
$transData = [
    'transaction_no' => $transactionNo,
    'date'           => date('Y-m-d'),
    'time'           => date('H:i:s'),
    'customer'       => $customer !== '' ? $customer : 'Guest',
    'payment'        => $payment,
    'subtotal'       => $subtotal,
    'discount'       => $discount,
    'tax'            => $tax,
    'total'          => $total,
    'paid'           => $paid,
    'change'         => $kembalian,
    'items'          => $items
];

// Simpan dalam format JSON per baris
$jsonLine = json_encode($transData) . "\n";
file_put_contents($fileTransaksi, $jsonLine, FILE_APPEND | LOCK_EX);

// ============================================
// TAMPILKAN STRUK
// ============================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt - <?php echo $transactionNo; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/struk.css">
    
</head>
<body>
  <div class="receipt-container">
    
    <div class="receipt-paper">
      
      <!-- Header -->
      <div class="receipt-header">
        <div class="brand-name">Kopi Murah Cuy</div>
        <div class="brand-tagline">Coffee & Cake</div>
        <div class="transaction-number"><?php echo $transactionNo; ?></div>
      </div>

      <!-- Info Grid -->
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Customer</span>
          <span class="info-value"><?php echo $customer !== '' ? htmlspecialchars($customer) : 'Guest'; ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Payment</span>
          <span class="info-value"><?php echo htmlspecialchars($paymentTxt); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Date</span>
          <span class="info-value"><?php echo date("d/m/Y"); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Time</span>
          <span class="info-value"><?php echo date("H:i:s"); ?></span>
        </div>
      </div>

      <!-- Items Section -->
      <div class="items-section">
        <h3 class="section-title">Order Details</h3>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <div class="item-name"><?php echo htmlspecialchars($it['name']); ?></div>
                <span class="item-category"><?php echo htmlspecialchars($it['category']); ?></span>
              </td>
              <td><?php echo rupiah($it['price']); ?></td>
              <td><?php echo (int)$it['qty']; ?></td>
              <td><?php echo rupiah($it['total']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
          <div class="summary-row subtotal">
            <span>Subtotal</span>
            <span><?php echo rupiah($subtotal); ?></span>
          </div>
          
          <?php if ($discRate > 0): ?>
          <div class="summary-row discount">
            <span>Discount (<?php echo htmlspecialchars($voucherName); ?>)</span>
            <span>-<?php echo rupiah($discount); ?></span>
          </div>
          <?php endif; ?>
          
          <div class="summary-row tax">
            <span>Tax (11%)</span>
            <span><?php echo rupiah($tax); ?></span>
          </div>
          
          <div class="summary-row total">
            <span>Total</span>
            <span><?php echo rupiah($total); ?></span>
          </div>
          
          <div class="summary-row paid">
            <span>Amount Paid</span>
            <span><?php echo rupiah($paid); ?></span>
          </div>
          
          <div class="summary-row <?php echo $kembalian >= 0 ? 'change' : 'kurang-bayar'; ?>">
            <span><?php echo $kembalian >= 0 ? 'Change' : 'Insufficient'; ?></span>
            <span><?php echo $kembalian >= 0 ? rupiah($kembalian) : rupiah($kurangBayar); ?></span>
          </div>
        </div>
      </div>

      <!-- Payment Status -->
      <div class="payment-status <?php echo $statusClass; ?>">
        <?php echo $paymentStatus; ?>
      </div>

      <?php if ($discRate === 0.0 && $voucher !== ''): ?>
      <div class="voucher-notice">
        <p>⚠️ Voucher code <strong><?php echo htmlspecialchars($voucher); ?></strong> not recognized.</p>
        <p>💡 Available codes: <strong>HEMAT10</strong> or <strong>MAHASISWA5</strong></p>
      </div>
      <?php endif; ?>

      <!-- Footer -->
      <div class="receipt-footer">
        <div class="thank-you">Thank You</div>
        <div class="rating-stars">★★★★★</div>
        <p>We hope you enjoyed your experience</p>
        <p>Visit us again soon at Interlude Coffee & Tea</p>
        <p style="margin-top: 20px; font-size: 11px; color: #aaa;">Powered by Coffee Shop POS System</p>
      </div>

    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <a href="form_kasir_sederhana.php" class="btn btn-new">← New Transaction</a>
      <button onclick="window.print()" class="btn btn-print">Print Receipt</button>
    </div>

  </div>
</body>
</html>