<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../config/helpers.php'; require_admin();
$totalBerita=(int)$pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$totalKategori=(int)$pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalAdmin=(int)$pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
$recent=$pdo->query("SELECT b.id_berita,b.judul,b.status,b.tanggal_posting,k.nama_kategori FROM berita b LEFT JOIN kategori k ON k.id_kategori=b.id_kategori ORDER BY b.id_berita DESC LIMIT 7")->fetchAll();
$pageTitle='Dashboard Admin'; require __DIR__.'/layout_top.php';
?>
<div class="admin-stats">
 <div class="stat-card"><span>Total Berita</span><strong><?= $totalBerita ?></strong><small>Artikel tersimpan</small></div>
 <div class="stat-card"><span>Kategori</span><strong><?= $totalKategori ?></strong><small>Kategori berita</small></div>
 <div class="stat-card"><span>Admin</span><strong><?= $totalAdmin ?></strong><small>Akun pengelola</small></div>
</div>
<div class="admin-panel"><div class="panel-heading"><h2>Berita terbaru</h2><a class="btn btn-primary" href="<?= base_url('admin/berita.php?action=tambah') ?>">+ Tulis berita</a></div>
<div class="table-scroll"><table><thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?= e($r['judul']) ?></td><td><?= e($r['nama_kategori']??'-') ?></td><td><?= date('d/m/Y',strtotime($r['tanggal_posting'])) ?></td><td><span class="status <?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td><td><a href="<?= base_url('admin/berita.php?action=edit&id='.$r['id_berita']) ?>">Edit</a></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="5">Belum ada berita.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__.'/layout_bottom.php'; ?>
