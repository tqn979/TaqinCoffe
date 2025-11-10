<?php
// ---- SETUP PENYIMPANAN ----
$dirData  = __DIR__ . '/data';
$fileUser = $dirData . '/users.txt';
if (!is_dir($dirData)) { mkdir($dirData, 0777, true); }
if (!file_exists($fileUser)) { touch($fileUser); }

$msgNama = $msgEmail = $msgPassword = $msgPassword2 = '';
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $pass   = $_POST['password'] ?? '';
    $pass2  = $_POST['password2'] ?? '';

    $valid = true;

    // Validasi nama
    if ($nama === '') {
        $msgNama = 'Nama wajib diisi.';
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $msgNama = 'Nama hanya boleh mengandung huruf dan spasi.';
        $valid = false;
    }

    // Validasi email
    if ($email === '') {
        $msgEmail = 'Email wajib diisi.';
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msgEmail = 'Format email tidak valid.';
        $valid = false;
    } else {
        // cek duplikasi email
        $exists = false;
        $fh = fopen($fileUser, 'r');
        if ($fh) {
            while (($line = fgets($fh)) !== false) {
                $line = trim($line);
                if ($line === '') continue;
                list($e,) = explode(';', $line, 2);
                if ($e === $email) { $exists = true; break; }
            }
            fclose($fh);
        }

        if ($exists) {
            $msgEmail = 'Email sudah terdaftar.';
            $valid = false;
        }
    }

    // Validasi password
    if ($pass === '') {
        $msgPassword = 'Password wajib diisi.';
        $valid = false;
    } elseif (strlen($pass) < 8) {
        $msgPassword = 'Password minimal 8 karakter.';
        $valid = false;
    }

    // Validasi konfirmasi password
    if ($pass2 === '') {
        $msgPassword2 = 'Konfirmasi password wajib diisi.';
        $valid = false;
    } elseif ($pass !== $pass2) {
        $msgPassword2 = 'Konfirmasi password tidak cocok.';
        $valid = false;
    }

    // Jika valid, simpan ke file
    if ($valid) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $fhw = fopen($fileUser, 'a');
        if ($fhw && flock($fhw, LOCK_EX)) {
            fwrite($fhw, $email . ';' . $nama . ';' . $hash . PHP_EOL);
            flock($fhw, LOCK_UN);
            fclose($fhw);
            header("Location: login.php?success=1");
            exit;
        } else {
            $msgEmail = 'Gagal menyimpan data. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Karyawan CoffeeShop</title>
  <link rel="stylesheet" href="css/registerr.css">
</head>
<body>
  <h2>Registrasi Karyawan CoffeeShop</h2>

  <form method="POST" action="">
    <div class="form-header">
      <h2>Daftar Akun</h2>
      <p>Lengkapi data berikut untuk registrasi.</p>
    </div>

    <p>
      <label>Nama:</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
      <?php if ($msgNama): ?><span class="error-msg"><?= $msgNama ?></span><?php endif; ?>
    </p>

    <p>
      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      <?php if ($msgEmail): ?><span class="error-msg"><?= $msgEmail ?></span><?php endif; ?>
    </p>

    <p>
      <label>Password:</label>
      <input type="password" name="password" required>
      <?php if ($msgPassword): ?><span class="error-msg"><?= $msgPassword ?></span><?php endif; ?>
    </p>

    <p>
      <label>Konfirmasi Password:</label>
      <input type="password" name="password2" required>
      <?php if ($msgPassword2): ?><span class="error-msg"><?= $msgPassword2 ?></span><?php endif; ?>
    </p>

    <button type="submit">Daftar</button>
    <a href="login.php">Sudah punya akun? Login di sini</a>
  </form>
</body>
</html>
