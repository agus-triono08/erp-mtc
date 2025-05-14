# Enterprise Resource Planning Maintenance Division (ERP MTC)

**ERP Maintenance Division** adalah sistem yang dirancang untuk membantu bisnis mengelola dan memelihara aset dan infrastruktur mereka dengan lebih efektif, sehingga dapat meningkatkan efisiensi, mengurangi biaya, dan meningkatkan kualitas layanan.

## Cara Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini secara lokal.

### 1. Clone Repository

Clone repository ini ke komputer Anda dengan perintah berikut:

```bash
git clone https://github.com/agus-triono08/erp-mtc.git
```

### 2. Install Dependencies

Masuk ke direktori proyek dan install semua dependensi yang diperlukan menggunakan Composer:

```bash
cd erp_mtc
composer install
npm install
```

### 3. Salin File ```.env```

Buat file ```.env``` baru dari file ```.env.example``` yang ada di direktori root proyek Anda:

```bash
cp .env.example .env
```

### 4. Konfigurasi Database

Setelah itu, buka file ```.env``` dan konfigurasi pengaturan database sesuai dengan database yang Anda gunakan. Contoh pengaturan untuk MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_mtc
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Kunci Enkripsi

Menambahkan kunci di file ```env``` dengan nilai pada ```APP_KEY```.

```bash
php artisan key:generate
```

### 6. Jalankan Symlink

Ini akan membuat symlink baru di ```public/storage``` yang akan menunjuk ke ```storage/app/public```.

```bash
php artisan storage:link
```

## Cara Migrasi Aplikasi ini
#### 1. Layout
``` bash
php artisan migrate --path=database\migrations\2025_03_04_084059_create_layout_table.php
```
#### 2. Divisi
``` bash
php artisan migrate --path=database\migrations\2025_03_05_012010_create_divisis_table.php
```
#### 3. Jabatan
``` bash
php artisan migrate --path=database\migrations\2025_03_05_015205_create_jabatans_table.php
```
#### 4. Users
``` bash
php artisan migrate --path=database\migrations\2025_03_05_022752_create_users_table.php
```
#### 5. Merek
``` bash
php artisan migrate --path=database\migrations\2025_03_25_074016_create_merek_table.php
```
#### 6. Jenis
``` bash
php artisan migrate --path=database\migrations\2025_03_25_074843_create_jenis_table.php
```
#### 7. Kategori
``` bash
php artisan migrate --path=database\migrations\2025_03_25_074707_create_kategori_table.php
```
#### 8. Kategori Merek
``` bash
php artisan migrate --path=database\migrations\2025_03_25_074454_create_kategori_merek_table.php
```
#### 9. Tipe
``` bash
php artisan migrate --path=database\migrations\2025_03_25_074207_create_tipe_table.php
```
#### 10. All
``` bash
php artisan migrate
```

### 7. Jalankan Migrasi Database

<!-- Setelah mengonfigurasi database, jalankan migrasi untuk membuat struktur tabel di database Anda:

```bash
php artisan migrate
``` -->

<!-- Jika Anda ingin mengisi database dengan data dummy, Anda dapat menjalankan perintah berikut untuk menjalankan seeder:

```bash
php artisan db:seed
``` -->

Jika Anda ingin menambahkan Jadwal Perawatan secara manual, Anda dapat menjalankan perintah berikut:
```bash
php artisan maintenance:generate-next-year
```

Jika Anda ingin Jadwal Perawatan secara otomatis, Anda dapat menjalankan perintah berikut:
#### 1. Tambahkan cron job ke sistem
```bash
crontab -e
```

#### 2. Tambahkan baris berikut ke file cron job
Gantilah /path/to/artisan dengan path sebenarnya ke file artisan Laravel di server kamu:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```
**Penjelasan:**
- "* * * * * = jalankan setiap menit."

- "cd /path/to/your/project = masuk ke direktori Laravel kamu."

- "php artisan schedule:run = jalankan Laravel scheduler."

- ">> /dev/null 2>&1 = redirect output supaya tidak membanjiri log."

### 8. Jalankan Aplikasi Laravel

Setelah langkah-langkah di atas selesai, Anda dapat menjalankan aplikasi menggunakan perintah berikut:

```bash
php artisan serve
```

```bash
npm run watch
```

Aplikasi akan berjalan di http://127.0.0.1:8000/. Anda dapat mengaksesnya melalui browser.

## Teknologi yang Digunakan

- **Laravel 8**: Framework PHP yang digunakan untuk membangun aplikasi ini.
- **Vue 2**: Framework JS yang digunakan untuk membangun aplikasi ini.
- **MySQL**: Sistem manajemen database relasional yang digunakan untuk menyimpan data.
- **HTML, CSS, JavaScript**: Untuk membangun antarmuka pengguna (frontend).
- **Composer**: Manajer ketergantungan PHP untuk mengelola paket-paket yang digunakan dalam proyek.

## License


The license used in developing this project is the [GPL-3.0 license](https://www.gnu.org/licenses/gpl-3.0.html).

## Kontak
Jika Anda memiliki pertanyaan atau masalah terkait aplikasi ini, silakan hubungi saya melalui [GitHub Issues](https://github.com/agus-triono08/erp-mtc/issues).
