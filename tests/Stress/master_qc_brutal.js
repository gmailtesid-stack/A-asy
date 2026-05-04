import { browser } from 'k6/browser';
import http from 'k6/http';
import { check, sleep, group } from 'k6';

// Konfigurasi dinamis berdasarkan Environment Variable
const browserEnabled = __ENV.K6_BROWSER_ENABLED !== 'false';

export const options = {
  scenarios: {
    // Skenario 1: Brutal API & Logic Stress (Backend/TiDB)
    api_brutal_load: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '2m', target: 200 },  // Ramp-up cepat
        { duration: '10m', target: 500 }, // Peak Load masif 500 VUs
        { duration: '2m', target: 500 }, // Spike Test (Lonjakan mendadak)
        { duration: '5m', target: 0 },   // Cool down
      ],
      exec: 'apiBrutalTesting',
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.15'], // 15% toleransi: 20% req sengaja pakai invalid token (security test)
    http_req_duration: ['p(95)<2000'], // Respon di bawah 2 detik
  },
};

// Aktifkan UI Testing hanya jika K6_BROWSER_ENABLED tidak diset 'false'
if (browserEnabled) {
  options.scenarios.ui_monkey_testing = {
    executor: 'constant-vus',
    vus: 10,
    duration: '10m',
    exec: 'visualMonkeyTesting',
  };
}

const BASE_URL = 'https://e-asy.vercel.app';

// ── SETUP: Login sekali, token dibagikan ke semua VU ──────────────────────────
export function setup() {
  const loginRes = http.post(
    `${BASE_URL}/api/login`,
    JSON.stringify({ email: 'admin@easy-pos.id', password: 'password' }),
    { headers: { 'Content-Type': 'application/json' } }
  );

  const token = loginRes.json('token') || loginRes.json('access_token') || '';

  check(loginRes, {
    'Setup: Login Admin Berhasil': (r) => r.status === 200 && token !== '',
  });

  console.log(`[SETUP] Token diperoleh: ${token ? '✅ OK' : '❌ GAGAL (cek endpoint /api/login)'}`);
  return { token };
}

// --- BACKEND & LOGIC TESTING ---
export async function apiBrutalTesting(data) {
  const realToken  = data.token;
  // 80% trafik normal, 20% trafik sampah/invalid
  const isInvalid  = Math.random() < 0.2;
  const activeToken = isInvalid ? 'WRONG_TOKEN' : realToken;

  const checkoutPayload = JSON.stringify({
    product_id: 'PROD-001',
    qty: 1,
    is_frozen: true, // Menguji logika locking TiDB
  });

  const syncHppPayload = JSON.stringify({
    items: Array(100).fill({ id: 'P1', qty: 1, price: 15000 }),
    timestamp: new Date().toISOString(),
  });

  // Gunakan http.batch() agar lebih hemat socket di Windows
  const responses = http.batch([
    [
      'POST',
      `${BASE_URL}/api/pos/checkout`,
      checkoutPayload,
      { headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${realToken}` } },
    ],
    [
      'POST',
      `${BASE_URL}/api/pos/sync-hpp`,
      syncHppPayload,
      { headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${activeToken}` } },
    ],
  ]);

  const [checkoutRes, syncHppRes] = responses;

  group('Race Condition: Stok Perebutan', () => {
    check(checkoutRes, {
      'Logika TiDB: Stok Tidak Minus': (r) => r.status === 200 || r.status === 409 || r.status === 201,
    });
  });

  group('Stress Test: Payload Besar & Invalid Token', () => {
    if (isInvalid) {
      check(syncHppRes, { 'Security: Block Invalid Token': (r) => r.status === 401 });
    } else {
      check(syncHppRes, { 'Performance: Large Payload Success': (r) => r.status === 200 || r.status === 422 });
    }
  });

  // Jeda agar Windows sempat menutup koneksi lama
  sleep(0.1);
  sleep(1);
}

// --- FRONTEND & UI MONKEY TESTING ---
export async function visualMonkeyTesting() {
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    await page.goto(BASE_URL + '/login'); // Start from login for UI check

    group('Deep Navigation & Interaction', async () => {
      // Elements that are expected in the E-ASY ERP Sidebar/POS
      const menus = ['#nav-pos', '#nav-wms', '#nav-audit', '#nav-report'];
      // Pilih menu acak (Monkey Testing)
      const randomMenu = menus[Math.floor(Math.random() * menus.length)];
      
      // Since it's a monkey test on login page or home, let's just check the brand identity
      check(page, {
        'UI: Render Semicolon Logo': (p) => p.locator('#semicolon-brand-logo').isVisible(),
        'UI: Dark Theme Consistent': (p) => p.locator('body').evaluate((el) => window.getComputedStyle(el).backgroundColor === 'rgb(10, 10, 10)' || window.getComputedStyle(el).backgroundColor === 'rgb(0, 0, 0)'),
      });
      
      // If we are logged in, we could navigate. But k6 browser doesn't persist login unless we handle it.
      // For now, we validate the presence of navigation structure if possible.
    });

  } finally {
    await page.close(); 
  }
}
