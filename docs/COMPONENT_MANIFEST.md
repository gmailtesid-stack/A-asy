# 📑 E-ASY POS COMPONENT MANIFEST (MASTER LIST)

Dokumen ini mencatat setiap komponen dalam repositori E-ASY dan fungsinya secara spesifik untuk memastikan transparansi total bagi pengembang.

---

## 🏗️ 1. CORE ARCHITECTURE & CONFIGURATION

| File | Fungsi / Deskripsi |
| :--- | :--- |
| `api/index.php` | **Vercel Gateway**. Mengalihkan cache, views, dan sessions ke `/tmp` (filesystem writable di Vercel). |
| `bootstrap/app.php` | Konfigurasi middleware global, routing, dan penanganan exception (Laravel 13). |
| `config/pos.php` | Aturan bisnis POS: PPN (`tax_rate`) dan prefix invoice (`INV`). |
| `config/database.php` | Konfigurasi multi-db (MySQL/SQLite) dengan dukungan SSL TiDB Cloud. |
| `vercel.json` | Konfigurasi *Infrastructure as Code* untuk deployment Vercel. |
| `.github/workflows/deploy.yml` | Pipeline CI/CD: Automated Testing & Production Deploy ke Vercel. |

---

## 🧠 2. BACKEND LOGIC (CONTROLLERS)

| Controller | Tanggung Jawab Utama |
| :--- | :--- |
| `TransactionController` | Otak POS. Menangani checkout atomik, potong stok, & cetak struk. |
| `InboundController` | Manajemen pengadaan barang (PO) dan penerimaan fisik (GRN). |
| `OutboundController` | Siklus pesanan keluar: Picking, Packing, & Shipping. |
| `InventoryController` | Audit stok, penyesuaian manual, dan log mutasi barang. |
| `ReportController` | Dashboard analitik, laporan keuangan, & peta aset GPS. |
| `AuthController` | Sistem login, logout, dan manajemen sesi user. |
| `SyncMarketplaceJob` | Job background untuk sinkronisasi stok ke kanal eksternal. |
| `CloudinaryService` | Integrasi CDN untuk optimasi dan pengiriman gambar produk. |

---

## 💾 3. DATA MODELS & ENTITIES

| Model | Mewakili Entitas Bisnis |
| :--- | :--- |
| `Product` | Katalog barang (SKU, Harga, Deskripsi, Gambar). |
| `Inventory` | Saldo stok real-time per outlet/gudang/lokasi. |
| `Transaction` | Header transaksi penjualan di kasir. |
| `Outlet` | Entitas cabang fisik (Multi-tenancy). |
| `Warehouse` | Fasilitas penyimpanan barang (WMS). |
| `Location` | Koordinat rak fisik (Aisle, Shelf, Bin). |
| `Grn` (Goods Received Note) | Dokumen resmi penerimaan barang masuk. |
| `SalesOrder` | Pesanan dari pelanggan/marketplace. |
| `Channel` | Pengaturan sinkronisasi marketplace (Shopee/Tokopedia). |

---

## 🛡️ 4. SECURITY & MIDDLEWARE

| Component | Kegunaan |
| :--- | :--- |
| `CheckRole` | Gating akses berdasarkan peran (Admin/Supervisor/Operator). |
| `CheckPermission` | Gating akses berdasarkan izin granular (e.g. `void-transaction`). |
| `EnsureSameOutlet` | Proteksi isolasi data; mencegah akses data lintas cabang. |
| `InventoryPolicy` | Aturan otorisasi model untuk manipulasi data stok. |

---

## 🎨 5. FRONTEND & UI (BLADE VIEWS)

| Views Folder | Deskripsi Antarmuka |
| :--- | :--- |
| `resources/views/pos/` | Antarmuka kasir modern dengan pencarian produk cepat. |
| `resources/views/inbound/` | Form pembuatan PO dan proses penerimaan barang (GRN). |
| `resources/views/outbound/` | Dashboard picking & packing untuk staff gudang. |
| `resources/views/reports/` | Visualisasi chart.js dan peta sebaran aset GPS. |
| `resources/views/master/` | Manajemen data master (Product, Category, Supplier). |

---

## 🗄️ 6. DATABASE SCHEMA (MIGRATIONS)

| Migration File | Peran dalam Database |
| :--- | :--- |
| `create_outlets_table` | Inisialisasi entitas cabang utama. |
| `create_rbac_tables` | Pembuatan tabel `roles` dan `permissions`. |
| `create_wms_transaction_tables` | Pembuatan tabel PO, SO, GRN, Picking, Packing. |
| `upgrade_products_for_oms` | Penambahan kolom `status` dan `csku` untuk marketplace. |
| `add_indexes_for_performance` | Optimasi database untuk query skala besar. |

---
*Dokumen ini adalah manifestasi teknis lengkap dari E-ASY Unified System.*
