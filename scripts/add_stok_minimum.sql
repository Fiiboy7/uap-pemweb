-- Menambahkan kolom stok_minimum ke tabel barang
ALTER TABLE barang ADD COLUMN stok_minimum INT DEFAULT 10 AFTER stok;

-- Update data yang sudah ada dengan nilai default
UPDATE barang SET stok_minimum = 10 WHERE stok_minimum IS NULL;
