# Mini Wallet - Backend

REST API untuk Mini Wallet dibangun menggunakan Laravel 11 + Sanctum.

## Tech Stack
- Laravel 11
- Laravel Sanctum
- MySQL

## Fitur
- Register & Login dengan Sanctum Token
- Lihat Saldo
- Top-up Saldo
- Transfer antar pengguna (dengan DB Transaction & rollback)
- Riwayat Transaksi
- Validasi ketat input nominal
- Protected endpoint dengan middleware auth

## ERD
| Tabel        | Kolom                                              |
|--------------|----------------------------------------------------|
| users        | id, name, email, phone, password, timestamps       |
| wallets      | id, user_id, balance, timestamps                   |
| transactions | id, wallet_id, type, amount, related_user_id, description, timestamps |

## Cara Menjalankan

### 1. Clone repository
git clone https://github.com/muhamadbn2025-debug/mini-wallet-backend.git
cd mini-wallet-backend

### 2. Install dependencies
composer install

### 3. Copy .env
cp .env.example .env

### 4. Generate key
php artisan key:generate

### 5. Setting database di .env
DB_DATABASE=mini_wallet
DB_USERNAME=root
DB_PASSWORD=

### 6. Migrasi database
php artisan migrate

### 7. Jalankan server
php artisan serve

## API Endpoints

| Method | Endpoint          | Auth | Keterangan              |
|--------|-------------------|------|-------------------------|
| POST   | /api/register     | No   | Register user baru      |
| POST   | /api/login        | No   | Login & dapat token     |
| POST   | /api/logout       | Yes  | Logout                  |
| GET    | /api/wallet       | Yes  | Lihat saldo             |
| POST   | /api/topup        | Yes  | Top-up saldo            |
| POST   | /api/transfer     | Yes  | Transfer ke user lain   |
| GET    | /api/transactions | Yes  | Riwayat transaksi       |