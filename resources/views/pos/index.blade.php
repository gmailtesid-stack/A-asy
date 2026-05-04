@extends('layouts.app')

@section('title', 'Kasir — E-ASY POS')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kasir / POS</li>
@endsection

@push('styles')
@push('styles')
<style>
    .pos-container { 
        display: grid; 
        grid-template-columns: 1fr 420px; 
        gap: 2rem; 
        height: calc(100vh - var(--topbar-h) - 5rem);
    }

    /* Product Section */
    .product-section { display: flex; flex-direction: column; gap: 1.5rem; overflow: hidden; }
    
    .product-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
        gap: 1.25rem; 
        overflow-y: auto; 
        padding-bottom: 2rem;
    }
    
    .product-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        text-align: center;
        position: relative;
    }
    .product-card:hover { 
        transform: translateY(-8px); 
        border-color: var(--primary); 
        box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.1), 0 10px 10px -5px rgba(99, 102, 241, 0.04);
    }
    .product-card img { 
        width: 100px; height: 100px; 
        object-fit: cover; 
        border-radius: 16px; 
        margin-bottom: 1rem; 
        box-shadow: 0 4px 12px rgba(0,0,0,.05);
    }
    .product-card .p-name { font-weight: 700; font-size: .95rem; color: var(--text-main); margin-bottom: 4px; }
    .product-card .p-price { color: var(--primary); font-weight: 800; font-size: 1.1rem; }
    .product-card .p-stock { 
        font-size: .75rem; 
        color: var(--text-muted); 
        background: var(--bg-main); 
        padding: 2px 10px; 
        border-radius: 20px;
        display: inline-block;
        margin-top: 8px;
    }

    /* Cart Panel */
    .cart-panel {
        background: var(--card-bg);
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .cart-header { padding: 1.5rem; background: rgba(99, 102, 241, 0.05); border-bottom: 1px solid var(--border-color); }
    .cart-items { flex: 1; overflow-y: auto; padding: 1rem; }
    .cart-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 16px;
        margin-bottom: 0.75rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all .2s;
    }
    .cart-item:hover { background: rgba(99, 102, 241, 0.03); border-color: var(--primary); }
    
    .qty-controls { display: flex; align-items: center; gap: 0.75rem; background: var(--bg-main); border-radius: 10px; padding: 4px; }
    .qty-btn { 
        border: none; background: var(--card-bg); border-radius: 8px; 
        width: 32px; height: 32px; cursor: pointer; 
        font-weight: 800; color: var(--primary);
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
        transition: all .2s;
        color: var(--text-main);
    }
    .qty-btn:hover { background: var(--primary); color: #fff; }

    /* Summary */
    .cart-summary { padding: 1.5rem; background: rgba(99, 102, 241, 0.05); border-top: 1px solid var(--border-color); }
    .btn-checkout {
        width: 100%; padding: 1.25rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dk));
        color: #fff; border: none; border-radius: 16px;
        font-size: 1.1rem; font-weight: 800; cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        transition: all .3s;
    }
    .btn-checkout:hover { transform: scale(1.02); box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4); }

    /* Search bar */
    .search-box {
        position: relative;
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }
    .search-box input {
        width: 100%; border: none; padding: 1rem 1rem 1rem 3rem;
        border-radius: 16px; outline: none; font-weight: 500;
        background: transparent;
        color: var(--text-main);
    }
    .search-box i { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--primary); font-size: 1.2rem; }

    /* Recent Transactions Bar */
    .recent-bar {
        background: var(--card-bg); border-radius: 20px; padding: 1.25rem;
        border: 1px solid var(--border-color); margin-top: 1.5rem;
    }
    .recent-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);
    }
    .recent-item:last-child { border: none; }
</style>
@endpush

@section('content')
<div class="pos-container animate__animated animate__fadeIn">
    
    <div class="product-section">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari produk berdasarkan nama atau SKU...">
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
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" onerror="this.src='https://placehold.co/100x100?text=IMG'">
                <div class="p-name">{{ $product->name }}</div>
                <div class="p-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                <div class="p-stock">Stok: {{ $product->inventories->where('outlet_id', auth()->user()->outlet_id)->first()?->quantity ?? 0 }}</div>
            </div>
            @endforeach
        </div>

        {{-- Recent Transactions --}}
        <div class="recent-bar shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Transaksi Terakhir</h6>
            <div class="table-responsive">
                <table class="table table-borderless table-sm align-middle mb-0" style="font-size: .85rem;">
                    <thead>
                        <tr class="text-muted">
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $rt)
                        <tr class="recent-item">
                            <td class="fw-bold">{{ $rt->invoice_number }}</td>
                            <td>{{ $rt->cashier->name }}</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($rt->total, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('pos.receipt', $rt) }}" target="_blank" class="btn btn-sm btn-light rounded-pill">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Cart Section --}}
    <div class="cart-panel">
        <div class="cart-header d-flex justify-content-between align-items-center">
            <h5 class="fw-800 mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Detail Pesanan</h5>
            <button class="btn btn-sm btn-outline-danger border-0 rounded-pill" onclick="clearCart()">
                <i class="bi bi-trash3-fill"></i> Reset
            </button>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="text-center py-5 opacity-25" id="emptyCart">
                <i class="bi bi-cart-x" style="font-size: 5rem; display: block;"></i>
                <p class="mt-3 fw-bold">Keranjang masih kosong</p>
            </div>
        </div>

        <div class="cart-summary">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold" id="sumSubtotal">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Pajak (PPN 11%)</span>
                <span class="fw-bold" id="sumTax">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mb-4 mt-2">
                <span class="h5 fw-800 mb-0">TOTAL</span>
                <span class="h5 fw-800 text-primary mb-0" id="sumTotal">Rp 0</span>
            </div>

            <div class="mb-3">
                <label class="small fw-bold text-muted mb-2">Metode Pembayaran</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="cash" checked>
                        <label class="btn btn-outline-primary w-100 py-2 rounded-3" for="pay_cash">
                            <i class="bi bi-cash me-1"></i> Tunai
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payment_method" id="pay_qris" value="qris">
                        <label class="btn btn-outline-primary w-100 py-2 rounded-3" for="pay_qris">
                            <i class="bi bi-qr-code-scan me-1"></i> QRIS
                        </label>
                    </div>
                </div>
            </div>

            <div id="cashRow" class="bg-primary-subtle p-3 rounded-4 mb-4">
                <label class="small fw-bold text-primary mb-2">Nominal Uang Diterima</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 text-primary fw-bold">Rp</span>
                    <input type="number" id="cashAmount" class="form-control border-0 shadow-none" placeholder="0">
                </div>
                <div class="d-flex justify-content-between mt-3 px-1">
                    <span class="small fw-bold text-primary opacity-75">Kembalian:</span>
                    <span class="fw-800 text-primary" id="changeDisplay">Rp 0</span>
                </div>
            </div>

            <button class="btn-checkout" id="btnCheckout" onclick="processCheckout()" disabled>
                BAYAR SEKARANG
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
        div.className = 'cart-item animate__animated animate__fadeInRight animate__faster';
        
        // Elemen Nama & Info
        const infoDiv = document.createElement('div');
        infoDiv.style.flex = '1';
        infoDiv.innerHTML = `
            <div class="fw-bold" style="font-size: .85rem;">${item.name}</div>
            <div class="text-primary fw-bold" style="font-size: .8rem;">Rp ${fmt(item.price)}</div>
        `;

        // Controls
        const controls = document.createElement('div');
        controls.className = 'qty-controls';
        
        const btnMinus = document.createElement('button');
        btnMinus.className = 'qty-btn';
        btnMinus.textContent = '−';
        btnMinus.onclick = () => changeQty(item.id, -1);
        
        const qtySpan = document.createElement('span');
        qtySpan.className = 'qty-display';
        qtySpan.textContent = item.qty;
        
        const btnPlus = document.createElement('button');
        btnPlus.className = 'qty-btn';
        btnPlus.textContent = '+';
        btnPlus.onclick = () => changeQty(item.id, 1);

        controls.appendChild(btnMinus);
        controls.appendChild(qtySpan);
        controls.appendChild(btnPlus);
        
        // Subtotal
        const subtotalDiv = document.createElement('div');
        subtotalDiv.className = 'fw-800 text-end';
        subtotalDiv.style.minWidth = '80px';
        subtotalDiv.style.fontSize = '.9rem';
        subtotalDiv.textContent = `Rp ${fmt(item.price * item.qty)}`;
        
        div.appendChild(infoDiv);
        div.appendChild(controls);
        div.appendChild(subtotalDiv);
        
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

// Update payment method logic
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', e => {
        document.getElementById('cashRow').style.display = e.target.value === 'cash' ? 'block' : 'none';
    });
});

// ── Proses checkout ───────────────────────────────────────────────
async function processCheckout() {
    const items = Object.values(cart).filter(i => i.qty > 0);
    if (!items.length) return;

    const btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>MEMPROSES...';

    try {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const res = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                items: items.map(i => ({ product_id: i.id, quantity: i.qty })),
                payment_method: paymentMethod,
                cash_amount: parseFloat(document.getElementById('cashAmount').value) || null,
            }),
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire({
                title: 'Transaksi Berhasil!',
                html: `Invoice: <b>${data.invoice}</b><br>Kembalian: <b>Rp ${fmt(data.change)}</b>`,
                icon: 'success',
                confirmButtonText: 'Cetak Struk',
                confirmButtonColor: '#6366f1',
                showCancelButton: true,
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(`/pos/receipt/${data.data.id}`, '_blank');
                }
            });
            cart = {};
            renderCart();
            document.getElementById('cashAmount').value = '';
        } else {
            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
        }
    } catch (e) {
        Swal.fire('Error!', e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
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
