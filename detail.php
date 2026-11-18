<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT b.*, k.nama_kategori FROM berita b LEFT JOIN kategori k ON k.id_kategori=b.id_kategori WHERE b.id_berita=? AND b.status='publish'");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { http_response_code(404); $pageTitle='Berita tidak ditemukan'; require __DIR__.'/partials/header.php'; echo '<main class="container empty-state"><h2>Berita tidak ditemukan</h2><a href="'.base_url().'">Kembali</a></main>'; require __DIR__.'/partials/footer.php'; exit; }
$pageTitle = $item['judul'];
$related = $pdo->prepare("SELECT id_berita,judul,gambar FROM berita WHERE status='publish' AND id_berita<>? AND id_kategori=? ORDER BY tanggal_posting DESC LIMIT 3");
$related->execute([$id,$item['id_kategori']]);
require __DIR__ . '/partials/header.php';
?>
<main class="container article-layout">
 <article class="article">
   <a class="back-link" href="<?= base_url() ?>">← Kembali ke beranda</a>
   <span class="category"><?= e($item['nama_kategori'] ?? 'Umum') ?></span>
   <h1><?= e($item['judul']) ?></h1>
   <div class="article-meta">Ditulis oleh <b><?= e($item['penulis']) ?></b> <span>·</span> <?= date('d F Y, H:i', strtotime($item['tanggal_posting'])) ?></div>
   <?php if ($item['gambar']): ?><img class="article-cover" src="<?= base_url('uploads/'.rawurlencode($item['gambar'])) ?>" alt="<?= e($item['judul']) ?>"><?php endif; ?>
   <div class="article-content"><?= nl2br(e($item['isi_berita'])) ?></div>
 </article>
 <aside class="sidebar"><h3>Berita terkait</h3>
 <?php foreach ($related as $r): ?><a class="related-item" href="<?= base_url('detail.php?id='.$r['id_berita']) ?>"><?php if ($r['gambar']): ?><img src="<?= base_url('uploads/'.rawurlencode($r['gambar'])) ?>" alt=""><?php else: ?><div class="related-placeholder">K</div><?php endif; ?><b><?= e($r['judul']) ?></b></a><?php endforeach; ?>
 </aside>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
