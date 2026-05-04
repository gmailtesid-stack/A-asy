# Disaster Recovery Plan & High Availability Strategy

Sistem E-ASY ERP telah diarsiteki dengan pertimbangan *Enterprise Grade* untuk memastikan **Zero Downtime** dan ketahanan data.

## 1. High Availability (HA)
*   **Frontend & Application Layer (Vercel):** Aplikasi Laravel dide-deploy di infrastruktur Serverless Vercel. Tidak ada *single point of failure*. Vercel Edge Network akan me-routing *traffic* ke node terdekat dan secara otomatis melakukan *auto-scaling* jika terjadi lonjakan transaksi POS secara masif.
*   **Database Layer (TiDB Cloud):** Kami menggunakan database SQL terdistribusi. TiDB secara *native* melakukan replikasi data menggunakan konsensus algoritma Raft. Jika satu *storage node* mati, *node* replikanya akan langsung mengambil alih operasional tanpa intervensi manual.

## 2. Backup & Point-in-Time Recovery (PiTR)
*   **Automated Snapshot:** Database TiDB di-backup secara otomatis setiap hari.
*   **PiTR:** TiDB mendukung *Point-in-Time Recovery*. Jika Admin secara tidak sengaja menghapus seluruh data Master Produk tanpa klausa `WHERE`, *Database Administrator* dapat me- *restore* cluster TiDB persis ke detik sebelum *query* tersebut dieksekusi.

## 3. Data Encryption
*   **Encryption in Transit:** Seluruh komunikasi antara Vercel, TiDB, dan Kasir dilindungi dengan TLS 1.3.
*   **Encryption at Rest:** Data pelanggan dan keuangan dienkripsi secara transparan (TDE) oleh infrastruktur cloud (AWS/GCP) yang menaungi TiDB Cloud.

## 4. Rate Limiting (API Security)
*   Integrasi pihak ketiga (misal: Tokopedia, Shopee) dibatasi pada kecepatan `60 requests / minute` menggunakan `Illuminate\Routing\Middleware\ThrottleRequests` milik Laravel untuk mencegah eksploitasi dan serangan DDoS di level aplikasi.
