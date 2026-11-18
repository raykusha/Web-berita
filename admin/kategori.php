<?php
require_once __DIR__.'/../config/database.php';require_once __DIR__.'/../config/helpers.php';require_admin();
$error='';
if(isset($_GET['hapus'])){$id=(int)$_GET['hapus'];$s=$pdo->prepare("SELECT COUNT(*) FROM berita WHERE id_kategori=?");$s->execute([$id]);if((int)$s->fetchColumn()>0){$error='Kategori masih digunakan berita. Pindahkan atau hapus berita terkait terlebih dahulu.';}else{$pdo->prepare("DELETE FROM kategori WHERE id_kategori=?")->execute([$id]);redirect(base_url('admin/kategori.php?deleted=1'));}}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM kategori WHERE id_kategori=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['nama_kategori']??'');$id=(int)($_POST['id_kategori']??0);if($name==='')$error='Nama kategori wajib diisi.';else{try{if($id)$pdo->prepare("UPDATE kategori SET nama_kategori=? WHERE id_kategori=?")->execute([$name,$id]);else $pdo->prepare("INSERT INTO kategori(nama_kategori) VALUES(?)")->execute([$name]);redirect(base_url('admin/kategori.php?saved=1'));}catch(PDOException $ex){$error='Nama kategori sudah digunakan.';}}}
$rows=$pdo->query("SELECT k.*,COUNT(b.id_berita) jumlah FROM kategori k LEFT JOIN berita b ON b.id_kategori=k.id_kategori GROUP BY k.id_kategori ORDER BY k.nama_kategori")->fetchAll();
$pageTitle='Kelola Kategori';require __DIR__.'/layout_top.php';
?>
<div class="admin-panel form-panel"><h2><?= $edit?'Edit kategori':'Tambah kategori' ?></h2><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="inline-form"><input type="hidden" name="id_kategori" value="<?= e((string)($edit['id_kategori']??'')) ?>"><input name="nama_kategori" placeholder="Contoh: Teknologi" value="<?= e($edit['nama_kategori']??'') ?>" required><button class="btn btn-primary">Simpan</button><?php if($edit): ?><a class="btn btn-muted" href="<?= base_url('admin/kategori.php') ?>">Batal</a><?php endif; ?></form></div>
<div class="admin-panel"><h2>Daftar kategori</h2><?php if(isset($_GET['saved'])): ?><div class="success">Kategori berhasil disimpan.</div><?php endif; ?><?php if(isset($_GET['deleted'])): ?><div class="success">Kategori berhasil dihapus.</div><?php endif; ?>
<div class="table-scroll"><table><thead><tr><th>Nama kategori</th><th>Jumlah berita</th><th>Aksi</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['nama_kategori']) ?></td><td><?= $r['jumlah'] ?></td><td class="actions"><a href="?edit=<?= $r['id_kategori'] ?>">Edit</a><a class="danger" onclick="return confirm('Hapus kategori ini?')" href="?hapus=<?= $r['id_kategori'] ?>">Hapus</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require __DIR__.'/layout_bottom.php'; ?>
