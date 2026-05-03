# 🔄 E-ASY BUSINESS WORKFLOWS & LOGIC SPECIFICATION

Dokumen ini menjelaskan alur logika bisnis paling kritikal dalam sistem E-ASY untuk memastikan operasional berjalan lancar dan akurat.

---

## 🛒 1. ALUR TRANSAKSI POS (POINT OF SALE)

Proses ini dirancang untuk kecepatan dan akurasi stok menggunakan prinsip **ACID Transaction**.

1.  **Selection**: Operator memasukkan produk via pencarian atau scan barcode.
2.  **Validation**: Sistem mengecek ketersediaan stok fisik di `inventories` outlet terkait.
3.  **Atomic Locking**: Saat kasir memproses pembayaran:
    -   Sistem menjalankan `lockForUpdate()` pada baris stok database.
    -   Mencegah tabrakan data jika ada dua kasir menjual barang yang sama di detik yang sama.
4.  **Deduction**: Stok dikurangi, invoice dibuat, dan `InventoryLog` dihasilkan.
5.  **Notification**: Jika stok di bawah `min_quantity`, notifikasi dikirim ke admin secara real-time.

---

## 🚛 2. ALUR LOGISTIK INBOUND (WMS MASUK)

Memastikan barang yang dipesan dari supplier masuk ke gudang dengan benar.

1.  **Purchase Order (PO)**: Admin membuat dokumen pemesanan barang. Status: `pending`.
2.  **Confirmation**: Supervisor memvalidasi harga dan jumlah. Status: `confirmed`.
3.  **Reception (GRN)**: Staff gudang menerima barang, mencocokkan fisik dengan dokumen, dan menginput jumlah yang diterima.
4.  **Putaway**: Sistem otomatis menambah stok dan mencatat lokasi rak fisik (`Location`). Status PO: `received`.

---

## 📦 3. ALUR LOGISTIK OUTBOUND (WMS KELUAR)

Mengelola pesanan pelanggan dari berbagai kanal hingga sampai ke kurir.

1.  **Sales Order (SO)**: Pesanan masuk (Manual atau via API Marketplace).
2.  **Picking**: Sistem membuat daftar pengambilan barang berdasarkan lokasi rak terdekat. Staff menandai barang: `Found`, `Partial`, atau `Not Found`.
3.  **Packing**: Validasi akhir barang yang diambil sebelum dibungkus.
4.  **Shipping**: Input nomor resi dan penyerahan barang ke ekspedisi.

---

## 🌐 4. ALUR SINKRONISASI MARKETPLACE (OMS)

Menjaga keselarasan stok antara gudang fisik dan toko online.

1.  **Event Trigger**: Setiap ada perubahan stok fisik (Penjualan, GRN, Adjustment), sistem memicu `SyncMarketplaceJob`.
2.  **API Communication**: Job berjalan di latar belakang (Background) untuk mengirimkan data stok terbaru ke kanal yang terhubung (Shopee/Tokopedia).
3.  **Error Handling**: Jika API marketplace sibuk atau down, sistem melakukan *retry* otomatis hingga 3 kali.
4.  **Status Monitoring**: Status sinkronisasi terakhir dapat dipantau di menu `Marketplace Channels`.

---

## 🔄 5. ALUR MUTASI ANTAR OUTLET (TRANSFER)

Memindahkan stok antar cabang tanpa kehilangan jejak.

1.  **Request**: Outlet A meminta barang ke Outlet B.
2.  **Shipping**: Outlet B mengirim barang; stok di Outlet B dikurangi dengan status `In-Transit`.
3.  **Receipt**: Outlet A menerima barang; stok di Outlet A bertambah otomatis.

---
*Logic Integrity: Guaranteed by Laravel Eloquent & Database Constraints.*
