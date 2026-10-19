-- InternTrack Database
-- Sistem Monitoring Kegiatan PKL Siswa
CREATE DATABASE IF NOT EXISTS interntrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE interntrack;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
    related_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE perusahaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    bidang VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pembimbing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(30) DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE penempatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    perusahaan_id INT NOT NULL,
    pembimbing_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    status ENUM('Belum Mulai','Aktif','Selesai') NOT NULL DEFAULT 'Belum Mulai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id) ON DELETE CASCADE,
    FOREIGN KEY (pembimbing_id) REFERENCES pembimbing(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jurnal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    kegiatan TEXT NOT NULL,
    hasil TEXT DEFAULT NULL,
    kendala TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Hadir','Izin','Sakit','Alpa') NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_absensi (siswa_id, tanggal),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE monitoring (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    pembimbing_id INT NOT NULL,
    tanggal_monitoring DATE NOT NULL,
    catatan TEXT NOT NULL,
    nilai INT DEFAULT NULL,
    rekomendasi TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (pembimbing_id) REFERENCES pembimbing(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password untuk semua: password123
-- Hash: $2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q

INSERT INTO users (username, password, role, related_id) VALUES
('admin', '$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q', 'admin', NULL);

INSERT INTO siswa (nis, nama, kelas, jurusan, no_hp, alamat) VALUES
('2024001','Ahmad Fauzi','XII RPL 1','Rekayasa Perangkat Lunak','081234567001','Jl. Merdeka No.1 Semarang'),
('2024002','Siti Aminah','XII RPL 1','Rekayasa Perangkat Lunak','081234567002','Jl. Sudirman No.2 Semarang'),
('2024003','Budi Santoso','XII RPL 1','Rekayasa Perangkat Lunak','081234567003','Jl. Gajah Mada No.3 Semarang'),
('2024004','Dewi Lestari','XII RPL 1','Rekayasa Perangkat Lunak','081234567004','Jl. Pemuda No.4 Semarang'),
('2024005','Eko Prasetyo','XII RPL 1','Rekayasa Perangkat Lunak','081234567005','Jl. Ahmad Yani No.5 Semarang'),
('2024006','Fitri Handayani','XII RPL 2','Rekayasa Perangkat Lunak','081234567006','Jl. Diponegoro No.6 Semarang'),
('2024007','Gilang Ramadhan','XII RPL 2','Rekayasa Perangkat Lunak','081234567007','Jl. Kartini No.7 Semarang'),
('2024008','Hana Putri','XII RPL 2','Rekayasa Perangkat Lunak','081234567008','Jl. Imam Bonjol No.8 Semarang'),
('2024009','Irfan Maulana','XII RPL 2','Rekayasa Perangkat Lunak','081234567009','Jl. Pahlawan No.9 Semarang'),
('2024010','Joko Widodo','XII RPL 2','Rekayasa Perangkat Lunak','081234567010','Jl. Veteran No.10 Semarang'),
('2024011','Kartika Sari','XII RPL 1','Rekayasa Perangkat Lunak','081234567011','Jl. Cendrawasih No.11 Semarang'),
('2024012','Lukman Hakim','XII RPL 1','Rekayasa Perangkat Lunak','081234567012','Jl. Melati No.12 Semarang'),
('2024013','Maya Indah','XII RPL 1','Rekayasa Perangkat Lunak','081234567013','Jl. Mawar No.13 Semarang'),
('2024014','Nurul Hidayah','XII RPL 2','Rekayasa Perangkat Lunak','081234567014','Jl. Anggrek No.14 Semarang'),
('2024015','Omar Abdullah','XII RPL 2','Rekayasa Perangkat Lunak','081234567015','Jl. Kenanga No.15 Semarang'),
('2024016','Putri Ayu','XII RPL 1','Rekayasa Perangkat Lunak','081234567016','Jl. Dahlia No.16 Semarang'),
('2024017','Qori Ananda','XII RPL 2','Rekayasa Perangkat Lunak','081234567017','Jl. Flamboyan No.17 Semarang'),
('2024018','Rizki Aditya','XII RPL 1','Rekayasa Perangkat Lunak','081234567018','Jl. Bougenville No.18 Semarang'),
('2024019','Salsa Bilah','XII RPL 2','Rekayasa Perangkat Lunak','081234567019','Jl. Teratai No.19 Semarang'),
('2024020','Taufik Hidayat','XII RPL 1','Rekayasa Perangkat Lunak','081234567020','Jl. Kamboja No.20 Semarang'),
('2024021','Umi Kulsum','XII RPL 2','Rekayasa Perangkat Lunak','081234567021','Jl. Sakura No.21 Semarang'),
('2024022','Vina Melati','XII RPL 1','Rekayasa Perangkat Lunak','081234567022','Jl. Tulip No.22 Semarang'),
('2024023','Wahyu Nugroho','XII RPL 2','Rekayasa Perangkat Lunak','081234567023','Jl. Lily No.23 Semarang'),
('2024024','Xenia Putri','XII RPL 1','Rekayasa Perangkat Lunak','081234567024','Jl. Orchid No.24 Semarang'),
('2024025','Yusuf Rahman','XII RPL 2','Rekayasa Perangkat Lunak','081234567025','Jl. Lotus No.25 Semarang');

INSERT INTO users (username, password, role, related_id) VALUES
('2024001','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',1),
('2024002','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',2),
('2024003','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',3),
('2024004','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',4),
('2024005','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',5),
('2024006','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',6),
('2024007','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',7),
('2024008','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',8),
('2024009','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',9),
('2024010','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',10),
('2024011','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',11),
('2024012','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',12),
('2024013','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',13),
('2024014','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',14),
('2024015','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',15),
('2024016','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',16),
('2024017','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',17),
('2024018','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',18),
('2024019','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',19),
('2024020','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',20),
('2024021','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',21),
('2024022','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',22),
('2024023','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',23),
('2024024','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',24),
('2024025','$2y$10$L6jYYfFzW5GJ/N2fbSGXGOjH1gT/h7gYrxhX/VEDz3Ofu8iLgt85q','siswa',25);

INSERT INTO perusahaan (nama_perusahaan, alamat, bidang, kontak) VALUES
('PT Teknologi Maju','Jl. Industri No.1 Semarang','Software Development','024-1234567'),
('CV Digital Kreatif','Jl. Teknologi No.2 Semarang','Web Development','024-2345678'),
('PT Solusi Informatika','Jl. Komputer No.3 Semarang','IT Consulting','024-3456789'),
('PT Data Analytics Indo','Jl. Big Data No.4 Semarang','Data Science','024-4567890'),
('CV Mobile Apps Studio','Jl. Aplikasi No.5 Semarang','Mobile Development','024-5678901'),
('PT Cloud Computing ID','Jl. Cloud No.6 Semarang','Cloud Services','024-6789012'),
('PT Cyber Security Pro','Jl. Keamanan No.7 Semarang','Cybersecurity','024-7890123'),
('CV UI/UX Design Lab','Jl. Desain No.8 Semarang','UI/UX Design','024-8901234'),
('PT Network Solutions','Jl. Jaringan No.9 Semarang','Networking','024-9012345'),
('PT EduTech Indonesia','Jl. Pendidikan No.10 Semarang','EdTech','024-0123456');

INSERT INTO pembimbing (nama, nip, no_hp, email) VALUES
('Dra. Sri Wahyuni, M.Pd','198001012005012001','081112223001','sri.wahyuni@smkhidayah.sch.id'),
('Drs. Bambang Sutrisno, M.Kom','197505152000031002','081112223002','bambang.s@smkhidayah.sch.id'),
('Siti Nurhaliza, S.Kom, M.T','198803202010012003','081112223003','siti.n@smkhidayah.sch.id'),
('Ahmad Yani, S.T, M.Kom','198206102008011004','081112223004','ahmad.y@smkhidayah.sch.id'),
('Rina Marlina, S.Pd, M.Pd','199001152012012005','081112223005','rina.m@smkhidayah.sch.id'),
('Hendra Gunawan, S.Kom','198509202009011006','081112223006','hendra.g@smkhidayah.sch.id'),
('Dewi Sartika, S.T','199203102014012007','081112223007','dewi.s@smkhidayah.sch.id'),
('Agus Salim, M.Kom','197812052001031008','081112223008','agus.s@smkhidayah.sch.id'),
('Lina Kurniawati, S.Pd','198704182011012009','081112223009','lina.k@smkhidayah.sch.id'),
('Fajar Nugraha, S.Kom, M.T','199105252015011010','081112223010','fajar.n@smkhidayah.sch.id');

INSERT INTO penempatan (siswa_id, perusahaan_id, pembimbing_id, tanggal_mulai, tanggal_selesai, status) VALUES
(1,1,1,'2026-07-01','2026-09-30','Aktif'),
(2,1,1,'2026-07-01','2026-09-30','Aktif'),
(3,2,2,'2026-07-01','2026-09-30','Aktif'),
(4,2,2,'2026-07-01','2026-09-30','Aktif'),
(5,3,3,'2026-07-01','2026-09-30','Aktif'),
(6,3,3,'2026-07-01','2026-09-30','Aktif'),
(7,4,4,'2026-07-01','2026-09-30','Aktif'),
(8,4,4,'2026-07-01','2026-09-30','Aktif'),
(9,5,5,'2026-07-01','2026-09-30','Aktif'),
(10,5,5,'2026-07-01','2026-09-30','Aktif'),
(11,6,6,'2026-07-01','2026-09-30','Aktif'),
(12,6,6,'2026-07-01','2026-09-30','Aktif'),
(13,7,7,'2026-07-01','2026-09-30','Selesai'),
(14,8,8,'2026-07-01','2026-09-30','Aktif'),
(15,9,9,'2026-07-01','2026-09-30','Belum Mulai');

INSERT INTO jurnal (siswa_id, tanggal, kegiatan, hasil, kendala) VALUES
(1,'2026-09-01','Orientasi perusahaan dan pengenalan tim','Memahami struktur organisasi','Belum terbiasa lingkungan baru'),
(1,'2026-09-02','Mempelajari sistem yang dikembangkan','Dokumentasi sistem dibaca','Dokumentasi kurang lengkap'),
(1,'2026-09-03','Membuat desain database','ERD selesai dibuat','Kesulitan menentukan relasi tabel'),
(1,'2026-09-04','Implementasi tabel database','Tabel berhasil dibuat','Tidak ada'),
(1,'2026-09-05','Coding fitur login','Halaman login berfungsi','Validasi form masih error'),
(2,'2026-09-01','Orientasi dan briefing awal','Memahami jobdesk','Tidak ada'),
(2,'2026-09-02','Belajar framework yang digunakan','Setup environment selesai','Versi PHP berbeda'),
(2,'2026-09-03','Membuat modul CRUD siswa','CRUD dasar selesai','UI kurang rapi'),
(3,'2026-09-01','Pengenalan proyek web company profile','Memahami requirement','Tidak ada'),
(3,'2026-09-02','Desain UI homepage','Mockup selesai','Warna belum final'),
(3,'2026-09-03','Coding HTML/CSS homepage','Homepage responsif','Tidak ada'),
(4,'2026-09-01','Orientasi divisi development','Mengenal anggota tim','Tidak ada'),
(4,'2026-09-02','Belajar Git & GitHub workflow','Bisa push dan pull','Konflik merge'),
(5,'2026-09-01','Briefing proyek dashboard admin','Requirement dipahami','Tidak ada'),
(5,'2026-09-02','Membuat wireframe dashboard','Wireframe disetujui','Tidak ada'),
(6,'2026-09-01','Orientasi perusahaan','Memahami budaya kerja','Tidak ada'),
(6,'2026-09-02','Setup project Laravel','Project berhasil di-setup','Composer error'),
(7,'2026-09-01','Pengenalan data analytics tools','Mengenal Power BI','Tidak ada'),
(7,'2026-09-02','Membuat dashboard sederhana','Dashboard tampil data','Query lambat'),
(8,'2026-09-01','Orientasi mobile development','Memahami Android Studio','Tidak ada'),
(8,'2026-09-02','Membuat layout activity','Layout selesai','Constraint layout sulit'),
(9,'2026-09-01','Briefing proyek mobile app','Requirement clear','Tidak ada'),
(9,'2026-09-02','Coding splash screen','Splash screen jalan','Tidak ada'),
(10,'2026-09-01','Orientasi cloud services','Mengenal AWS basics','Tidak ada'),
(10,'2026-09-02','Deploy aplikasi sederhana','App berhasil di-deploy','Konfigurasi security group'),
(11,'2026-09-01','Orientasi cybersecurity','Memahami threat landscape','Tidak ada'),
(11,'2026-09-02','Praktik penetration testing dasar','Laporan vulnerability','Tools limited'),
(12,'2026-09-01','Orientasi UI/UX','Memahami design system','Tidak ada'),
(12,'2026-09-02','Membuat prototype di Figma','Prototype interaktif','Tidak ada'),
(14,'2026-09-01','Orientasi networking','Memahami topologi jaringan','Tidak ada'),
(14,'2026-09-02','Konfigurasi router dasar','Router berfungsi','Command sulit diingat');

INSERT INTO absensi (siswa_id, tanggal, status, keterangan) VALUES
(1,'2026-09-01','Hadir',NULL),(1,'2026-09-02','Hadir',NULL),(1,'2026-09-03','Hadir',NULL),(1,'2026-09-04','Hadir',NULL),(1,'2026-09-05','Hadir',NULL),
(1,'2026-09-08','Hadir',NULL),(1,'2026-09-09','Izin','Keperluan keluarga'),(1,'2026-09-10','Hadir',NULL),(1,'2026-09-11','Hadir',NULL),(1,'2026-09-12','Hadir',NULL),
(2,'2026-09-01','Hadir',NULL),(2,'2026-09-02','Hadir',NULL),(2,'2026-09-03','Hadir',NULL),(2,'2026-09-04','Sakit','Demam'),(2,'2026-09-05','Hadir',NULL),
(2,'2026-09-08','Hadir',NULL),(2,'2026-09-09','Hadir',NULL),(2,'2026-09-10','Hadir',NULL),
(3,'2026-09-01','Hadir',NULL),(3,'2026-09-02','Hadir',NULL),(3,'2026-09-03','Alpa',NULL),(3,'2026-09-04','Hadir',NULL),(3,'2026-09-05','Hadir',NULL),
(4,'2026-09-01','Hadir',NULL),(4,'2026-09-02','Hadir',NULL),(4,'2026-09-03','Hadir',NULL),(4,'2026-09-04','Hadir',NULL),(4,'2026-09-05','Hadir',NULL),
(5,'2026-09-01','Hadir',NULL),(5,'2026-09-02','Hadir',NULL),(5,'2026-09-03','Hadir',NULL),(5,'2026-09-04','Izin','Urusan sekolah'),(5,'2026-09-05','Hadir',NULL),
(6,'2026-09-01','Hadir',NULL),(6,'2026-09-02','Hadir',NULL),(6,'2026-09-03','Hadir',NULL),
(7,'2026-09-01','Hadir',NULL),(7,'2026-09-02','Hadir',NULL),(7,'2026-09-03','Sakit','Flu'),
(8,'2026-09-01','Hadir',NULL),(8,'2026-09-02','Hadir',NULL),(8,'2026-09-03','Hadir',NULL),
(9,'2026-09-01','Hadir',NULL),(9,'2026-09-02','Hadir',NULL),
(10,'2026-09-01','Hadir',NULL),(10,'2026-09-02','Hadir',NULL),
(11,'2026-09-01','Hadir',NULL),(11,'2026-09-02','Hadir',NULL),
(12,'2026-09-01','Hadir',NULL),(12,'2026-09-02','Hadir',NULL),
(14,'2026-09-01','Hadir',NULL),(14,'2026-09-02','Hadir',NULL);

INSERT INTO monitoring (siswa_id, pembimbing_id, tanggal_monitoring, catatan, nilai, rekomendasi) VALUES
(1,1,'2026-09-10','Siswa mampu berkomunikasi dengan baik dan dapat menyelesaikan tugas yang diberikan industri. Inisiatif tinggi.',85,'Pertahankan performa, tingkatkan dokumentasi kode.'),
(2,1,'2026-09-10','Siswa aktif bertanya dan cepat memahami instruksi. Hasil kerja rapi.',88,'Bagus fokus pada clean code.'),
(3,2,'2026-09-11','Siswa masih perlu bimbingan lebih intensif pada bagian frontend.',75,'Latihan lebih banyak CSS dan JavaScript.'),
(4,2,'2026-09-11','Kemampuan problem solving bagus. Tim work baik.',90,'Siap untuk tugas yang lebih kompleks.'),
(5,3,'2026-09-12','Siswa disiplin dan tepat waktu. Output sesuai target.',87,'Tingkatkan soft skill presentasi.'),
(6,3,'2026-09-12','Masih kesulitan pada konsep OOP. Perlu pendampingan.',70,'Review materi dasar PHP OOP.'),
(7,4,'2026-09-13','Siswa menunjukkan minat tinggi pada data analysis.',82,'Eksplorasi tools tambahan seperti Tableau.'),
(8,4,'2026-09-13','Progress mobile development bagus. Layout sudah rapi.',84,'Lanjutkan ke fitur API integration.'),
(9,5,'2026-09-14','Siswa mandiri dan kreatif dalam menyelesaikan UI.',89,'Pertahankan, siap mentoring junior.'),
(10,5,'2026-09-14','Pemahaman cloud masih dasar. Perlu banyak praktik.',72,'Ikuti workshop cloud fundamentals.');
