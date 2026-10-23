# PANTAU.ID

**PANTAU.ID** adalah sebuah platform berbasis web yang dirancang untuk mewujudkan transparansi dan kemudahan akses informasi terkait proyek-proyek pembangunan di sekitar daerah Anda. 

Melalui aplikasi ini, masyarakat dapat memantau detail pelaksanaan proyek secara langsung, mulai dari siapa yang bertanggung jawab hingga estimasi waktu dan biaya yang digunakan.

## Teknologi yang Digunakan (Tech Stack)

Proyek ini dibangun menggunakan teknologi modern berikut:

- **Backend / Framework:** [Laravel](https://laravel.com/) (PHP)
- **Frontend / Styling:** CSS (Native / Custom)
- **Database:** [PostgreSQL](https://www.postgresql.org/)

## Prasyarat Instalasi

Sebelum memulai instalasi, pastikan sistem Anda telah memiliki:
- PHP >= 8.3
- Composer
- PostgreSQL
- Git

## Cara Instalasi & Menjalankan Proyek (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan PANTAU.ID di komputer lokal Anda:

1. **Clone Repository**
   ```bash
   git clone https://github.com/Tanjidoru/ProjectTier0.git

2. **Install Dependencies PHP (Composer)** 
    composer install

3. **Konfigurasi Environment**
    cp .env.example .env

4. **Konfigurasi Database (PostgreSQL)**
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nama_database_pantau_id
    DB_USERNAME=postgres
    DB_PASSWORD=password_db_anda

5. **Generate Application Key**
    php artisan key:generate

6. **Migrasi Database**
    php artisan migrate --seed

7. **Jalankan Local Server**
    php artisan serve
