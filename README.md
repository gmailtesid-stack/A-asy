# 🏪 E-ASY POS System

> **Point of Sale System** berbasis Laravel + MySQL (TiDB Cloud) untuk operasional multi-outlet.  
> Hosting di **Vercel** | Database di **TiDB Cloud** | Media di **Cloudinary** | CI/CD via **GitHub Actions**

---

## 🚀 Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 (PHP 8.2) |
| Database | TiDB Cloud (MySQL Compatible) |
| Hosting | Vercel (Serverless) |
| Media Storage | Cloudinary |
| CI/CD | GitHub Actions |
| Frontend | Blade + Bootstrap 5 + Chart.js |

---

## 📋 Fitur Utama

- ✅ **Multi-Outlet** — Manajemen cabang terpusat
- ✅ **POS/Kasir** — Checkout real-time dengan potong stok otomatis
- ✅ **Inventory** — Stok per outlet + audit trail log
- ✅ **Low Stock Alert** — Email + notifikasi in-app otomatis
- ✅ **Dashboard Laporan** — Chart pendapatan harian, perbandingan outlet, top produk
- ✅ **RBAC** — Super Admin / Manager / Kasir
- ✅ **Cloudinary** — Upload foto produk

---

## ⚡ Quick Setup (Lokal)

```bash
# 1. Clone repo
git clone https://github.com/YOUR_USERNAME/easy-pos.git
cd easy-pos

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Isi variabel di .env (TiDB, Cloudinary, Mail)
# Lihat bagian Configuration di bawah

# 5. Migrasi + Seeder
php artisan migrate
php artisan db:seed

# 6. Jalankan
php artisan serve
```

---

## 🔧 Configuration

### TiDB Cloud (`.env`)
```env
DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=easy_pos
DB_USERNAME=xxxxxxxx.root
DB_PASSWORD=your_password
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

### Cloudinary (`.env`)
```env
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_UPLOAD_PRESET=easy_pos_products
```

### Mail / SMTP (`.env`)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

---

## 🚀 Deployment ke Vercel

### 1. GitHub Secrets yang diperlukan:
| Secret | Deskripsi |
|---|---|
| `VERCEL_TOKEN` | Token dari Vercel dashboard |
| `VERCEL_ORG_ID` | Organization ID Vercel |
| `VERCEL_PROJECT_ID` | Project ID Vercel |
| `APP_URL` | URL production app |
| `CRON_SECRET` | Secret key untuk cron job |

### 2. Environment Variables di Vercel Dashboard:
Salin semua isi `.env.example` → Vercel Dashboard → Settings → Environment Variables

### 3. Deploy:
```bash
git add .
git commit -m "🚀 Initial deploy E-ASY POS"
git push origin main
# GitHub Actions otomatis deploy ke Vercel!
```

---

## 👥 Akun Demo (setelah seeder)

| Email | Password | Role |
|---|---|---|
| admin@easy-pos.app | password | Super Admin |
| manager.jkt@easy-pos.app | password | Manager Jakarta |
| kasir.jkt@easy-pos.app | password | Kasir Jakarta |
| manager.bdg@easy-pos.app | password | Manager Bandung |

---

## 🗂️ Struktur Project

```
easy-pos/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── TransactionController.php  ← Checkout + stok
│   │   │   ├── ReportController.php       ← Dashboard laporan
│   │   │   └── ProductController.php      ← CRUD + Cloudinary
│   │   └── Middleware/
│   │       ├── CheckRole.php              ← RBAC
│   │       └── EnsureSameOutlet.php       ← Isolasi outlet
│   ├── Models/           (8 models)
│   └── Notifications/
│       └── LowStockNotification.php
├── database/
│   ├── migrations/       (5 migration files)
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── pos/index.blade.php
│   └── reports/dashboard.blade.php
├── routes/web.php
├── .env.example
├── vercel.json
└── .github/workflows/deploy.yml
```

---

## 📄 License
MIT — E-ASY POS System © 2024
