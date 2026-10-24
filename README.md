# UTS Kualitas dan Evolusi Perangkat Lunak

## Identitas Mahasiswa
- Nama: Matthew Hayunaji Priantara
- NIM: 24/536179/SV/24400
- Kelas: TRPL Smt 5
- Program Studi: Sarjana Terapan Teknologi Rekayasa Perangkat Lunak
- Tema Proyek: Sistem Manajemen Inventaris Barang Gudang

## Deskripsi Sistem
Sistem ini merupakan aplikasi inventaris barang berbasis arsitektur client-server terpisah dengan komunikasi melalui RESTful API. Sistem dibangun untuk memenuhi pengujian CPMK 1 hingga CPMK 7 mata kuliah Kualitas dan Evolusi Perangkat Lunak.

## Spesifikasi Teknis
- Backend: Laravel 11 (PHP 8.3) dengan autentikasi token Laravel Sanctum.
- Server Runtime: FrankenPHP (Caddy/Go) berbasis Alpine Linux.
- Frontend: Vue.js 3 (Vite, Vue Router, Axios, Tailwind CSS).
- Basis Data: MySQL 8.0 dengan persistensi Docker named volume.
- Orkestrasi: Docker Compose (db, backend, frontend).
- CI/CD: GitHub Actions (Testing, Frontend Build, Push ke GHCR).

## Cara Menjalankan
1. Salin berkas lingkungan konfigurasi:
   cp .env.example .env
2. Bangun dan jalankan seluruh container:
   docker compose up -d --build
3. Layanan dapat diakses pada:
   - Frontend: http://localhost:8080
   - Backend API: http://localhost:8000
