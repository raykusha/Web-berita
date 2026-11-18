<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../config/helpers.php'; require_admin();
$action=$_GET['action']??'list'; $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:0; $error='';
if($action==='hapus' && $id){
    $s=$pdo->prepare("SELECT gambar FROM berita WHERE id_berita=?");$s->execute([$id]);$old=$s->fetch();
    $pdo->prepare("DELETE FROM berita WHERE id_berita=?")->execute([$id]); if($old) delete_image($old['gambar']);
    redirect(base_url('admin/berita.php?deleted=1'));
}
$item=['judul'=>'','id_kategori'=>'','isi_berita'=>'','gambar'=>null,'penulis'=>$_SESSION['admin_username']??'Admin','status'=>'publish','is_featured'=>0];
if($action==='edit' && $id){$s=$pdo->prepare("SELECT * FROM berita WHERE id_berita=?");$s->execute([$id]);$item=$s->fetch()?:$item;}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $judul=trim($_POST['judul']??'');$isi=trim($_POST['isi_berita']??'');$kategori=(int)($_POST['id_kategori']??0);
    if($judul===''||$isi===''||!$kategori){$error='Judul, kategori, dan isi berita wajib diisi.';}
    else {
      try {
        $gambar=upload_image($_FILES['gambar']??[]);
        $data=[$kategori,$judul,$isi,$gambar??($item['gambar']??null),trim($_POST['penulis']??'Admin'),$_POST['status']==='draft'?'draft':'publish',isset($_POST['is_featured'])?1:0];
        if($action==='edit'&&$id){
          $pdo->prepare("UPDATE berita SET id_kategori=?,judul=?,isi_berita=?,gambar=?,penulis=?,status=?,is_featured=? WHERE id_berita=?")->execute([...$data,$id]);
          if($gambar) delete_image($item['gambar']);
        } else {
          $pdo->prepare("INSERT INTO berita (id_kategori,judul,isi_berita,gambar,penulis,status,is_featured,tanggal_posting) VALUES (?,?,?,?,?,?,?,NOW())")->execute($data);
        }
        redirect(base_url('admin/berita.php?saved=1'));
      } catch(Throwable $ex){$error=$ex->getMessage();}
    }
    $item=array_merge($item,['judul'=>$judul,'id_kategori'=>$kategori,'isi_berita'=>$isi]);
}
$cats=$pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$pageTitle=($action==='tambah'?'Tulis Berita':($action==='edit'?'Edit Berita':'Kelola Berita'));
require __DIR__.'/layout_top.php';
if(in_array($action,['tambah','edit'])):
?>
<div class="admin-panel form-panel">
<?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="admin-form">
<label>Judul berita<input name="judul" value="<?= e($item['judul']) ?>" required maxlength="200"></label>
<label>Kategori<select name="id_kategori" required><option value="">Pilih kategori</option><?php foreach($cats as $c): ?><option value="<?= $c['id_kategori'] ?>" <?= $item['id_kategori']==$c['id_kategori']?'selected':'' ?>><?= e($c['nama_kategori']) ?></option><?php endforeach; ?></select></label>
<label>Isi berita<textarea name="isi_berita" rows="12" required><?= e($item['isi_berita']) ?></textarea></label>
<label>Penulis<input name="penulis" value="<?= e($item['penulis']) ?>" required></label>
<label>Gambar (JPG/PNG/WEBP, maks. 3 MB)<input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"></label>
<?php if($item['gambar']): ?><img class="form-preview" src="<?= base_url('uploads/'.rawurlencode($item['gambar'])) ?>" alt="Gambar saat ini"><?php endif; ?>
<label>Status<select name="status"><option value="publish" <?= $item['status']==='publish'?'selected':'' ?>>Publish</option><option value="draft" <?= $item['status']==='draft'?'selected':'' ?>>Draft</option></select></label>
<label class="check-label"><input type="checkbox" name="is_featured" value="1" <?= !empty($item['is_featured'])?'checked':'' ?>> Jadikan berita utama</label>
<div class="form-actions"><button class="btn btn-primary">Simpan berita</button><a class="btn btn-muted" href="<?= base_url('admin/berita.php') ?>">Batal</a></div>
</form></div>
<?php else:
$rows=$pdo->query("SELECT b.*,k.nama_kategori FROM berita b LEFT JOIN kategori k ON k.id_kategori=b.id_kategori ORDER BY b.id_berita DESC")->fetchAll(); ?>
<div class="admin-panel"><div class="panel-heading"><h2>Daftar artikel</h2><a class="btn btn-primary" href="?action=tambah">+ Tulis berita</a></div>
<?php if(isset($_GET['saved'])): ?><div class="success">Berita berhasil disimpan.</div><?php endif; ?><?php if(isset($_GET['deleted'])): ?><div class="success">Berita berhasil dihapus.</div><?php endif; ?>
<div class="table-scroll"><table><thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['judul']) ?></td><td><?= e($r['nama_kategori']??'-') ?></td><td><span class="status <?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td><td><?= date('d/m/Y',strtotime($r['tanggal_posting'])) ?></td><td class="actions"><a href="?action=edit&id=<?= $r['id_berita'] ?>">Edit</a><a class="danger" onclick="return confirm('Hapus berita ini?')" href="?action=hapus&id=<?= $r['id_berita'] ?>">Hapus</a></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="5">Belum ada artikel.</td></tr><?php endif; ?></tbody></table></div></div>
<?php endif; require __DIR__.'/layout_bottom.php'; ?>
