# Projek 2 - Aplikasi Chat Real-Time (Laravel + Socket.io)

Aplikasi chat berbasis web *real-time* yang dibangun menggunakan framework **Laravel 11/12** untuk backend dan penyimpanan database MySQL, serta diintegrasikan dengan **Node.js + Socket.io** untuk menangani komunikasi data secara instan (*WebSocket*).

## 🚀 Fitur Utama (Core Requirements)

Aplikasi ini telah memenuhi seluruh spesifikasi fungsionalitas yang diwajibkan:
1. **User Authentication:** Menggunakan sistem autentikasi bawaan Laravel untuk mengamankan hak akses ruang chat.
2. **WebSocket Integration:** Sinkronisasi pengiriman data chat tanpa perlu memuat ulang halaman (*zero-refresh*).
3. **Private Chat:** Ruang komunikasi eksklusif dua arah antar-pengguna yang terproteksi dari kebocoran data di sisi klien.
4. **Group Chat:** Fasilitas komunikasi multi-user di dalam satu ruang obrolan (kamar grup) yang terisolasi.
5. **User Presence Tracking:** Fitur pemantauan status pengguna secara langsung (*Online/Offline*) dengan indikator warna visual pada sidebar.

---

## 🛠️ Arsitektur Teknologi

* **Backend Framework:** Laravel
* **Database System:** MySQL (menggunakan Eloquent ORM untuk manajemen data pesan dan pengguna)
* **Real-time Server:** Node.js (Express & Socket.io Server v4)
* **Frontend Tools:** Tailwind CSS (via Vite), Axios, & Socket.io Client

---

## ⚙️ Persyaratan Sistem & Instalasi

### 1. Prerequisites
Pastikan perangkat Anda sudah terinstal:
* PHP >= 8.2
* Composer
* Node.js & NPM
* MySQL / XAMPP

### 2. Langkah Instalasi

1. **Clone Repositori:**
   ```bash
   git clone [https://github.com/username/projek2.git](https://github.com/username/projek2.git)
   cd projek2
2. Instal Dependensi PHP (Laravel) & Frontend (NPM):
Jalankan perintah ini di terminal untuk mengunduh semua library yang dibutuhkan:
    composer install
    npm install
3. Konfigurasi Environment (.env):
Salin file konfigurasi bawaan Laravel, lalu sesuaikan nama database Anda di dalamnya (misal: DB_DATABASE=projek2 atau sesuai nama database MySQL kamu):
    cp .env.example .env
    php artisan key:generate
4. Migrasi Database:
Buat tabel-tabel database (users, messages, groups) secara otomatis ke phpMyAdmin dengan perintah:
    php artisan migrate
5. Menjalankan Aplikasi (Laravel & Vite):
Jalankan server lokal Laravel dan kompilasi Tailwind CSS secara bersamaan di terminal yang berbeda:
    php artisan serve

# Buka terminal baru di folder yang sama, lalu jalankan:
    npm run dev
6. Menjalankan Server WebSocket (Node.js):
    node server.js

Setelah langkah instalasi selesai, kamu bisa tambahkan bagian penutup ini di bawah teks langkah instalasi tadi untuk menjelaskan alur pengaman chat yang kamu buat ke dosen:

```markdown
---

## 👨‍💻 Logika Pengaman Ruang Chat (Client-Side Filter)
Aplikasi ini menerapkan proteksi ketat di sisi klien pada event `terima-pesan` untuk memastikan pesan tidak bocor:
* **Grup Chat:** Pesan hanya dirender jika `data.groupId` cocok dengan `currentGroupId` yang sedang dibuka pengguna.
* **Privat Chat:** Pesan disaring secara dua arah (`apakahDariTemanSaya` atau `apakahDariSayaSendiri`). Jika pengguna sedang berada di halaman selamat datang atau membuka room lain, pesan privat akan ditahan di latar belakang dan tidak akan merusak visual halaman.