# 📊 PANDUAN LENGKAP PRESENTASI SEMINAR KERJA PRAKTIK (KP)
## Sistem Informasi Pengaduan & Prioritas Penanganan Kerusakan Jalan (JALAN KU)
**Berbasis Web Menggunakan AI YOLOv8 dan SPK TOPSIS**

* **Presenter**: Adiwinata Wildan
* **Program Studi**: Teknik Informatika / Sistem Informasi
* **Tahun Akademik**: 2026

---

## 📑 DAFTAR SLIDE PRESENTASI

- [Slide 1: Cover & Judul Presentasi](#slide-1-cover--judul-presentasi)
- [Slide 2: Latar Belakang & Urgensi Masalah](#slide-2-latar-belakang--urgensi-masalah)
- [Slide 3: Tujuan Pengembangan & Manfaat Sistem](#slide-3-tujuan-pengembangan--manfaat-sistem)
- [Slide 4: Arsitektur Sistem & Stack Teknologi](#slide-4-arsitektur-sistem--stack-teknologi)
- [Slide 5: Hak Akses & Alur Kerja 4 Role Pengguna](#slide-5-hak-akses--alur-kerja-4-role-pengguna)
- [Slide 6: Inovasi 1 — Deteksi Kerusakan Berbasis AI YOLOv8](#slide-6-inovasi-1--deteksi-kerusakan-berbasis-ai-yolov8)
- [Slide 7: Inovasi 2 — Sistem Pendukung Keputusan (SPK) TOPSIS](#slide-7-inovasi-2--sistem-pendukung-keputusan-spk-topsis)
- [Slide 8: Inovasi 3 — Monitoring Progres 3 Fase & Live Tracking](#slide-8-inovasi-3--monitoring-progres-3-fase--live-tracking)
- [Slide 9: Demonstrasi Antarmuka Sistem (UI Showcase)](#slide-9-demonstrasi-antarmuka-sistem-ui-showcase)
- [Slide 10: Hasil Implementasi & Pengujian Fungsional](#slide-10-hasil-implementasi--pengujian-fungsional)
- [Slide 11: Kesimpulan & Saran Pengembangan](#slide-11-kesimpulan--saran-pengembangan)
- [Slide 12: Penutup & Sesi Tanya Jawab (Q&A)](#slide-12-penutup--sesi-tanya-jawab-qa)

---

## SLIDE 1: COVER / JUDUL PRESENTASI

### 🖥️ Konten Tampilan Slide
* **Kategori**: SEMINAR LAPORAN KERJA PRAKTIK (KP)
* **Judul Utama**: **SISTEM INFORMASI JALAN KU**
* **Sub-Judul**: Sistem Informasi Pelaporan dan Penentuan Prioritas Penanganan Kerusakan Jalan Berbasis Web Menggunakan AI YOLOv8 dan SPK TOPSIS
* **Identitas Penyusun**:
  * **Nama Mahasiswa**: Adiwinata Wildan
  * **Program Studi**: Teknik Informatika / Sistem Informasi
  * **Tahun Akademik**: 2026

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Selamat pagi/siang kepada Bapak/Ibu Dosen Pembimbing dan Dosen Penguji yang saya hormati. Terima kasih atas waktu dan kesempatan yang diberikan. Pada kesempatan kali ini, saya akan mempresentasikan laporan Kerja Praktik saya yang berjudul: **Sistem Informasi Pengaduan dan Penentuan Prioritas Penanganan Kerusakan Jalan (JALAN KU) Berbasis Web Menggunakan AI YOLOv8 dan SPK TOPSIS**."*

---

## SLIDE 2: LATAR BELAKANG & URGENSI MASALAH

### 🖥️ Konten Tampilan Slide
1. **Saluran Pengaduan Konvensional Tidak Terintegrasi**
   * Laporan via media sosial atau surat fisik sering tercecer tanpa titik koordinat GPS presisi.
   * Dinas kesulitan memetakan persebaran titik kerusakan jalan secara sistematis.
2. **Penentuan Prioritas Perbaikan Masih Bersifat Subjektif**
   * Keterbatasan anggaran dan personel menuntut perbaikan jalan dilakukan pada ruas yang paling mendesak.
   * Penilaian perbaikan sebelumnya sering kali belum berbasis parameter keparahan terukur.
   * Risiko kecelakaan fatal bagi pengendara (terutama roda 2) kerap terabaikan.
3. **Kurangnya Transparansi Tindak Lanjut**
   * Masyarakat tidak memiliki akses untuk memantau tahapan pengerjaan laporan secara *real-time*.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Latar belakang pengembangan sistem ini didasari oleh tiga permasalahan utama di lapangan. Pertama, saluran aduan kerusakan jalan selama ini masih konvensional dan tercecer, sehingga dinas kesulitan menentukan lokasi pasti di lapangan. Kedua, dengan keterbatasan anggaran pemerintah daerah, proses penentuan prioritas jalan mana yang harus diperbaiki terlebih dahulu sering kali masih subjektif. Ketiga, masyarakat pelapor tidak memiliki saluran transparan untuk memantau apakah laporannya sudah disurvei, sedang diperbaiki, atau sudah selesai."*

---

## SLIDE 3: TUJUAN PENGEMBANGAN & MANFAAT SISTEM

### 🖥️ Konten Tampilan Slide
* 🎯 **1. Akses Pengaduan Partisipatif (*Crowdsourcing*)**: Membangun portal pelaporan online dengan fitur *geotagging* GPS otomatis dan peta interaktif Leaflet.
* 🤖 **2. Otomatisasi Deteksi Kerusakan Berbasis AI**: Menerapkan model **AI YOLOv8** untuk mendeteksi lubang (*pothole*), retakan (*crack*), dan longsor (*landslide*) dari foto warga.
* ⚖️ **3. Penentuan Prioritas Objektif (SPK TOPSIS)**: Menerapkan algoritma **TOPSIS** untuk merangking usulan penanganan jalan berdasarkan 4 kriteria terukur.
* 👷 **4. Efisiensi Disposisi Tugas**: Mempercepat alur penugasan dari Admin ke dinas teknis operasional (OPD Bina Marga).
* 📊 **5. Monitoring & Transparansi 3 Fase**: Menyediakan linimasa pengerjaan bertahap (*Before*, *In Progress*, *After*) dan evaluasi kepuasan masyarakat.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Berdasarkan masalah tersebut, tujuan utama dari proyek ini adalah membangun sistem JALAN KU yang tidak hanya menampung laporan warga, tetapi juga memiliki kemampuan AI untuk mendeteksi jenis kerusakan secara otomatis, serta dilengkapi algoritma SPK TOPSIS untuk menghasilkan rekomendasi prioritas perbaikan jalan yang objektif, transparan, dan akuntabel."*

---

## SLIDE 4: ARSITEKTUR SISTEM & STACK TEKNOLOGI

### 🖥️ Konten Tampilan Slide
* 🌐 **Frontend**:
  * Laravel Blade Template & Tailwind CSS (Antarmuka Responsif & *Mobile-Friendly*)
  * Leaflet.js & OpenStreetMap (Peta Interaktif Spasial)
* ⚙️ **Backend**:
  * **Framework**: Laravel 13
  * **Bahasa Pemrograman**: PHP 8.4
  * **Arsitektur**: Model-View-Controller (MVC) & RESTful Geo-API
  * **Keamanan**: *Role-Based Access Control* (RBAC) & CSRF Protection
* 🤖 **AI Engine (Computer Vision)**:
  * Python 3.12 & Ultralytics YOLOv8 (`model_terbaru_kaggle.pt`)
* 🧮 **Decision Support Engine**:
  * Algoritma SPK TOPSIS (*Decision Matrix, Normalization, Euclidean Distance*)

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Untuk membangun sistem yang andal, arsitektur teknologi dibagi menjadi 4 pilar. Pada sisi Backend, kami menggunakan **Framework Laravel 13** dengan **PHP 8.4**. Frontend menggunakan Blade dan Leaflet.js untuk pemetaan geografis. Di bagian pemrosesan citra AI, sistem ditenagai oleh **Python 3.12** dengan model **YOLOv8**, dan di sisi pendukung keputusan diimplementasikan modul **SPK TOPSIS** yang terintegrasi langsung di dalam service Laravel."*

---

## SLIDE 5: HAK AKSES & ALUR KERJA 4 ROLE PENGGUNA

### 🖥️ Konten Tampilan Slide
| Role Pengguna | Fungsi & Wewenang Utama |
| :--- | :--- |
| 👤 **1. Masyarakat (Pelapor)** | Membuat laporan pengaduan & *pin* lokasi GPS, unggah foto awal (1–3 foto), melacak status laporan (*Live Tracking*), dan memberi rating kepuasan (1–5★). |
| 🛡️ **2. Admin (Verifikator / Command Center)** | Memvalidasi laporan masuk (Verifikasi / Tolak / Tandai Duplikat), menjalankan deteksi AI YOLO, memicu hitung ulang TOPSIS, dan menerbitkan surat tugas ke OPD. |
| 👷 **3. OPD / Petugas Lapangan** | Menerima surat tugas, memulai survei fisik lokasi, menginput progres fisik berkala (0% – 100%), unggah foto *Before, In Progress, After*, dan menyelesaikan perbaikan jalan. |
| ⚙️ **4. Super Admin** | Manajemen pengguna (User CRUD) & master OPD, mengonfigurasi bobot kriteria SPK TOPSIS, dan memantau seluruh *Audit Trail Log*. |

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Sistem JALAN KU memiliki 4 tingkatan hak akses yang saling berkolaborasi. Masyarakat bertindak sebagai pelapor, Admin di dinas bertugas memvalidasi data dan menentukan prioritas, OPD Lapangan bertindak sebagai eksekutor fisik perbaikan, dan Super Admin mengontrol tata kelola data master serta bobot algoritma keputusan."*

---

## SLIDE 6: INOVASI 1 — DETEKSI KERUSAKAN BERBASIS AI YOLOV8

### 🖥️ Konten Tampilan Slide
* 📷 **Alur Pemrosesan Citra**:
  * Foto laporan warga diproses oleh skrip engine `ai_engine/yolo_detector.py`.
  * Sistem melakukan optimasi/kompresi citra otomatis guna efisiensi RAM server dan mencegah *timeout*.
* 🔍 **Kelas Cacat Jalan yang Dideteksi**:
  * **Pothole (Lubang Jalan)**
  * **Crack (Retakan Aspal)**
  * **Landslide (Longsor / Ambles)**
* 📈 **Output Hasil Deteksi AI**:
  * Visualisasi kotak pembatas (*bounding box*) pada foto.
  * Persentase tingkat keyakinan (*Confidence Score*).
  * Estimasi luasan area kerusakan ($m^2$).
  * **Otomatisasi Input**: Nilai deteksi otomatis mengisi skala kriteria **C1 (Tingkat Kerusakan)** dan **C2 (Keselamatan Pengguna)**.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Inovasi pertama adalah modul Computer Vision berbasis AI YOLOv8. Saat warga mengunggah foto jalan rusak, AI akan otomatis memindai dan menghitung jumlah lubang, retakan, atau longsor beserta estimasi luas kerusakannya. Hasil pemindaian AI ini secara otomatis mengisi nilai parameter fisik jalan sehingga tidak ada rekayasa atau perkiraan subjektif dari manusia."*

---

## SLIDE 7: INOVASI 2 — SISTEM PENDUKUNG KEPUTUSAN (SPK) TOPSIS

### 🖥️ Konten Tampilan Slide
* ⚖️ **4 Kriteria Baku Penilaian (Total Bobot 100%)**:
  1. **C1 - Tingkat/Luas Kerusakan (40% | Benefit)**: Mengukur keparahan fisik ($m^2$) via AI YOLO.
  2. **C2 - Keselamatan Pengguna (25% | Benefit)**: Mengukur potensi bahaya fatal/kecelakaan pengendara.
  3. **C3 - Jumlah Laporan Tervalidasi (25% | Benefit)**: Akumulasi aduan warga pada ruas jalan yang sama (*Crowdsourcing*).
  4. **C4 - Lama Belum Tertangani (10% | Benefit)**: Jumlah hari sejak laporan masuk agar aduan lama tidak terbengkalai.
* 🧮 **Formula & Skor Preferensi ($V$)**:
  * Menghitung Jarak Solusi Ideal Positif ($D^+$) dan Jarak Solusi Ideal Negatif ($D^-$).
  * Menghasilkan nilai preferensi $V = \frac{D^-}{D^+ + D^-}$ ($0.0000 - 1.0000$).
* 🏷️ **Klasifikasi Kategori Prioritas**:
  * **Sangat Prioritas** ($V \ge 0.65$ atau Longsor Parah)
  * **Prioritas Tinggi** ($V \ge 0.35$ atau $\ge 3$ Lubang)
  * **Sedang** ($V \ge 0.15$) | **Rendah** ($V < 0.15$)

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Inovasi kedua adalah algoritma SPK TOPSIS. Sistem menggabungkan 4 kriteria utama: kondisi fisik dari AI (40%), potensi bahaya keselamatan (25%), banyaknya laporan warga (25%), dan lamanya waktu tunggu (10%). Dari perhitungan jarak solusi ideal positif dan negatif, sistem menghasilkan nilai preferensi matematis yang otomatis mengurutkan 10 jalan paling prioritas untuk segera dieksekusi oleh dinas teknis."*

---

## SLIDE 8: INOVASI 3 — MONITORING PROGRES 3 FASE & LIVE TRACKING

### 🖥️ Konten Tampilan Slide
* 📸 **Dokumentasi Lapangan Tiga Fase**:
  * **1. Fase Before (Sebelum)**: Foto kondisi awal kerusakan dari masyarakat pelapor.
  * **2. Fase In Progress (Pengerjaan)**: Foto proses perbaikan bertahap (0% – 100%) dan catatan teknis oleh petugas dinas OPD.
  * **3. Fase After (Selesai)**: Foto bukti pengerjaan aspal yang telah mulus dan rampung 100%.
* 🔄 **Linimasa Real-Time (*Live Tracking*)**:
  * Masyarakat dapat memantau status secara langsung melalui tahapan: `Diajukan` $\rightarrow$ `Diverifikasi` $\rightarrow$ `Ditugaskan` $\rightarrow$ `Disurvei` $\rightarrow$ `Sedang Diperbaiki` $\rightarrow$ `Selesai`.
* ⭐ **Umpan Balik Warga**:
  * Pembukaan fitur rating (1–5 bintang) dan komentar kepuasan setelah status resmi Selesai.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Inovasi ketiga adalah transparansi pengerjaan. Untuk menjamin akuntabilitas kinerja dinas di lapangan, petugas wajib mengunggah bukti foto 3 fase: Before, In Progress, dan After. Seluruh tahapan ini dapat dipantau oleh masyarakat secara real-time melalui halaman Live Tracking, dan setelah selesai, warga dapat memberikan rating bintang serta ulasan kepuasan."*

---

## SLIDE 9: DEMONSTRASI ANTARMUKA SISTEM (UI SHOWCASE)

### 🖥️ Konten Tampilan Slide
* 📱 **1. Portal Masyarakat**:
  * Peta Geotagging interaktif, tombol deteksi GPS otomatis, dan form pelaporan ringkas.
* 🖥️ **2. Dashboard Operasional Admin**:
  * Tabel **TOP 10 Rekomendasi Prioritas TOPSIS**, tombol eksekusi AI, dan panel penugasan OPD.
* 👷 **3. Dashboard Penugasan OPD Lapangan**:
  * Daftar surat perintah tugas aktif dan form pembaruan persentase progres fisik (0–100%).
* ⚙️ **4. Panel Master Super Admin**:
  * Manajemen akun pengguna, kustomisasi bobot kriteria SPK, dan log audit keamanan.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Berikut adalah gambaran antarmuka sistem JALAN KU. Setiap modul dirancang dengan antarmuka yang bersih, responsif, dan mudah digunakan. Di sisi masyarakat, terdapat form dengan peta Leaflet yang intuitif. Di sisi Admin, terdapat dashboard monitoring dengan tabel TOP 10 Prioritas TOPSIS, serta portal khusus bagi petugas OPD untuk memperbarui kemajuan kerja di lapangan."*

---

## SLIDE 10: HASIL IMPLEMENTASI & PENGUJIAN FUNGSIONAL

### 🖥️ Konten Tampilan Slide
* 🧪 **Hasil Pengujian Fungsional (*Blackbox Testing*)**:
  * Modul Autentikasi & RBAC: **100% Berhasil**
  * Modul Geotagging & Peta Leaflet: **100% Berhasil**
  * Modul AI YOLOv8 Deteksi Citra: **100% Berhasil**
  * Modul Algoritma SPK TOPSIS: **100% Berhasil & Akurat**
  * Modul Penugasan & Progres 3 Fase: **100% Berhasil**
* 💡 **Evaluasi Kinerja Sistem**:
  * Pemrosesan deteksi citra AI rata-rata memerlukan waktu $2 - 5$ detik per foto.
  * Kalkulasi perangkingan TOPSIS mampu mengolah puluhan laporan secara instan (< 1 detik).
  * Deteksi duplikasi berhasil mengelompokkan laporan pada ruas jalan yang sama secara akurat.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Berdasarkan hasil pengujian fungsional menggunakan metode Blackbox Testing pada seluruh modul dan role, sistem JALAN KU telah berjalan 100% valid sesuai spesifikasi kebutuhan. Komputasi AI dan algoritma TOPSIS berhasil mengeksekusi data secara cepat dan tepat."*

---

## SLIDE 11: KESIMPULAN & SARAN PENGEMBANGAN

### 🖥️ Konten Tampilan Slide
* 🏆 **Kesimpulan**:
  1. Sistem JALAN KU berhasil menyediakan platform pelaporan infrastruktur jalan terpadu berbasis *crowdsourcing* dan koordinat GPS presisi.
  2. Integrasi AI YOLOv8 dan SPK TOPSIS berhasil mengeliminasi penilaian subjektif dan mempercepat penetapan prioritas perbaikan jalan.
  3. Alur kerja dari verifikasi Admin, disposisi tugas ke OPD, hingga dokumentasi 3 fase berhasil meningkatkan transparansi dan akuntabilitas dinas.
* 🚀 **Saran Pengembangan Selanjutnya**:
  1. Integrasi notifikasi otomatis via WhatsApp Gateway / Email kepada warga pelapor.
  2. Pengembangan aplikasi *Mobile Native* (Android/iOS) dengan mode *offline* untuk petugas survei di daerah pelosok.
  3. Penambahan sensor getaran (*accelerometer*) smartphone untuk deteksi lubang jalan otomatis saat berkendara.

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Kesimpulannya, sistem JALAN KU telah berhasil menjadi solusi nyata dalam modernisasi penanganan infrastruktur jalan di daerah. Untuk pengembangan ke depan, sistem ini dapat ditingkatkan dengan integrasi WhatsApp Gateway serta aplikasi mobile native bagi petugas survei lapangan."*

---

## SLIDE 12: PENUTUP & SESI TANYA JAWAB (Q&A)

### 🖥️ Konten Tampilan Slide
* **TERIMA KASIH**
* **SESI TANYA JAWAB (Q & A)**
* **Seminar Laporan Kerja Praktik — Sistem JALAN KU**
* *Presenter: Adiwinata Wildan*
* *"Mewujudkan Infrastruktur Jalan yang Aman, Berkualitas, dan Transparan"*

### 🎙️ Naskah Pembicara (Speaker Notes)
> *"Demikian presentasi laporan Kerja Praktik mengenai Sistem Informasi JALAN KU. Saya mengucapkan terima kasih atas perhatian Bapak/Ibu Dosen Penguji dan Pembimbing. Waktu dan tempat saya kembalikan kepada Dosen Penguji untuk sesi tanya jawab dan masukan. Terima kasih."*
