import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { browser } from 'k6/browser';

// ====================================================================
// 1. KONFIGURASI QC (API Load + Browser UI)
// ====================================================================
export const options = {
  scenarios: {
    // Skenario 1: Stress Test Back-end (TiDB & Vercel API)
    api_load: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '1m', target: 20 },  // Pemanasan (Warm-up)
        { duration: '3m', target: 50 },  // Beban Operasional Puncak
        { duration: '1m', target: 100 }, // Stress Test maksimal
        { duration: '1m', target: 0 },   // Pendinginan
      ],
      exec: 'apiTesting', 
    },
    // Skenario 2: Visual Regression (Front-end & Aesthetic Check)
    ui_visual: {
      executor: 'shared-iterations',
      options: {
        browser: {
          type: 'chromium',
        },
      },
      vus: 1,
      iterations: 1, 
      exec: 'visualTesting', 
    },
  },
  thresholds: {
    'http_req_duration': ['p(95)<1500'], // 95% API respon < 1.5 detik
    'http_req_failed': ['rate<0.01'],    // Toleransi error API < 1%
    'browser_web_vital_lcp': ['p(95)<2500'], // Render UI < 2.5 detik
  },
};

// URL Spesifik E-ASY Project
const BASE_URL = 'https://e-asy.vercel.app/api';
const FRONTEND_URL = 'https://e-asy.vercel.app';

// ====================================================================
// FUNGSI 1: PENGUJIAN LOGIKA BACK-END & TiDB CONCURRENCY
// ====================================================================
export function apiTesting() {
  const headers = { 'Content-Type': 'application/json' };

  // --- SKENARIO 1: POS & ACCOUNTING (Hit ke TiDB) ---
  group('POS Transaction & Auto-Journaling', function () {
    const posPayload = JSON.stringify({
      branch_id: 'BRANCH-DEPOK',
      customer_id: 'CUST-001',
      items: [{ product_id: 'PROD-A', qty: 1, price: 250000 }],
      payment_method: 'QRIS'
    });
    const res = http.post(`${BASE_URL}/pos/checkout`, posPayload, { headers });
    
    check(res, {
      'API POS: Status 200 (Berhasil)': (r) => r.status === 200 || r.status === 201,
      'Database: Jurnal HPP Tersinkronisasi': (r) => r.json().accounting_synced === true,
    });
  });

  sleep(1);

  // --- SKENARIO 2: WMS IN-TRANSIT (Stock Isolation) ---
  group('WMS Multi-Branch Mutation', function () {
    const mutationPayload = JSON.stringify({
      from_branch: 'CENTRAL-WH',
      to_branch: 'BRANCH-DEPOK',
      items: [{ product_id: 'PROD-A', qty: 10 }]
    });
    const res = http.post(`${BASE_URL}/wms/mutate`, mutationPayload, { headers });
    
    check(res, {
      'API WMS: Mutasi Dikirim': (r) => r.status === 200,
      'Logika TiDB: Status Stok IN_TRANSIT': (r) => r.json().status === 'IN_TRANSIT',
    });
  });

  sleep(1);

  // --- SKENARIO 3: AUDIT & STOCK FREEZING ---
  group('Inventory Audit (Cycle Counting)', function () {
    const auditPayload = JSON.stringify({
      bin_location: 'RAK-A1',
      product_id: 'PROD-A',
      physical_count: 48 // Blind count input
    });
    const res = http.post(`${BASE_URL}/wms/stock-opname`, auditPayload, { headers });
    
    check(res, {
      'API Audit: Submit Berhasil': (r) => r.status === 200,
      'Logika TiDB: Baris Data Terkunci (is_frozen)': (r) => r.json().stock_locked === true,
    });
  });
  
  sleep(2);
}

// ====================================================================
// FUNGSI 2: PENGUJIAN VISUAL REGRESSION & ESTETIKA FRONT-END
// ====================================================================
export async function visualTesting() {
  const page = browser.newPage();

  try {
    // 1. Uji Tampilan Utama (POS/Dashboard)
    await page.goto(`${FRONTEND_URL}/login`); // Testing login page for visual stealth check
    await page.waitForLoadState('networkidle');

    // Cek Identitas Brand Semicolon
    const logoSemicolon = page.locator('id=semicolon-brand-logo');
    check(logoSemicolon, {
      'UI Dashboard: Logo Semicolon (;) ter-render': (el) => el.isVisible(),
    });

    // Cek Estetika "Stealth / Full Black"
    const mainBackground = page.locator('body');
    const bgColor = await mainBackground.evaluate((node) => window.getComputedStyle(node).backgroundColor);
    check(bgColor, {
      'UI Dashboard: Mode Stealth (Tanpa RGB/Warna Mencolok)': (color) => color === 'rgb(10, 10, 10)' || color === 'rgb(0, 0, 0)',
    });

    // Ambil Screenshot UI Normal
    await page.screenshot({ path: 'qc-artifacts/e-asy-dashboard-stealth.png' });

    // 2. Uji Halaman Error 403 Forbidden (Validasi Desain Kaku)
    // Forcing 403 by trying to access restricted admin area
    await page.goto(`${FRONTEND_URL}/admin/restricted-area`); 
    await page.waitForLoadState('networkidle');

    const errorPageHeading = page.locator('h1');
    const headingText = await errorPageHeading.textContent();
    
    check(headingText, {
      'UI Error: Menampilkan "403 Forbidden"': (text) => text.includes('403 Forbidden'),
    });

    // Ambil Screenshot UI Error 403
    await page.screenshot({ path: 'qc-artifacts/e-asy-403-rigid-server.png' });

  } finally {
    page.close();
  }
}
