<?php
if (!isset($pageTitle)) $pageTitle = 'Portal Berita';
$navCategories = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portal berita terkini dan terpercaya.">
  <title><?= e($pageTitle) ?> - KabarKita</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>
<body>
<header class="site-header">
  <div class="topline"><div class="container top-inner"><span>Informasi aktual, inspirasi tanpa batas.</span><span><?= date('l, d F Y') ?></span></div></div>
  <div class="container nav-wrap">
    <a class="brand" href="<?= base_url() ?>"><span class="brand-mark">K</span><span>Kabar<span class="brand-accent">Kita</span><small>PORTAL BERITA</small></span></a>
    <button class="menu-toggle" aria-label="Buka menu" aria-expanded="false">☰</button>
    <nav class="main-nav">
      <a href="<?= base_url() ?>">Beranda</a>
      <?php foreach ($navCategories as $cat): ?><a href="<?= base_url('index.php?kategori='.$cat['id_kategori']) ?>"><?= e($cat['nama_kategori']) ?></a><?php endforeach; ?>
      <a class="admin-link" href="<?= base_url('admin/login.php') ?>">Admin ↗</a>
    </nav>
    <form class="search-form" action="<?= base_url() ?>" method="get"><input type="search" name="q" placeholder="Cari berita..." value="<?= e($_GET['q'] ?? '') ?>"><button aria-label="Cari">⌕</button></form>
  </div>
</header>
