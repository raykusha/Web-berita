<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
if (is_admin()) redirect(base_url('admin/index.php'));
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $stmt=$pdo->prepare("SELECT * FROM admin WHERE username=? LIMIT 1");
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $admin=$stmt->fetch();
    if ($admin && password_verify($_POST['password'] ?? '', $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']=$admin['id_admin']; $_SESSION['admin_username']=$admin['username'];
        redirect(base_url('admin/index.php'));
    }
    $error='Username atau password salah.';
}
$pageTitle='Login Admin';
require __DIR__.'/../partials/header.php';
?>
<main class="container login-wrap"><form class="login-card" method="post">
 <span class="eyebrow">AREA PENGELOLA</span><h1>Masuk Admin</h1><p>Kelola berita dan kategori KabarKita.</p>
 <?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
 <label>Username<input name="username" required autocomplete="username"></label>
 <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
 <button class="btn btn-primary full" type="submit">Masuk ke dashboard</button>
 <a class="back-link" href="<?= base_url() ?>">← Kembali ke website</a>
</form></main>
<?php require __DIR__.'/../partials/footer.php'; ?>
