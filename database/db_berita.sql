CREATE DATABASE IF NOT EXISTS db_berita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_berita;

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin (
  id_admin INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berita (
  id_berita INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT UNSIGNED NOT NULL,
  judul VARCHAR(200) NOT NULL,
  isi_berita LONGTEXT NOT NULL,
  gambar VARCHAR(255) DEFAULT NULL,
  tanggal_posting DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  penulis VARCHAR(100) NOT NULL,
  status ENUM('draft','publish') NOT NULL DEFAULT 'publish',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_berita_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_berita_tanggal (tanggal_posting),
  INDEX idx_berita_status (status)
) ENGINE=InnoDB;

INSERT IGNORE INTO kategori (nama_kategori) VALUES
('Nasional'),('Daerah'),('Teknologi'),('Pendidikan'),('Olahraga'),('Hiburan');

-- Akun awal: username admin, password admin123
-- Password disimpan dalam bentuk hash bcrypt.
INSERT IGNORE INTO admin (username,password) VALUES
('admin', '$2y$12$wl71BlSimY9zV9HcA6G.3ut2f8Asd1R/1vt2hAtIXB9Czga7DHk5W');
