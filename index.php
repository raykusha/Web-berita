<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

$q = trim($_GET['q'] ?? '');
$kategoriId = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT) ?: 0;
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$where = ["b.status = 'publish'"];
$params = [];
if ($q !== '') {
    $where[] = '(b.judul LIKE ? OR b.isi_berita LIKE ? OR b.penulis LIKE ?)';
    $params = array_merge($params, ["%$q%", "%$q%", "%$q%"]);
}
if ($kategoriId) {
    $where[] = 'b.id_kategori = ?';
    $params[] = $kategoriId;
}
$whereSql = implode(' AND ', $where);
$count = $pdo->prepare("SELECT COUNT(*) FROM berita b WHERE $whereSql");
$count->execute($params);
$total = (int)$count->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT b.*, k.nama_kategori FROM berita b
    LEFT JOIN kategori k ON k.id_kategori=b.id_kategori
    WHERE $whereSql ORDER BY b.tanggal_posting DESC, b.id_berita DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$berita = $stmt->fetchAll();
$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$headline = $pdo->query("SELECT b.*, k.nama_kategori FROM berita b LEFT JOIN kategori k ON k.id_kategori=b.id_kategori
    WHERE b.status='publish' ORDER BY b.is_featured DESC, b.tanggal_posting DESC LIMIT 1")->fetch();
$pageTitle = 'Beranda';
require __DIR__ . '/partials/header.php';
?>
<main class="container">
  <?php if ($headline && !$q && !$kategoriId && $page === 1): ?>
  <section class="hero">
    <div class="hero-copy">
      <span class="eyebrow">BERITA UTAMA · <?= e($headline['nama_kategori'] ?? 'Umum') ?></span>
      <h1><?= e($headline['judul']) ?></h1>
      <p><?= e(excerpt($headline['isi_berita'], 180)) ?></p>
      <a class="btn btn-light" href="<?= base_url('detail.php?id=' . $headline['id_berita']) ?>">Baca selengkapnya <span>→</span></a>
    </div>
    <?php if ($headline['gambar']): ?><img src="<?= base_url('uploads/' . rawurlencode($headline['gambar'])) ?>" alt="<?= e($headline['judul']) ?>"><?php else: ?><div class="hero-placeholder">KABAR<br>TERKINI</div><?php endif; ?>
  </section>
  <?php endif; ?>

  <div class="section-heading">
    <div><span class="eyebrow">UPDATE TERBARU</span><h2><?= $q ? 'Hasil pencarian' : ($kategoriId ? 'Berita kategori' : 'Berita pilihan') ?></h2></div>
    <?php if ($q): ?><p>Pencarian: <b><?= e($q) ?></b></p><?php endif; ?>
  </div>
  <?php if (!$berita): ?>
    <div class="empty-state"><h3>Belum ada berita ditemukan</h3><p>Coba kata kunci lain atau pilih kategori berbeda.</p><a href="<?= base_url() ?>">Kembali ke beranda</a></div>
  <?php else: ?>
  <div class="news-grid">
    <?php foreach ($berita as $item): ?>
    <article class="news-card">
      <a class="thumb" href="<?= base_url('detail.php?id=' . $item['id_berita']) ?>">
        <?php if ($item['gambar']): ?><img src="<?= base_url('uploads/' . rawurlencode($item['gambar'])) ?>" alt="<?= e($item['judul']) ?>"><?php else: ?><div class="thumb-placeholder">KABAR HARI INI</div><?php endif; ?>
      </a>
      <div class="card-body">
        <span class="category"><?= e($item['nama_kategori'] ?? 'Umum') ?></span>
        <h3><a href="<?= base_url('detail.php?id=' . $item['id_berita']) ?>"><?= e($item['judul']) ?></a></h3>
        <p><?= e(excerpt($item['isi_berita'])) ?></p>
        <div class="meta"><?= e($item['penulis']) ?> <span>·</span> <?= date('d M Y', strtotime($item['tanggal_posting'])) ?></div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php if ($pages > 1): ?>
  <nav class="pagination">
    <?php for ($i=1; $i<=$pages; $i++): ?>
      <a class="<?= $i===$page?'active':'' ?>" href="?<?= http_build_query(array_filter(['q'=>$q,'kategori'=>$kategoriId,'page'=>$i])) ?>"><?= $i ?></a>
    <?php endfor; ?>
  </nav>
  <?php endif; endif; ?>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
