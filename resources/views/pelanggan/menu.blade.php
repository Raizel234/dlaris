<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu — {{ config('app.name') }}</title>
    <meta name="description" content="Pesan makanan dan minuman favorit Anda langsung dari meja di D'LARIS Cafe & Karaoke.">
    @vite(['resources/css/bootstrap.css', 'resources/css/pelanggan.css', 'resources/js/bootstrap.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<nav class="app-navbar">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('beranda') }}" class="brand-name">D'<span>LARIS</span></a>
            @if($meja)
            <div class="meja-badge">
                <i class="fa-solid fa-chair" style="font-size:.65rem;"></i>
                Meja {{ $meja->nomor_meja }}
            </div>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            @auth
            <a href="{{ route('pelanggan.riwayat') }}" class="btn btn-sm rounded-pill" style="font-size:.75rem;border:1px solid rgba(74,44,42,0.1);color:var(--coffee-light);padding:.3rem .7rem;background:transparent;">
                <i class="fa-regular fa-clock"></i>
            </a>
            @endauth
            <button class="cart-btn" id="cartToggleBtn">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>Keranjang</span>
                <span class="cart-count" id="cartCountNav">0</span>
            </button>
        </div>
    </div>
</nav>

<div class="cat-bar">
    <div class="cat-scroll" id="catScroll">
        @foreach($kategoris as $k)
        <button class="cat-pill {{ $loop->first ? 'active' : '' }}" data-id="{{ $k->id }}" onclick="loadMenu({{ $k->id }}, this)">
            @if($k->ikon)<i class="{{ $k->ikon }}"></i>@endif
            {{ $k->nama }}
        </button>
        @endforeach
    </div>
    <div class="search-wrap">
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass" style="font-size:.82rem;"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Cari menu favorit..." oninput="handleSearch(this.value)">
        </div>
    </div>
</div>

<div class="menu-container">
    <div id="menuGrid" class="row g-3"></div>
    <div id="emptyState" class="empty-state" style="display:none;">
        <i class="fa-solid fa-bowl-food"></i>
        <p>Menu tidak ditemukan</p>
    </div>
</div>

<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
        <h5><i class="fa-solid fa-bag-shopping"></i> Keranjang Saya</h5>
        <button class="btn-close-drawer" onclick="closeCart()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="cart-items" id="cartItemsContainer">
        <div class="cart-empty">
            <i class="fa-regular fa-basket-shopping"></i>
            <p>Keranjang masih kosong<br><small>Tambahkan menu favoritmu!</small></p>
        </div>
    </div>
    <div class="cart-footer">
        <div class="cart-total-row">
            <div>
                <div class="cart-total-label">Total Belanja</div>
                <div id="cartTotalItems" style="font-size:.7rem;color:var(--coffee-light);opacity:.5;"></div>
            </div>
            <div class="cart-total-val" id="cartTotalVal">Rp 0</div>
        </div>
        @auth
        <button class="btn-order" id="btnOrder" onclick="submitOrder()" disabled>
            <i class="fa-solid fa-check-circle"></i>
            <span id="btnOrderText">Pesan Sekarang</span>
        </button>
        @else
        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn-add-outline" style="margin-top:0;padding:.7rem;">
            <i class="fa-solid fa-right-to-bracket"></i> Login untuk Memesan
        </a>
        @endauth
    </div>
</div>

<div class="cart-fab" id="cartFab">
    <button onclick="openCart()">
        <i class="fa-solid fa-bag-shopping"></i>
        <span id="fabText">0 item</span>
        <span class="fab-badge" id="fabBadge">0</span>
    </button>
</div>

<div class="detail-modal-overlay" id="detailModalOverlay" onclick="closeDetailModal(event)">
    <div class="detail-modal" id="detailModal">
        <div class="detail-modal-img" id="modalImg">
            <div class="no-img-lg"><i class="fa-solid fa-bowl-food"></i></div>
            <button class="btn-close-modal" onclick="closeDetailModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="detail-modal-body">
            <div class="detail-modal-tags" id="modalTags"></div>
            <div class="detail-modal-name" id="modalName">-</div>
            <div class="detail-modal-price" id="modalPrice">Rp 0</div>
            <div class="detail-modal-desc" id="modalDesc">-</div>
            <div class="detail-qty-row">
                <div class="detail-qty-ctrl">
                    <button onclick="modalQtyChange(-1)">−</button>
                    <span id="modalQty">1</span>
                    <button onclick="modalQtyChange(1)">+</button>
                </div>
                @auth
                <button class="btn-add-modal" id="btnAddModal" onclick="addFromModal()">
                    <i class="fa-solid fa-plus"></i> Tambah ke Keranjang
                </button>
                @else
                <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn-add-modal">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
const state = {
    kategoris: @json($kategoris),
    menus: [], cart: [], cartTotal: 0, cartCount: 0,
    selectedKategori: null, cartOpen: false, loading: false, loadingOrder: false,
    searchTimeout: null, modalMenu: null, modalQty: 1,
    isAuth: {{ Auth::check() ? 'true' : 'false' }},
    mejaId: {{ $meja?->id ?? 'null' }},
};

const rupiah = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);

document.addEventListener('DOMContentLoaded', () => {
    if (state.kategoris.length) {
        state.selectedKategori = state.kategoris[0].id;
        fetchMenu(state.selectedKategori);
    }
    if (state.isAuth) fetchCart();
});

function loadMenu(kategoriId, btn) {
    document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    state.selectedKategori = kategoriId;
    document.getElementById('searchInput').value = '';
    fetchMenu(kategoriId);
}
function fetchMenu(kategoriId) {
    showSkeletons();
    axios.get(`{{ url('menu-meja/kategori') }}/${kategoriId}`)
        .then(res => renderMenus(res.data.data || []))
        .catch(() => renderMenus([]));
}
function handleSearch(q) {
    clearTimeout(state.searchTimeout);
    state.searchTimeout = setTimeout(() => {
        if (q.trim().length < 2) {
            if (state.selectedKategori) fetchMenu(state.selectedKategori);
            return;
        }
        showSkeletons();
        document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
        axios.get('{{ route("pelanggan.menu.search") }}', { params: { q: q.trim() } })
            .then(res => renderMenus(res.data.data || []))
            .catch(() => renderMenus([]));
    }, 350);
}

function showSkeletons() {
    const g = document.getElementById('menuGrid');
    g.innerHTML = Array(8).fill('').map(() => `
        <div class="col-6 col-md-4 col-xl-3">
            <div class="skeleton-card">
                <div class="skeleton skeleton-img"></div>
                <div class="skeleton-body">
                    <div class="skeleton" style="height:13px;margin-bottom:7px;width:80%"></div>
                    <div class="skeleton" style="height:11px;margin-bottom:10px;width:55%"></div>
                    <div class="skeleton" style="height:32px;border-radius:9px"></div>
                </div>
            </div>
        </div>
    `).join('');
    document.getElementById('emptyState').style.display = 'none';
}

function getCartQty(menuId) {
    const item = state.cart.find(i => i.menu_id === menuId);
    return item ? item.jumlah : 0;
}

function renderMenus(menus) {
    state.menus = menus;
    const grid = document.getElementById('menuGrid');
    const empty = document.getElementById('emptyState');
    if (!menus.length) {
        grid.innerHTML = '';
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';
    grid.innerHTML = menus.map(m => {
        const qty = getCartQty(m.id);
        const hasImg = m.foto;
        const imgHtml = hasImg
            ? `<img src="{{ asset('storage') }}/${m.foto}" alt="${m.nama}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'no-img\\'><i class=\\'fa-solid fa-bowl-food\\'></i></div>'">`
            : `<div class="no-img"><i class="fa-solid fa-bowl-food"></i></div>`;

        const stockLabel = (m.stok !== null && m.stok !== undefined && m.stok > 0)
            ? `<div class="stock-badge"><i class="fa-solid fa-box"></i> Sisa ${m.stok}</div>`
            : '';

        const btnHtml = state.isAuth
            ? (qty > 0
                ? `<div class="qty-control" id="qtyCtrl-${m.id}">
                        <button class="qty-btn" onclick="updateQty(${m.id}, -1, event)">−</button>
                        <span class="qty-val" id="qtyVal-${m.id}" onclick="editQty(${m.id});event.stopPropagation();" style="cursor:pointer">${qty}</span>
                        <button class="qty-btn" onclick="updateQty(${m.id}, 1, event)">+</button>
                   </div>`
                : `<button class="btn-add" onclick="addToCart(${m.id}, event)">
                        <i class="fa-solid fa-plus"></i> Tambah
                   </button>`)
            : `<a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn-add-outline">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
               </a>`;

        return `
        <div class="col-6 col-md-4 col-xl-3">
            <div class="menu-card">
                <div class="menu-card-img">
                    ${imgHtml}
                    <div class="label-badges">
                        ${m.is_best_seller ? '<span class="badge-best">Best</span>' : ''}
                        ${m.is_new ? '<span class="badge-new">Baru</span>' : ''}
                    </div>
                    <div class="price-badge">${rupiah(m.harga)}</div>
                    <div class="card-overlay">
                        <button class="overlay-btn" onclick="openDetail(${m.id})">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </div>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-name">${m.nama}</div>
                    <div class="menu-card-desc">${m.deskripsi || '&nbsp;'}</div>
                    ${stockLabel}
                    <div id="btnWrap-${m.id}">${btnHtml}</div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function addToCart(menuId, e) {
    if (e) e.stopPropagation();
    if (!state.isAuth) { window.location = '{{ route("login") }}'; return; }
    const btn = document.querySelector(`#btnWrap-${menuId} .btn-add`);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }
    axios.post('{{ route("pelanggan.cart.add") }}', { menu_id: menuId, jumlah: 1 })
        .then(res => { updateCartState(res.data.data); showToast('Ditambahkan ke keranjang!'); })
        .catch(err => showToast(err.response?.data?.message || 'Gagal menambah', 'error'))
        .finally(() => renderMenus(state.menus));
}

function updateQty(menuId, delta, e) {
    if (e) e.stopPropagation();
    const item = state.cart.find(i => i.menu_id === menuId);
    if (!item) return;
    const newQty = item.jumlah + delta;
    if (newQty <= 0) { removeItem(menuId, item.catatan || ''); return; }
    axios.post('{{ route("pelanggan.cart.update") }}', { menu_id: menuId, jumlah: newQty, catatan: item.catatan || '' })
        .then(res => { updateCartState(res.data.data); renderMenus(state.menus); })
        .catch(() => {});
}
function editQty(menuId) {
    const item = state.cart.find(i => i.menu_id === menuId);
    if (!item) return;
    Swal.fire({
        title: 'Ubah jumlah',
        input: 'number',
        inputValue: item.jumlah,
        inputAttributes: { min: 1, step: 1 },
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#4a2c2a',
        preConfirm: (val) => {
            const n = parseInt(val);
            if (isNaN(n) || n < 1) { Swal.showValidationMessage('Minimal 1'); return; }
            return n;
        }
    }).then(r => {
        if (r.isConfirmed) {
            axios.post('{{ route("pelanggan.cart.update") }}', { menu_id: menuId, jumlah: r.value, catatan: item.catatan || '' })
                .then(res => { updateCartState(res.data.data); renderMenus(state.menus); })
                .catch(() => {});
        }
    });
}
function removeItem(menuId, catatan) {
    axios.post('{{ route("pelanggan.cart.remove") }}', { menu_id: menuId, catatan: catatan || '' })
        .then(res => { updateCartState(res.data.data); renderMenus(state.menus); })
        .catch(() => {});
}
function fetchCart() {
    axios.get('{{ route("pelanggan.cart") }}').then(res => updateCartState(res.data.data)).catch(() => {});
}
function updateCartState(data) {
    state.cart = data.items || [];
    state.cartTotal = data.total || 0;
    const prev = state.cartCount;
    state.cartCount = data.jumlah_item || 0;
    const el = document.getElementById('cartCountNav');
    el.textContent = state.cartCount;
    if (state.cartCount > prev) { el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); }
    const fab = document.getElementById('cartFab');
    if (state.cartCount > 0 && !state.cartOpen) {
        fab.style.display = 'block';
        document.getElementById('fabBadge').textContent = state.cartCount;
        document.getElementById('fabText').textContent = state.cartCount + ' item';
    } else { fab.style.display = 'none'; }
    document.getElementById('cartTotalVal').textContent = rupiah(state.cartTotal);
    document.getElementById('cartTotalItems').textContent = state.cartCount + ' item';
    const btn = document.getElementById('btnOrder');
    if (btn) btn.disabled = state.cartCount === 0;
    renderCartDrawer();
}
function renderCartDrawer() {
    const c = document.getElementById('cartItemsContainer');
    if (!state.cart.length) {
        c.innerHTML = `<div class="cart-empty"><i class="fa-regular fa-basket-shopping"></i><p>Keranjang masih kosong<br><small>Tambahkan menu favoritmu!</small></p></div>`;
        return;
    }
    c.innerHTML = state.cart.map(item => `
        <div class="cart-item-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="cart-item-name">${item.nama}</div>
                    <div class="cart-item-price">${rupiah(item.harga)} / pcs</div>
                </div>
                <div class="cart-item-subtotal">${rupiah(item.subtotal)}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="cart-item-note flex-grow-1">
                    <input type="text" placeholder="Catatan..." value="${item.catatan || ''}" onchange="updateNote(${item.menu_id}, this.value)">
                </div>
                <div class="qty-ctrl-sm">
                    <button onclick="cartQtyChange(${item.menu_id}, -1, '${item.catatan||''}')">−</button>
                    <span>${item.jumlah}</span>
                    <button onclick="cartQtyChange(${item.menu_id}, 1, '${item.catatan||''}')">+</button>
                </div>
                <button class="btn-del" onclick="removeItem(${item.menu_id}, '${item.catatan||''}')"><i class="fa-solid fa-trash-can"></i></button>
            </div>
        </div>
    `).join('');
}
function cartQtyChange(menuId, delta, catatan) {
    const item = state.cart.find(i => i.menu_id === menuId);
    if (!item) return;
    const n = item.jumlah + delta;
    if (n <= 0) { removeItem(menuId, catatan); return; }
    axios.post('{{ route("pelanggan.cart.update") }}', { menu_id: menuId, jumlah: n, catatan })
        .then(res => { updateCartState(res.data.data); renderMenus(state.menus); }).catch(() => {});
}
function updateNote(menuId, note) {
    const item = state.cart.find(i => i.menu_id === menuId);
    if (!item) return;
    axios.post('{{ route("pelanggan.cart.update") }}', { menu_id: menuId, jumlah: item.jumlah, catatan: note })
        .then(res => updateCartState(res.data.data)).catch(() => {});
}

function submitOrder() {
    if (state.loadingOrder) return;
    state.loadingOrder = true;
    const btn = document.getElementById('btnOrder');
    const txt = document.getElementById('btnOrderText');
    btn.disabled = true;
    txt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
    axios.post('{{ route("pelanggan.order") }}', { meja_id: state.mejaId })
        .then(res => {
            const nomor = res.data.data?.nomor_order || '';
            updateCartState({ items: [], total: 0, jumlah_item: 0 });
            closeCart();
            showToast('Pesanan #' + nomor + ' berhasil dibuat!');
        })
        .catch(err => showToast(err.response?.data?.message || 'Gagal membuat pesanan', 'error'))
        .finally(() => { state.loadingOrder = false; btn.disabled = false; txt.textContent = 'Pesan Sekarang'; });
}

function openCart() {
    state.cartOpen = true;
    document.getElementById('cartOverlay').classList.add('open');
    document.getElementById('cartDrawer').classList.add('open');
    document.getElementById('cartFab').style.display = 'none';
    document.body.style.overflow = 'hidden';
    renderCartDrawer();
}
function closeCart() {
    state.cartOpen = false;
    document.getElementById('cartOverlay').classList.remove('open');
    document.getElementById('cartDrawer').classList.remove('open');
    document.body.style.overflow = '';
    if (state.cartCount > 0) document.getElementById('cartFab').style.display = 'block';
}
document.getElementById('cartToggleBtn').addEventListener('click', () => { state.cartOpen ? closeCart() : openCart(); });

function openDetail(menuId) {
    const menu = state.menus.find(m => m.id === menuId);
    if (!menu) return;
    state.modalMenu = menu; state.modalQty = 1;
    const ov = document.getElementById('detailModalOverlay');
    document.getElementById('modalName').textContent = menu.nama;
    document.getElementById('modalPrice').textContent = rupiah(menu.harga);
    document.getElementById('modalDesc').textContent = menu.deskripsi || 'Tidak ada deskripsi.';
    document.getElementById('modalQty').textContent = 1;
    let tags = '';
    if (menu.is_best_seller) tags += '<span class="detail-modal-tag">Best Seller</span>';
    if (menu.is_new) tags += '<span class="detail-modal-tag">Baru</span>';
    if (menu.stok !== null && menu.stok !== undefined && menu.stok > 0) {
        tags += '<span class="detail-modal-tag" style="background:rgba(212,160,74,0.08);color:#b8860b;">Stok ' + menu.stok + '</span>';
    }
    document.getElementById('modalTags').innerHTML = tags;
    const imgEl = document.getElementById('modalImg');
    if (menu.foto) {
        imgEl.innerHTML = '<img src="{{ asset("storage") }}/' + menu.foto + '" alt="' + menu.nama + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.innerHTML=\'<div class=\\\'no-img-lg\\\'><i class=\\\'fa-solid fa-bowl-food\\\'></i></div><button class=\\\'btn-close-modal\\\' onclick=\\\'closeDetailModal()\\\'><i class=\\\'fa-solid fa-xmark\\\'></i></button>\';"><button class="btn-close-modal" onclick="closeDetailModal()"><i class="fa-solid fa-xmark"></i></button>';
    } else {
        imgEl.innerHTML = '<div class="no-img-lg"><i class="fa-solid fa-bowl-food"></i></div><button class="btn-close-modal" onclick="closeDetailModal()"><i class="fa-solid fa-xmark"></i></button>';
    }
    ov.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDetailModal(e) {
    if (e && e.target !== document.getElementById('detailModalOverlay')) return;
    document.getElementById('detailModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
function modalQtyChange(delta) {
    state.modalQty = Math.max(1, state.modalQty + delta);
    document.getElementById('modalQty').textContent = state.modalQty;
}
function addFromModal() {
    if (!state.modalMenu || !state.isAuth) return;
    const btn = document.getElementById('btnAddModal');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    axios.post('{{ route("pelanggan.cart.add") }}', { menu_id: state.modalMenu.id, jumlah: state.modalQty })
        .then(res => {
            updateCartState(res.data.data);
            renderMenus(state.menus);
            closeDetailModal({ target: document.getElementById('detailModalOverlay') });
            showToast(state.modalMenu.nama + ' x ' + state.modalQty + ' ditambahkan!');
        })
        .catch(err => showToast(err.response?.data?.message || 'Gagal menambah', 'error'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-plus"></i> Tambah ke Keranjang'; });
}

function showToast(msg, type) {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast-item';
    const icon = type === 'error' ? 'circle-exclamation"' : 'circle-check"';
    const color = type === 'error' ? '#f87171' : '#d4a04a';
    t.innerHTML = '<i class="fa-solid fa-' + icon + ' style="color:' + color + '"></i> ' + msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 2700);
}
</script>
</body>
</html>
