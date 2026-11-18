<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string {
    // Menentukan folder utama project
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));

    // Jika sedang berada di folder admin,
    // naik satu tingkat ke folder utama project.
    if (preg_match('~/admin$~', $scriptDir)) {
        $scriptDir = dirname($scriptDir);
    }

    $base = rtrim($scriptDir, '/');

    if ($base === '.' || $base === '/') {
        $base = '';
    }

    return $base . '/' . ltrim($path, '/');
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}
function is_admin(): bool {
    return isset($_SESSION['admin_id']);
}
function require_admin(): void {
    if (!is_admin()) redirect(base_url('admin/login.php'));
}
function excerpt(string $text, int $length = 130): string {
    $text = trim(strip_tags($text));
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '…' : $text;
}
function upload_image(array $file): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('Upload gambar gagal.');
    if (($file['size'] ?? 0) > 3 * 1024 * 1024) throw new RuntimeException('Ukuran gambar maksimal 3 MB.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $target = dirname(__DIR__) . '/uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) throw new RuntimeException('Tidak dapat menyimpan gambar.');
    return $name;
}
function delete_image(?string $name): void {
    if (!$name) return;
    $path = dirname(__DIR__) . '/uploads/' . basename($name);
    if (is_file($path)) unlink($path);
}
