# 🗄️ E-ASY DATABASE DICTIONARY (SCHEMA DEEP-DIVE)

Dokumen ini mendokumentasikan setiap tabel utama, kolom penting, dan relasi data untuk memastikan integritas database E-ASY tetap terjaga.

---

## 🏬 1. TABEL MASTER (MULTI-TENANT)

### **Table: `outlets`**
*Pusat data cabang/toko.*
- `code`: Kode unik outlet (e.g. OTL-01). **Indexed**.
- `name`: Nama cabang.
- `latitude` / `longitude`: Koordinat GPS untuk pelaporan peta aset.

### **Table: `users`**
*Pengguna sistem.*
- `role`: Peran utama (super_admin, manager, cashier).
- `outlet_id`: Referensi ke outlet asal (Multi-tenancy isolation).
- `is_active`: Status akun (aktif/nonaktif).

---

## 📦 2. MANAJEMEN PRODUK & STOK

### **Table: `products`**
*Katalog master barang.*
- `sku`: Stock Keeping Unit unik. **Unique Indexed**.
- `csku`: Channel SKU untuk sinkronisasi marketplace.
- `price` / `cost_price`: Harga jual dan modal.
- `status`: Lifecycle produk (`live`, `draft`, `under_review`).

### **Table: `inventories`**
*Saldo stok terdistribusi.*
- `quantity`: Jumlah stok fisik saat ini.
- `min_quantity`: Ambang batas untuk alert stok rendah.
- `location_id`: Lokasi rak spesifik dalam gudang.

---

## 🛒 3. TRANSAKSI & LOGISTIK

### **Table: `transactions`**
*Rekaman POS Penjualan.*
- `invoice_number`: Nomor unik faktur. **Unique Indexed**.
- `payment_method`: cash, transfer, qris, card.
- `subtotal`, `tax`, `discount`, `total`: Rincian finansial.

### **Table: `purchase_orders` (PO)**
*Pengadaan barang dari supplier.*
- `po_number`: Nomor unik PO.
- `status`: pending, confirmed, received.

### **Table: `grn` (Goods Received Note)**
*Penerimaan barang masuk.*
- `received_at`: Tanggal fisik barang masuk.
- `purchase_order_id`: Relasi ke dokumen PO asal.

---

## 🛡️ 4. KEAMANAN & AUDIT

### **Table: `inventory_logs`**
*Audit Trail Mutasi Barang.*
- `quantity_before` / `quantity_after`: Perubahan saldo stok.
- `reference`: Nomor dokumen pemicu (Invoice, GRN, Adjustment).
- `type`: in, out, adjustment, transfer.

### **Table: `roles` & `permissions`**
*Manajemen RBAC.*
- `slug`: Kode unik izin (e.g. `view-reports`).
- `role_id` <-> `permission_id`: Tabel pivot untuk mapping izin.

---

## 🚀 5. OPTIMASI & INDEXING strategy

Sistem menggunakan strategi indexing khusus di file `add_indexes_for_performance.php`:
- **Indeks Status**: Pada tabel `transactions`, `purchase_orders`, `sales_orders`, dan `products`.
- **Indeks Pencarian**: Pada kolom `invoice_number`, `po_number`, `sku`, dan `transfer_number`.
- **Indeks Audit**: Pada tabel `inventory_logs` (reference, type) untuk pelacakan cepat.

---
*Database Engine: Distributed MySQL (TiDB Cloud Compatible).*
