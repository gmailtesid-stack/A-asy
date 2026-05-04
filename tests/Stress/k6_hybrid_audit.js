import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { browser } from 'k6/experimental/browser';

// 1. Konfigurasi Skenario Hybrid (API & Browser)
export const options = {
  scenarios: {
    // Skenario 1: Load Test Back-end (API)
    backend_load: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '1m', target: 20 },  // Pemanasan
        { duration: '3m', target: 50 },  // Beban Puncak Kasir
        { duration: '1m', target: 100 }, // Stress Test
        { duration: '1m', target: 0 },   // Pendinginan
      ],
      exec: 'apiTesting', // Memanggil fungsi apiTesting
    },
    // Skenario 2: Visual Regression (Front-end)
    frontend_visual: {
      executor: 'shared-iterations',
      options: {
        browser: {
          type: 'chromium',
        },
      },
      vus: 1,
      iterations: 1, // Cukup 1 iterasi untuk memotret UI
      exec: 'visualTesting', // Memanggil fungsi visualTesting
    },
  },
  thresholds: {
    'http_req_duration': ['p(95)<1500'], 
    'http_req_failed': ['rate<0.01'],    
    'browser_web_vital_lcp': ['p(95)<2500'], // Pemuatan UI tidak boleh lebih dari 2.5 detik
  },
};

const BASE_URL = 'https://nusa-cyber.vercel.app/api';
const FRONTEND_URL = 'https://nusa-cyber.vercel.app/pos';

// ====================================================================
// FUNGSI 1: PENGUJIAN LOGIKA BACK-END & DATABASE (TiDB)
// ====================================================================
export function apiTesting() {
  const headers = { 'Content-Type': 'application/json' };

  // --- SKENARIO POS & ACCOUNTING ---
  group('POS Transaction & Auto-Journaling', function () {
    const posPayload = JSON.stringify({
      branch_id: 'BRANCH-DEPOK',
      customer_id: 'CUST-001',
      items: [{ product_id: 'PROD-A', qty: 1, price: 250000 }],
      payment_method: 'QRIS'
    });
    const res = http.post(`${BASE_URL}/pos/checkout`, posPayload, { headers });
    check(res, {
      'POS: Transaksi Berhasil (200)': (r) => r.status === 200,
      'Back-end: Jurnal HPP Terbentuk': (r) => r.json().accounting_synced === true,
    });
  });

  sleep(1);

  // --- SKENARIO WMS IN-TRANSIT ---
  group('WMS Multi-Branch Mutation', function () {
    const mutationPayload = JSON.stringify({
      from_branch: 'CENTRAL-WH',
      to_branch: 'BRANCH-DEPOK',
      items: [{ product_id: 'PROD-A', qty: 10 }]
    });
    const res = http.post(`${BASE_URL}/wms/mutate`, mutationPayload, { headers });
    check(res, {
      'WMS: Mutasi Berhasil': (r) => r.status === 200,
      'Logika: Status In-Transit Aktif': (r) => r.json().status === 'IN_TRANSIT',
    });
  });

  sleep(1);

  // --- SKENARIO CRM TIERING ---
  group('CRM Tiering Logic', function () {
    const res = http.get(`${BASE_URL}/crm/customer-tier?id=CUST-001`);
    check(res, {
      'CRM: Get Data Berhasil': (r) => r.status === 200,
      'Logic: Auto-Upgrade Check': (r) => r.json().tier_updated !== undefined,
    });
  });

  // --- SKENARIO AUDIT / STOCK OPNAME ---
  group('Inventory Audit (Stock Freezing)', function () {
    const auditPayload = JSON.stringify({
      bin_location: 'RAK-A1',
      product_id: 'PROD-A',
      physical_count: 50
    });
    const res = http.post(`${BASE_URL}/wms/stock-opname`, auditPayload, { headers });
    check(res, {
      'Audit: Submit Berhasil': (r) => r.status === 200,
      'Back-end: Rak Terkunci (is_frozen)': (r) => r.json().stock_locked === true,
    });
  });
  
  sleep(2);
}

// ====================================================================
// FUNGSI 2: PENGUJIAN UI/UX & VISUAL REGRESSION (Vercel Edge)
// ====================================================================
export async function visualTesting() {
  const page = browser.newPage();

  try {
    await page.goto(FRONTEND_URL);
    await page.waitForLoadState('networkidle');

    // 1. Memastikan Identitas Brand Semicolon Muncul
    const logoSemicolon = page.locator('id=semicolon-brand-logo');
    check(logoSemicolon, {
      'UI: Logo Semicolon (;) ter-render dengan benar': (el) => el.isVisible(),
    });

    // 2. Memastikan Estetika "Stealth / Full Black" Tidak Berubah
    // Mengecek apakah background utama menggunakan warna gelap yang kaku
    const mainBackground = page.locator('body');
    const bgColor = await mainBackground.evaluate((node) => window.getComputedStyle(node).backgroundColor);
    
    // Asumsi: Warna gelap RGB(0,0,0) atau varian stealth kaku lainnya, tidak ada RGB mencolok
    check(bgColor, {
      'UI: Mode Stealth (Kaku/Gelap) Aktif': (color) => color === 'rgb(10, 10, 10)' || color === 'rgb(0, 0, 0)',
    });

    // 3. Visual Regression: Mengambil Screenshot untuk Komparasi Pipeline
    // Screenshot ini akan disimpan sebagai artefak CI/CD untuk memastikan tidak ada layout berantakan
    await page.screenshot({ path: 'screenshots/pos-dashboard-stealth.png' });

    // 4. Simulasi Flow Transaksi Kasir di UI
    await page.locator('input[name="barcode_scanner"]').type('PROD-A');
    await page.locator('button[id="btn-checkout"]').click();
    
    const receiptModal = page.locator('div[id="receipt-modal"]');
    check(receiptModal, {
      'UI: Modal Struk Muncul Setelah Checkout': (el) => el.isVisible(),
    });

  } finally {
    page.close();
  }
}
