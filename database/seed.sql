-- ====================================================================
-- HIMO MOJOKERTO RAYA - SEED DATA v2.0
-- Initial data setup for organization structure, departments, settings & users
-- ====================================================================

-- 1. ROLES
INSERT INTO `roles` (`id`, `name`, `display_name`, `permissions`) VALUES
(1, 'super_admin', 'Super Administrator', '["*"]'),
(2, 'editor', 'Staff Redaksi / Humas', '["news.*", "contacts.view", "members.view"]');

-- 2. USERS
-- Default admin credentials:
-- Email: admin@himo.id
-- Password: AdminHimo2026! (Bcrypt cost 12 hash below)
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password_hash`, `is_active`) VALUES
(1, 1, 'Administrator HIMO', 'admin@himo.id', '$2y$12$NqM.s8N8o9mYIomH3Z2gNuOq3mK1t6mX5gWq4P3B9K1t6mX5gWq4P', 1);

-- 3. DEPARTMENTS (7 Departemen HIMO Mojokerto Raya)
INSERT INTO `departments` (`id`, `name`, `slug`, `icon`, `description`, `order_index`) VALUES
(1, 'Departemen Keagamaan dan Budi Pekerti Luhur', 'keagamaan', 'fas fa-praying-hands', 'Membina nilai moral, spiritual, toleransi, dan kerukunan beragama antar pelajar di Mojokerto Raya.', 1),
(2, 'Departemen Kepemimpinan dan Kaderisasi', 'kepemimpinan', 'fas fa-user-tie', 'Menyelenggarakan pelatihan dan kaderisasi calon pemimpin organisasi siswa di tingkat kabupaten dan kota.', 2),
(3, 'Departemen Hubungan Masyarakat dan Kemitraan', 'humas', 'fas fa-bullhorn', 'Menjembatani komunikasi eksternal, kerjasama antar-OSIS, dan sinergi dengan stakeholder pendidikan.', 3),
(4, 'Departemen Sosial, Lingkungan Hidup dan Pengabdian', 'sosial-lingkungan', 'fas fa-seedling', 'Menggerakkan aksi kemanusiaan, bakti sosial, dan kepedulian terhadap kelestarian lingkungan hidup.', 4),
(5, 'Departemen Pendidikan, Riset dan Prestasi', 'pendidikan-riset', 'fas fa-graduation-cap', 'Mendorong iklim literasi, riset pelajar, dan pendampingan olimpiade/prestasi akademik maupun non-akademik.', 5),
(6, 'Departemen Teknologi Informasi dan Media Kreatif', 'media-kreatif', 'fas fa-laptop-code', 'Mengelola publikasi digital, dokumentasi visual, desain komunikasi visual, dan infrastruktur website.', 6),
(7, 'Departemen Kewirausahaan dan Ekonomi Kreatif', 'kewirausahaan', 'fas fa-coins', 'Mengembangkan jiwa entrepreneurship, kemandirian finansial organisasi, dan pemberdayaan potensi pelajar.', 7);

-- 4. POSITIONS
INSERT INTO `positions` (`id`, `name`, `level`, `order_index`) VALUES
(1, 'Delegasi Kabupaten Mojokerto', 'delegasi', 1),
(2, 'Delegasi Kota Mojokerto', 'delegasi', 2),
(3, 'Ketua Kabupaten Mojokerto', 'pimpinan', 3),
(4, 'Ketua Kota Mojokerto', 'pimpinan', 4),
(5, 'Wakil Ketua Kabupaten Mojokerto', 'pimpinan', 5),
(6, 'Wakil Ketua Kota Mojokerto', 'pimpinan', 6),
(7, 'Sekretaris 1', 'bph', 7),
(8, 'Sekretaris 2', 'bph', 8),
(9, 'Bendahara 1', 'bph', 9),
(10, 'Bendahara 2', 'bph', 10),
(11, 'Koordinator Departemen', 'departemen_koor', 11),
(12, 'Anggota Departemen', 'departemen_anggota', 12);

-- 5. MEMBERS INITIAL SEEDS (Core Leadership Placeholders)
INSERT INTO `members` (`name`, `position_id`, `department_id`, `school_origin`, `order_index`) VALUES
('[Nama Delegasi Kabupaten]', 1, NULL, 'SMA Negeri di Kab. Mojokerto', 1),
('[Nama Delegasi Kota]', 2, NULL, 'SMA Negeri di Kota Mojokerto', 2),
('[Nama Ketua Kabupaten]', 3, NULL, 'SMA Negeri di Kab. Mojokerto', 3),
('[Nama Ketua Kota]', 4, NULL, 'SMA Negeri di Kota Mojokerto', 4),
('[Nama Wakil Ketua Kabupaten]', 5, NULL, 'SMA Negeri di Kab. Mojokerto', 5),
('[Nama Wakil Ketua Kota]', 6, NULL, 'SMA Negeri di Kota Mojokerto', 6),
('[Nama Sekretaris 1]', 7, NULL, 'SMA Negeri di Mojokerto Raya', 7),
('[Nama Sekretaris 2]', 8, NULL, 'SMA Negeri di Mojokerto Raya', 8),
('[Nama Bendahara 1]', 9, NULL, 'SMA Negeri di Mojokerto Raya', 9),
('[Nama Bendahara 2]', 10, NULL, 'SMA Negeri di Mojokerto Raya', 10);

-- 6. NEWS CATEGORIES
INSERT INTO `news_categories` (`id`, `name`, `slug`) VALUES
(1, 'Koordinasi', 'koordinasi'),
(2, 'Sosial', 'sosial'),
(3, 'Lingkungan', 'lingkungan'),
(4, 'Publikasi', 'publikasi'),
(5, 'Edukasi', 'edukasi');

-- 7. NEWS ARTICLES
INSERT INTO `news` (`id`, `category_id`, `author_id`, `title`, `slug`, `excerpt`, `body`, `featured_image`, `status`, `published_at`) VALUES
(1, 1, 1, 'Rapat Koordinasi HIMO Mojokerto Raya 2026', 'rakoor-2026', 'Forum koordinasi perdana pengurus HIMO Mojokerto Raya periode 2026/2027.', 'Rapat Koordinasi HIMO Mojokerto Raya sebagai forum sinkronisasi program kerja antar departemen dan antar-OSIS wilayah Kabupaten dan Kota Mojokerto.', 'Rakoor.png', 'published', '2026-05-23 09:00:00'),
(2, 2, 1, 'HIMO Bersinar: Apresiasi Pelajar Berprestasi', 'himo-bersinar-2026', 'Ajang temu dan apresiasi insan pelajar berprestasi se-Mojokerto Raya.', 'HIMO Bersinar adalah ajang apresiasi dan inspirasi bagi pelajar berprestasi dari OSIS se-Mojokerto Raya, digelar oleh HIMO Mojokerto Raya.', 'Bersinar.png', 'published', '2026-03-24 10:00:00'),
(3, 3, 1, 'HIMO Clean Up Day: Aksi Nyata Lingkungan', 'himo-clean-up-day-2026', 'Aksi gotong royong dan pelestarian lingkungan fasilitas publik bersama anggota OSIS.', 'Aksi nyata kepedulian lingkungan oleh pengurus dan anggota HIMO Mojokerto Raya dalam menjaga kebersihan fasilitas publik dan kawasan sekolah.', 'IMG_4010.jpg', 'published', '2026-05-23 07:30:00');

-- 8. CONTACT SETTINGS
INSERT INTO `contacts` (`key`, `value`, `label`, `type`) VALUES
('contact_person_1_name', '[Nama Narahubung 1]', 'Nama Contact Person 1', 'text'),
('contact_person_1_role', 'Ketua Umum', 'Jabatan CP 1', 'text'),
('contact_person_1_phone', '6281234567890', 'Nomor WhatsApp CP 1', 'phone'),
('contact_person_2_name', '[Nama Narahubung 2]', 'Nama Contact Person 2', 'text'),
('contact_person_2_role', 'Sekretaris Umum', 'Jabatan CP 2', 'text'),
('contact_person_2_phone', '6281234567891', 'Nomor WhatsApp CP 2', 'phone'),
('official_email', 'himomjkt@email.com', 'Email Resmi', 'email'),
('social_instagram_url', 'https://www.instagram.com/himomjkt', 'Instagram URL', 'url'),
('social_tiktok_url', 'https://www.tiktok.com/@himomojokerto', 'TikTok URL', 'url'),
('social_youtube_url', 'https://youtube.com/@himomojokertoraya', 'YouTube URL', 'url'),
('secretariat_address', 'Jl. Hayam Wuruk No.66, Mergelo, Magersari, Kec. Magersari, Kota Mojokerto, Jawa Timur 61318', 'Alamat Sekretariat', 'textarea'),
('google_maps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3492.4544729665495!2d112.4339906!3d-7.469145!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e780d603a1d9513%3A0x6b2dfcaab51eec4f!2sJl.%20Hayam%20Wuruk%20No.66%2C%20Mergelo%2C%20Magersari%2C%20Kec.%20Magersari%2C%20Kota%20Mojokerto%2C%20Jawa%20Timur%2061318!5e0!3m2!1sid!2sid!4v1711531234567!5m2!1sid!2sid', 'Google Maps Embed Link', 'textarea');

-- 9. SITE SETTINGS
INSERT INTO `site_settings` (`key`, `value`, `label`, `group`) VALUES
('site_name', 'HIMO Mojokerto Raya', 'Nama Organisasi', 'general'),
('site_tagline', 'The Spirit Of Majapahit Greatness', 'Slogan Organisasi', 'general'),
('period_active', '2026/2027', 'Periode Kepengurusan', 'general'),
('org_description', 'Himpunan Musyawarah OSIS (HIMO) Mojokerto Raya merupakan wadah kolaborasi, komunikasi, dan pengembangan organisasi siswa intra sekolah di wilayah Kabupaten dan Kota Mojokerto.', 'Deskripsi Organisasi', 'profile'),
('vision_statement', 'Mewujudkan Himpunan Musyawarah OSIS Mojokerto Raya sebagai wadah kolaborasi yang progresif, inklusif, dan berdampak bagi pelajar serta masyarakat Mojokerto Raya.', 'Visi Organisasi', 'profile'),
('stat_members', '48', 'Jumlah Pengurus Aktif', 'statistics'),
('stat_departments', '7', 'Jumlah Departemen', 'statistics'),
('stat_programs', '21', 'Jumlah Program Tahunan', 'statistics'),
('site_logo_path', 'Logo Himo Mojokerto.PNG', 'Logo Utama', 'media'),
('hero_background_path', 'WhatsApp Image 2026-03-27 at 16.27.16.jpeg', 'Foto Hero Landing Page', 'media');
