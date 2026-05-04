# 🔄 E-ASY BUSINESS WORKFLOWS & LOGIC SPECIFICATION

Dokumen ini menjelaskan alur logika bisnis paling kritikal dalam sistem E-ASY untuk memastikan operasional berjalan lancar dan akurat.

---

## 🛒 1. ALUR TRANSAKSI POS (POINT OF SALE)

Proses ini dirancang untuk kecepatan dan akurasi stok menggunakan prinsip **ACID Transaction**.

1.  **Selection**: Operator memasukkan produk via pencarian atau scan barcode.
2.  **Validation**: Sistem mengecek ketersediaan stok fisik di `inventories` outlet terkait.
3.  **Atomic FIFO Calculation**: Saat kasir memproses pembayaran:
    -   Sistem menjalankan `lockForUpdate()` pada baris log stok database.
    -   Sistem menghitung **HPP (COGS)** berdasarkan metode **FIFO (First-In First-Out)** murni.
    -   HPP tercatat secara permanen (`immutable`) di dalam detail transaksi.
4.  **Deduction**: Stok dikurangi, invoice dibuat, dan `InventoryLog` dihasilkan.
5.  **Automated Journaling**: Sistem otomatis membuat entri buku besar (Buku Kas, Pendapatan, HPP, Hutang Pajak).

---

## 🧾 2. ALUR AKUNTANSI OTOMATIS (FINANCE)

Setiap pergerakan nilai barang dan uang diubah menjadi bahasa akuntansi secara real-time.

1.  **POS Journal**: Debit Kas/Bank, Kredit Pendapatan (Gross), Debit Diskon Penjualan (jika ada), Kredit Hutang Pajak.
2.  **HPP Journal**: Debit Beban HPP (COGS), Kredit Persediaan Barang (berdasarkan nilai FIFO).
3.  **Opname Adjustment**: Mencatat selisih stok fisik vs sistem ke akun beban selisih persediaan.
4.  **Commission Accrual**: Performa penjualan operator dihitung menjadi potensi komisi yang dapat dipantau di dashboard HR.

---

## 🚛 3. ALUR LOGISTIK INBOUND (WMS MASUK)
...

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

## 🛡️ 6. ALUR PERSETUJUAN (WORKFLOW APPROVAL)

Mencegah fraud dengan menerapkan lapis persetujuan untuk operasi sensitif.

1.  **Pengajuan (Trigger)**: Tindakan seperti *Stock Opname* atau *Pencatatan Biaya (Expense)* tidak akan langsung mengubah sistem. Status ditandai `pending`.
2.  **Gatekeeping**: `ApprovalController` menahan dokumen. Notifikasi muncul di dashboard Manajer.
3.  **Keputusan**:
    -   **Disetujui**: Sistem menjalankan *side-effect* (contoh: memotong stok atau menjurnal pengeluaran kas).
    -   **Ditolak**: Dokumen dibatalkan dengan catatan (notes).

---

## 💸 7. MANAJEMEN BIAYA (EXPENSES) & LABA BERSIH

Untuk mendapatkan Laba Bersih yang sesungguhnya (Bukan hanya Laba Kotor/Margin).

1.  **Input Biaya**: Outlet/HO mencatat OPEX (Sewa, Gaji, Lakban, Listrik).
2.  **Verifikasi & Jurnal**: Setelah di-*approve*, sistem otomatis menjurnal: Debit Beban (5100), Kredit Kas (1100).
3.  **Net Profit Calculation**: Sistem Business Intelligence mengagregasi Laba Kotor (Pendapatan - HPP) dikurangi Total OPEX.

---

## 🕵️ 8. AUDIT TRAIL & KEPATUHAN (COMPLIANCE)

Integritas data forensik untuk sistem *Enterprise-Grade*.

1.  **Delta Logging**: Sistem merekam spesifik *JSON Diff* (Data Lama vs Data Baru) pada setiap `update`.
2.  **Forensic Capture**: Alamat IP, User Agent, dan Endpoint URL yang mengeksekusi diikat pada *log*.
3.  **Masking**: Data sensitif (Password/Token) otomatis disensor `[MASKED]` untuk mencegah kebocoran internal.

---
*Logic Integrity: Guaranteed by Laravel Eloquent, Database Constraints, and Service Architectures.*
