-- Insert data awal
USE inventory_system;

-- Insert user admin (password: admin123)
INSERT INTO users (username, password, nama_lengkap, role) VALUES 
('admin', '$2y$10$hOs6NQMMGEhW/8j/k6Ns3.xU6vaSpNZYWEgPLvWc/ZmGAsJf7ulse', 'Administrator', 'admin'),
('user', '$2y$10$dC0NIwbcy2mcTZQHcBokO.0CGp5argBt2gSTreqFoKeaeF.vld0Iq', 'Pengguna', 'user');

-- Insert kategori
INSERT INTO kategori (nama_kategori, deskripsi) VALUES 
('Elektronik', 'Barang-barang elektronik'),
('Alat Tulis', 'Perlengkapan tulis kantor'),
('Furniture', 'Perabotan kantor'),
('Konsumsi', 'Makanan dan minuman');

-- Insert supplier
INSERT INTO supplier (nama_supplier, alamat, telepon, email) VALUES 
('PT. Elektronik Jaya', 'Jl. Sudirman No. 123, Jakarta', '021-1234567', 'info@elektronikjaya.com'),
('CV. Alat Tulis Mandiri', 'Jl. Gatot Subroto No. 456, Bandung', '022-7654321', 'sales@alattulis.com'),
('Toko Furniture Modern', 'Jl. Ahmad Yani No. 789, Surabaya', '031-9876543', 'order@furnituremodern.com');

-- Insert barang
INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, stok, harga_beli, harga_jual, satuan, deskripsi) VALUES 
('ELK001', 'Laptop ASUS', 1, 1, 10, 8000000, 9500000, 'Unit', 'Laptop ASUS Core i5'),
('ATK001', 'Pulpen Pilot', 2, 2, 100, 2000, 3000, 'Pcs', 'Pulpen pilot hitam'),
('FUR001', 'Meja Kantor', 3, 3, 5, 500000, 750000, 'Unit', 'Meja kantor kayu jati'),
('KON001', 'Air Mineral', 4, 1, 50, 3000, 5000, 'Botol', 'Air mineral 600ml');
