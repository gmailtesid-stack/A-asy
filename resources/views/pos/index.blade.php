@extends('layouts.app')

@section('title', 'Kasir — E-ASY POS')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kasir / POS</li>
@endsection

@push('styles')
<style>
    .pos-wrap { display: grid; grid-template-columns: 1fr 380px; gap: 1.25rem; height: calc(100vh - var(--topbar-h) - 3.5rem); }

    /* Product Grid */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .75rem; overflow-y: auto; padding-right: .25rem; }
    .product-card {
        background: #fff;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all .2s;
        border: 2px solid transparent;
        text-align: center;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .product-card:hover { transform: translateY(-2px); border-color: #2563eb; box-shadow: 0 6px 20px rgba(37,99,235,.15); }
    .product-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: .5rem; }
    .product-card .p-name { font-weight: 600; font-size: .82rem; color: #1e293b; }
    .product-card .p-price { color: #2563eb; font-weight: 700; font-size: .9rem; }
    .product-card .p-stock { font-size: .7rem; color: #64748b; }

    /* Cart Panel */
    .cart-panel {
        background: #fff;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 8px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .cart-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; }
    .cart-items { flex: 1; overflow-y: auto; padding: .75rem; }
    .cart-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6rem .5rem;
        border-bottom: 1px solid #f8fafc;
        animation: slideIn .2s ease;
    }
    @keyframes slideIn { from { opacity:0; transform: translateX(10px); } to { opacity:1; transform: translateX(0); } }
    .cart-item .name { font-size: .82rem; font-weight: 600; flex: 1; }
    .qty-btn { border: none; background: #f1f5f9; border-radius: 6px; width: 28px; height: 28px; cursor: pointer; font-size: .9rem; transition: background .15s; }
    .qty-btn:hover { background: #e2e8f0; }
    .qty-display { min-width: 28px; text-align: center; font-weight: 700; font-size: .9rem; }

    /* Cart Summary */
    .cart-summary { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; background: #f8fafc; }
    .sum-row { display: flex; justify-content: space-between; font-size: .85rem; color: #64748b; margin-bottom: .3rem; }
    .sum-row.total { color: #1e293b; font-weight: 700; font-size: 1.1rem; }

    .payment-select { width: 100%; padding: .6rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .875rem; margin: .75rem 0 .5rem; }
    .btn-checkout {
        width: 100%; padding: .85rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff; border: none; border-radius: 10px;
        font-size: 1rem; font-weight: 700; cursor: pointer;
        transition: all .2s;
        box-shadow: 0 4px 15px rgba(37,99,235,.35);
    }
    .btn-checkout:hover { transform: translateY(-1px); }
    .btn-checkout:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    /* Search */
    .search-input {
        width: 100%; padding: .6rem 1rem .6rem 2.5rem;
        border: 1px solid #e2e8f0; border-radius: 10px;
        font-size: .875rem; background: #f8fafc; outline: none;
        transition: border-color .2s;
    }
    .search-input:focus { border-color: #2563eb; background: #fff; }
    .search-wrap { position: relative; margin-bottom: 1rem; }
    .search-wrap i { position: absolute; left: .8rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }

    .empty-cart { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
    .empty-cart i { font-size: 3rem; display: block; margin-bottom: .5rem; }
</style>
@endpush

@section('content')
<div class="pos-wrap">

    {{-- ── PRODUCT PANEL ─── --}}
    <div class="d-flex flex-column gap-3">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari produk...">
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
            <div class="product-card"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-stock="{{ $product->inventories->where('outlet_id', auth()->user()->outlet_id)->first()?->quantity ?? 0 }}"
                 data-image="{{ $product->image_url }}"
                 onclick="addToCart(this)">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" onerror="this.src='https://placehold.co/80x80?text=IMG'">
                <div class="p-name">{{ $product->name }}</div>
                <div class="p-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                <div class="p-stock">Stok: {{ $product->inventories->where('outlet_id', auth()->user()->outlet_id)->first()?->quantity ?? 0 }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── CART PANEL ─── --}}
    <div class="cart-panel">
        <div class="cart-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-cart3 me-2"></i>Pesanan</h6>
            <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                <i class="bi bi-trash"></i> Kosongkan
            </button>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="empty-cart" id="emptyCart">
                <i class="bi bi-cart-x"></i>
                Belum ada produk dipilih
            </div>
        </div>

        <div class="cart-summary">
            <div class="sum-row"><span>Subtotal</span><span id="sumSubtotal">Rp 0</span></div>
            <div class="sum-row"><span>Diskon</span><span id="sumDiscount">Rp 0</span></div>
            <div class="sum-row"><span>PPN (11%)</span><span id="sumTax">Rp 0</span></div>
            <hr class="my-2">
            <div class="sum-row total"><span>TOTAL</span><span id="sumTotal">Rp 0</span></div>

            <select class="payment-select" id="paymentMethod">
                <option value="cash">💵 Tunai (Cash)</option>
                <option value="qris">📱 QRIS</option>
                <option value="transfer">🏦 Transfer Bank</option>
                <option value="card">💳 Kartu Debit/Kredit</option>
            </select>

            <div id="cashRow" class="mb-2">
                <input type="number" id="cashAmount" class="form-control form-control-sm"
                       placeholder="Nominal uang diterima" min="0">
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Kembalian:</small>
                    <small class="fw-bold text-success" id="changeDisplay">Rp 0</small>
                </div>
            </div>

            <button class="btn-checkout" id="btnCheckout" onclick="processCheckout()" disabled>
                <i class="bi bi-bag-check-fill me-2"></i>Proses Transaksi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = {};
const TAX_RATE = 0.11;

// ── Tambah ke keranjang ───────────────────────────────────────────
function addToCart(el) {
    const id    = el.dataset.id;
    const name  = el.dataset.name;
    const price = parseFloat(el.dataset.price);
    const stock = parseInt(el.dataset.stock);

    if (!cart[id]) {
        cart[id] = { id, name, price, qty: 0, stock };
    }

    if (cart[id].qty >= stock) {
        alert(`⚠️ Stok ${name} tidak mencukupi!`);
        return;
    }

    cart[id].qty++;
    renderCart();
}

// ── Render keranjang ──────────────────────────────────────────────
function renderCart() {
    const container = document.getElementById('cartItems');
    const empty     = document.getElementById('emptyCart');
    const items     = Object.values(cart).filter(i => i.qty > 0);

    if (items.length === 0) {
        empty.style.display = 'block';
        container.querySelectorAll('.cart-item').forEach(e => e.remove());
        updateSummary();
        return;
    }

    empty.style.display = 'none';
    container.querySelectorAll('.cart-item').forEach(e => e.remove());

    items.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <div class="name">${item.name}</div>
            <button class="qty-btn" onclick="changeQty('${item.id}', -1)">−</button>
            <span class="qty-display">${item.qty}</span>
            <button class="qty-btn" onclick="changeQty('${item.id}', 1)">+</button>
            <small class="text-muted" style="min-width:70px;text-align:right;">
                Rp ${fmt(item.price * item.qty)}
            </small>
        `;
        container.appendChild(div);
    });

    updateSummary();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function clearCart() { cart = {}; renderCart(); }

// ── Update ringkasan harga ────────────────────────────────────────
function updateSummary() {
    const items    = Object.values(cart).filter(i => i.qty > 0);
    const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
    const tax      = subtotal * TAX_RATE;
    const total    = subtotal + tax;

    document.getElementById('sumSubtotal').textContent = 'Rp ' + fmt(subtotal);
    document.getElementById('sumTax').textContent      = 'Rp ' + fmt(tax);
    document.getElementById('sumTotal').textContent    = 'Rp ' + fmt(total);

    updateChange(total);
    document.getElementById('btnCheckout').disabled = items.length === 0;
}

function updateChange(total) {
    const cash   = parseFloat(document.getElementById('cashAmount').value) || 0;
    const change = Math.max(0, cash - total);
    document.getElementById('changeDisplay').textContent = 'Rp ' + fmt(change);
}

document.getElementById('cashAmount').addEventListener('input', () => {
    const items    = Object.values(cart).filter(i => i.qty > 0);
    const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
    updateChange(subtotal + subtotal * TAX_RATE);
});

document.getElementById('paymentMethod').addEventListener('change', e => {
    document.getElementById('cashRow').style.display = e.target.value === 'cash' ? 'block' : 'none';
});

// ── Proses checkout ───────────────────────────────────────────────
async function processCheckout() {
    const items = Object.values(cart).filter(i => i.qty > 0);
    if (!items.length) return;

    const btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    try {
        const res = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                items: items.map(i => ({ product_id: i.id, quantity: i.qty })),
                payment_method: document.getElementById('paymentMethod').value,
                cash_amount: parseFloat(document.getElementById('cashAmount').value) || null,
            }),
        });

        const data = await res.json();

        if (data.success) {
            alert(`✅ Transaksi Berhasil!\nNo: ${data.invoice}\nKembalian: Rp ${fmt(data.change)}`);
            cart = {};
            renderCart();
            window.open(`/pos/receipt/${data.data.id}`, '_blank');
        } else {
            alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan.'));
        }
    } catch (e) {
        alert('❌ Error koneksi: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-bag-check-fill me-2"></i>Proses Transaksi';
    }
}

// ── Search produk ─────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = card.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
});

function fmt(n) { return Math.round(n).toLocaleString('id-ID'); }
</script>
@endpush
