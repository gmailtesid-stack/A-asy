# 🌐 E-ASY POS, WMS & OMS — The Ultimate Enterprise Unified Ecosystem
## Master Technical Specification, Architectural Blueprint & Operational Manual | v1.0 | 2026

**E-ASY** adalah platform orkestrasi ritel enterprise tingkat tinggi yang dirancang untuk menyatukan seluruh ekosistem bisnis—mulai dari titik penjualan di toko (**POS**), logistik gudang yang kompleks (**WMS**), hingga integrasi pesanan marketplace global (**OMS**). Dokumen ini berfungsi sebagai referensi teknis otoritatif bagi pengembang, arsitek infrastruktur, dan manajer operasional.

---

## 🏗️ 1. ARCHITECTURAL PHILOSOPHY & CLOUD INTEGRATION

E-ASY dibangun dengan prinsip **Cloud-Native Distribution** & **Zero-Maintenance Infrastructure**.

### **A. Kenapa Menggunakan Ekosistem Ini? (Rationale)**

| Provider | Peran Strategis | Analisis Keunggulan |
| :--- | :--- | :--- |
| **GitHub** | *DevOps & Integrity* | Menjamin integritas kode melalui *Strict Version Control*. **GitHub Actions** mengotomatiskan *Unit Testing* & *Production Deployment*, memastikan tidak ada kode rusak yang mencapai server. |
| **Vercel** | *Global Edge Host* | Memberikan skalabilitas *Serverless* instan. Dengan teknologi *Edge Computing*, aplikasi dimuat dari lokasi terdekat pengguna (Jakarta/Singapore), menjamin latensi di bawah 100ms. |
| **TiDB Cloud** | *Distributed SQL* | Database *Next-Gen* yang menggabungkan kecepatan transaksi MySQL dengan skalabilitas horizontal tak terbatas. Mendukung HTAP untuk laporan real-time tanpa membebani transaksi kasir. |
| **Cloudinary** | *Asset Intelligence* | Mengotomatiskan optimasi gambar produk (Format WebP/AVIF secara dinamis). Menghemat bandwidth hingga 60% dan mempercepat loading katalog produk. |

### **B. Jaring Konektivitas (Integration Flow)**
Sistem ini bekerja melalui jaring-jaring integrasi otomatis yang saling mengunci:
1.  **Push to Deploy**: Developer melakukan `git push` -> GitHub Actions menjalankan test -> Vercel melakukan build.
2.  **Serverless Gateway**: `api/index.php` bertindak sebagai jembatan yang mengalihkan filesystem read-only Vercel (Cache/Views) ke direktori `/tmp` yang bersifat writable.
3.  **Secure Tunnel**: Aplikasi di Vercel terhubung ke **TiDB Cloud** menggunakan enkripsi SSL (ISRG Root X1) yang dikonfigurasi di `database.php` & `vercel.json`.
4.  **Event Driven**: Penjualan di POS memicu pembaruan stok di TiDB -> Memicu **SyncMarketplaceJob** di background untuk memperbarui stok di Shopee/Tokopedia secara real-time.

---

## 📦 2. BEDAH MODUL & FITUR MENDALAM (DEEP MODULES)

### **I. Point of Sale (POS) — Ultra Fast Cashier**
*Files: `TransactionController.php`, `Transaction.php`, `TransactionDetail.php`*
- **Atomic Checkout Logic**: Menjamin stok tidak akan pernah minus atau "oversell". Menggunakan protokol `DB::transaction` dengan `lockForUpdate()`.
- **Dynamic Tax System**: Kalkulasi PPN (Default 11%) yang dikonfigurasi di `config/pos.php`.
- **Multi-Payment Gateway**: Pencatatan metode Cash, Transfer, QRIS, dan Card secara terpisah.
- **Sequential Smart Invoicing**: Faktur unik per outlet (e.g., `INV-JKT-20260503-0001`) yang di-generate dengan proteksi duplikasi di level database.

### **II. Warehouse Management (WMS) — Total Logistics Control**
*Files: `InboundController.php`, `OutboundController.php`, `Location.php`, `StockTransfer.php`*
- **Inbound Pipeline**: PO Supplier -> Goods Received Note (GRN) -> Automasi *Stock Increment* pada lokasi rak yang spesifik.
- **Outbound Pipeline**: Sales Order (SO) -> Picking (Status: Found/Partial/Not Found) -> Packing -> Shipping.
- **Mapping Lokasi (Bin)**: Inventori terpetakan hingga level `Gudang -> Zona -> Baris -> Rak -> Bin (Wadah)`.
- **Stock Transfer**: Mutasi stok antar cabang dengan sistem *In-Transit* (stok dipotong saat kirim, bertambah saat diterima).

### **III. Order Management (OMS) — Global Integration**
*Files: `SyncMarketplaceJob.php`, `Channel.php`, `MarketplaceController.php`*
- **Marketplace Bridge**: Menghubungkan stok fisik gudang dengan kanal online (Shopee/Tokopedia).
- **Automated Reconcile**: Sinkronisasi stok otomatis setiap kali ada transaksi fisik atau perubahan status di marketplace.

---

## 🛡️ 3. KEAMANAN, RBAC & MULTI-TENANCY

### **A. Matriks Izin Granular (Permissions Matrix)**
| Fitur | Admin | Supervisor | Operator/Kasir |
| :--- | :---: | :---: | :---: |
| Dashboard Finansial | ✅ | ✅ | ❌ |
| Master Data (CRUD) | ✅ | ✅ | 👁️ View Only |
| Void Transaksi | ✅ | ❌ | ❌ |
| Kelola User & Role | ✅ | ❌ | ❌ |
| Proses GRN & Putaway | ✅ | ✅ | ✅ |
| Manajemen Lokasi Bin | ✅ | ✅ | ✅ |

### **B. Security Middleware Layer**
- **`CheckRole`**: Memastikan hanya peran tertentu yang bisa mengakses modul (e.g., Modul User hanya untuk Admin).
- **`CheckPermission`**: Gating fungsionalitas di level fitur (e.g., tombol 'Hapus' hanya muncul jika punya izin).
- **`EnsureSameOutlet`**: Fitur Multi-Outlet Isolation. Staff di Outlet A tidak diizinkan melihat atau mengedit data milik Outlet B.

---

## 💾 4. DATABASE DEEP-DIVE (TECHNICAL SCHEMA)

| Tabel Utama | Tujuan Bisnis | Primary Key / Indexing |
| :--- | :--- | :--- |
| `users` | Autentikasi & Mapping Role | `id`, `email` (Unique), `role_id` |
| `products` | Katalog Master (SKU/Barcode) | `id`, `sku` (Unique Index), `status` |
| `inventories` | Stok Real-time Terdistribusi | `id`, Composite Index: `[product, outlet, warehouse]` |
| `transactions` | Rekam Jejak Penjualan POS | `id`, `invoice_number` (Unique Index) |
| `inventory_logs`| Audit Trail (Log Perubahan Stok)| `id`, `reference` (Indexed), `type` |

---

## 🔄 5. LOGIKA OPERASIONAL (HOW IT WORKS)

### **A. Logika Checkout POS (Pseudo-Logic)**
```php
1. Validasi Input (Items, Payment)
2. Mulai DB Transaction
3. LOCK baris stok di tabel inventories (lockForUpdate)
4. Cek ketersediaan: IF (Stok < Diminta) -> Rollback & Error
5. Hitung Subtotal + Pajak (config/pos.php)
6. Generate Invoice Number (Sequential per Outlet)
7. Simpan Header & Detail Transaksi
8. Kurangi Stok Fisik & Buat InventoryLog
9. IF (Stok < Min_Quantity) -> Kirim LowStockNotification
10. Commit Transaction
```

### **B. Alur WMS Inbound**
`Supplier` -> `Purchase Order (PO)` -> `Approval Supervisor` -> `Goods Received (GRN)` -> `Stok Masuk Otomatis`

---

## 🛠️ 6. SPESIFIKASI TEKNIS & PEMELIHARAAN

### **A. Hardware Compatibility**
- **Thermal Printer**: Mendukung ESC/POS (80mm/58mm) via Driver Windows/Linux.
- **Barcode Scanner**: Seluruh tipe HID (Keyboard Mode) Laser/CCD.
- **Mobile device**: Dioptimalkan untuk layar 5.5" ke atas (Android PDA/Smartphone).

### **B. Jadwal Pemeliharaan (Maintenance)**
1. **Daily Check**: Notifikasi stok menipis dikirim otomatis via **GitHub Actions Cron** setiap hari.
2. **Snapshot DB**: TiDB Cloud melakukan backup otomatis setiap 24 jam.
3. **Log Rotation**: Disarankan melakukan pengarsipan `inventory_logs` setiap 12 bulan jika volume transaksi tinggi.

---

## 📁 7. PETA STRUKTUR PROYEK (DIRECTORY MAP)

```
easy-pos/
├── api/index.php              # Jembatan Laravel ke Vercel Serverless (Tmp redirection)
├── app/Http/Controllers/      # Logic Utama (POS, WMS, OMS, Master Data)
├── app/Http/Middleware/       # Layer Keamanan, RBAC, & Multi-outlet Isolation
├── app/Models/                # Definisi Skema Data & Relasi Eloquent
├── app/Jobs/                  # Asynchronous Tasks (Marketplace Sync)
├── app/Policies/              # Aturan Otorisasi Akses Data (Product, User)
├── app/Services/              # Integrasi Layanan (Cloudinary, Services)
├── config/pos.php             # Konfigurasi Aturan Bisnis (Tax, Prefix)
├── database/migrations/       # Evolusi Skema Tabel & Indeks Performa
├── database/seeders/          # Inisialisasi Data (RBAC, Outlets, Users)
├── resources/views/           # Antarmuka Blade (POS UI, Dashboard)
├── vercel.json                # Konfigurasi Deployment Awan (IaC)
└── README.md                  # Dokumen Referensi Otoritatif ini
```

---
*Developed with 💎 Precision by Antigravity — Engineered for Enterprise-Grade Excellence.*
