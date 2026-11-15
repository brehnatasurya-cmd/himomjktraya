<?php
require_once __DIR__ . '/../api/helpers/auth.php';

initSecureSession();

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Administrator | HIMO Mojokerto Raya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/admin.css" />
  <style>
    body {
      background: radial-gradient(circle at 50% 30%, #174A7E 0%, #0B1F3A 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }
    .login-box {
      background: #FFFFFF;
      border-radius: var(--admin-r-lg);
      padding: 44px 36px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
    }
    .login-brand {
      text-align: center;
      margin-bottom: 32px;
    }
    .login-brand img {
      width: 64px;
      height: 64px;
      margin-bottom: 14px;
    }
    .login-brand h1 {
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--admin-navy);
      letter-spacing: -0.02em;
    }
    .login-brand p {
      font-size: 0.85rem;
      color: var(--admin-text-soft);
      margin-top: 4px;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <div class="login-brand">
      <img src="../Logo Himo Mojokerto.PNG" alt="Logo HIMO" />
      <h1>Portal Admin HIMO</h1>
      <p>Sistem Manajemen Konten & Kepengurusan</p>
    </div>

    <div id="alert-box" class="alert alert-danger" style="display: none;"></div>

    <form id="login-form">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>" />

      <div class="form-group">
        <label class="form-label" for="email">Alamat Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="admin@himo.id" required autofocus autocomplete="email" />
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password" />
      </div>

      <button type="submit" id="btn-submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px;">
        <i class="fas fa-lock"></i> Masuk ke Dashboard
      </button>
    </form>

    <div style="text-align: center; margin-top: 28px; border-top: 1px solid var(--admin-border); padding-top: 16px;">
      <a href="../index.html" style="font-size: 0.84rem; color: var(--admin-text-soft); text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Kembali ke Website Utama
      </a>
    </div>
  </div>

  <script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const alertBox = document.getElementById('alert-box');
      const submitBtn = document.getElementById('btn-submit');

      alertBox.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';

      const formData = new FormData(this);

      try {
        const response = await fetch('../api/auth/login.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          window.location.href = data.data.redirect || 'dashboard.php';
        } else {
          alertBox.textContent = data.message || 'Login gagal. Periksa email dan password.';
          alertBox.style.display = 'flex';
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fas fa-lock"></i> Masuk ke Dashboard';
        }
      } catch (err) {
        alertBox.textContent = 'Terjadi kesalahan jaringan atau server tidak merespons.';
        alertBox.style.display = 'flex';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock"></i> Masuk ke Dashboard';
      }
    });
  </script>
</body>
</html>
