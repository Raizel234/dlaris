<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; }
        #app { min-height: 100vh; display: flex; flex-direction: column; }

        .order-item-enter { animation: slideIn 0.35s ease-out both; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-16px); } to { opacity: 1; transform: translateX(0); } }

        .cart-layout { display: flex; gap: 1.5rem; padding: 1.25rem; flex: 1; max-width: 1400px; margin: 0 auto; width: 100%; }
        .cart-items { flex: 3; min-width: 0; }
        .cart-summary { flex: 2; min-width: 280px; }

        .item-card { background: #fff; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #f3f4f6; transition: box-shadow 0.2s; }
        .item-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .item-card .item-info { display: flex; gap: 0.75rem; align-items: center; }
        .item-card .item-icon { width: 48px; height: 48px; border-radius: 0.5rem; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .item-card .item-details { flex: 1; min-width: 0; }
        .item-card .item-name { font-weight: 600; font-size: 0.9375rem; color: #1f2937; }
        .item-card .item-price { font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem; }
        .item-card .item-actions { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap; }

        .qty-btn { width: 32px; height: 32px; border-radius: 0.375rem; border: 1px solid #e5e7eb; background: #fff; color: #374151; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.875rem; transition: all 0.15s; }
        .qty-btn:hover { background: #f9fafb; border-color: #d1d5db; }
        .qty-btn:active { transform: scale(0.95); }
        .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .qty-value { min-width: 36px; text-align: center; font-weight: 700; font-size: 0.9375rem; color: #1f2937; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.375rem; transition: background 0.15s; user-select: none; }
        .qty-value:hover { background: #fef3c7; }

        .item-notes-input { flex: 1; min-width: 120px; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.375rem 0.625rem; font-size: 0.8125rem; outline: none; transition: border-color 0.15s; background: #fafafa; }
        .item-notes-input:focus { border-color: #d97706; background: #fff; box-shadow: 0 0 0 3px rgba(217,119,6,0.1); }

        .item-subtotal { font-weight: 700; font-size: 0.9375rem; color: #d97706; white-space: nowrap; min-width: 80px; text-align: right; }

        .btn-delete-item { width: 32px; height: 32px; border-radius: 0.375rem; border: none; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.875rem; transition: all 0.15s; }
        .btn-delete-item:hover { background: #fee2e2; }

        .summary-card { background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; position: sticky; top: 1.25rem; }
        .summary-card .summary-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; font-size: 1rem; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; }
        .summary-card .summary-body { padding: 1rem 1.25rem; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        .form-control { width: 100%; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.625rem 0.75rem; font-size: 0.875rem; outline: none; transition: border-color 0.15s; background: #fff; }
        .form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,0.1); }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; padding: 0.625rem 1.25rem; cursor: pointer; transition: all 0.15s; border: none; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-amber { background: #d97706; color: #fff; }
        .btn-amber:hover:not(:disabled) { background: #b45309; }
        .btn-outline { background: transparent; border: 1px solid #e5e7eb; color: #374151; }
        .btn-outline:hover { background: #f9fafb; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }

        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; font-size: 0.9375rem; }
        .summary-row.total { border-top: 2px solid #e5e7eb; margin-top: 0.5rem; padding-top: 0.75rem; font-weight: 700; font-size: 1.125rem; }
        .summary-row .label { color: #6b7280; }
        .summary-row .value { font-weight: 600; color: #1f2937; }
        .summary-row .value.discount { color: #10b981; }
        .summary-row .value.grand-total { color: #d97706; font-size: 1.25rem; }

        .promo-row { display: flex; gap: 0.5rem; }
        .promo-row .form-control { flex: 1; }
        .promo-badge { display: inline-flex; align-items: center; gap: 0.375rem; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 9999px; padding: 0.25rem 0.75rem; font-size: 0.8125rem; font-weight: 500; }
        .promo-badge button { background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.75rem; padding: 0; line-height: 1; }

        .btn-order { width: 100%; padding: 0.875rem; font-size: 1rem; border-radius: 0.75rem; margin-top: 0.75rem; }

        .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; }
        .empty-state i { font-size: 4rem; color: #d1d5db; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.125rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.875rem; color: #9ca3af; }

        /* Map Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(2px); }
        .modal-content { background: #fff; border-radius: 1rem; max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        .modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { font-weight: 700; font-size: 1.0625rem; }
        .modal-close { width: 36px; height: 36px; border-radius: 50%; border: none; background: #f3f4f6; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 1rem; }
        .modal-close:hover { background: #e5e7eb; }
        .modal-body { padding: 1.25rem; }

        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .tab-btn { padding: 0.5rem 1rem; border-radius: 9999px; border: 1px solid #e5e7eb; background: #fff; font-size: 0.8125rem; font-weight: 500; cursor: pointer; transition: all 0.15s; color: #374151; }
        .tab-btn:hover { border-color: #d1d5db; }
        .tab-btn.active { background: #d97706; color: #fff; border-color: #d97706; }

        .meja-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 0.75rem; }
        .meja-item { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 0.75rem 0.5rem; border-radius: 0.75rem; border: 2px solid #e5e7eb; background: #fff; cursor: pointer; transition: all 0.15s; }
        .meja-item:hover { border-color: #d97706; background: #fffbeb; }
        .meja-item.selected { border-color: #d97706; background: #fef3c7; box-shadow: 0 0 0 3px rgba(217,119,6,0.15); }
        .meja-item .meja-nomor { font-weight: 700; font-size: 0.9375rem; color: #1f2937; }
        .meja-item .meja-status { font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; }
        .meja-item .meja-status.available { background: #d1fae5; color: #059669; }
        .meja-item .meja-status.used { background: #fef3c7; color: #d97706; }
        .meja-item .meja-status.full { background: #fee2e2; color: #dc2626; }

        [x-cloak] { display: none !important; }
        .loading-spinner { display: inline-block; width: 1rem; height: 1rem; border: 2px solid #e5e7eb; border-top-color: #d97706; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 1023px) {
            .cart-layout { flex-direction: column; padding: 1rem; }
            .cart-summary { min-width: 0; }
            .summary-card { position: static; }
        }

        @media (max-width: 767px) {
            .item-card .item-actions { flex-direction: column; align-items: stretch; }
            .item-card .item-info { flex-wrap: wrap; }
            .item-subtotal { text-align: left; min-width: auto; }
            .item-notes-input { min-width: 0; }
        }

        .topbar { background: #fff; border-bottom: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.04); flex-shrink: 0; }
        .topbar-inner { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; max-width: 1400px; margin: 0 auto; }
        @media (min-width: 1024px) { .topbar-inner { padding: 0.625rem 1.5rem; } }

        .section-title { font-weight: 700; font-size: 1.0625rem; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    </style>
</head>
<body>
<div id="app"
     x-data="keranjangApp()"
     class="h-screen bg-gray-100 overflow-hidden flex flex-col"
     @keydown.escape.window="showMapModal = false">

    {{-- TOP BAR --}}
    <div class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('admin.pos') }}" class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-cash-register text-white text-xs"></i>
                </div>
                <span class="font-bold text-sm tracking-wide text-gray-800 hidden sm:inline">D'LARIS POS</span>
            </div>

            <span class="text-gray-300 hidden sm:inline">|</span>

            <div class="flex items-center gap-1.5 text-sm">
                <i class="fa-regular fa-user text-gray-400 text-xs"></i>
                <span class="text-gray-600 font-medium text-xs truncate max-w-[100px]">{{ Auth::user()->name }}</span>
            </div>

            <div class="flex items-center gap-1.5 text-sm ml-1">
                <i class="fa-regular fa-clock text-gray-400 text-xs"></i>
                <span class="text-gray-500 font-mono text-xs font-semibold" x-text="jamSekarang"></span>
            </div>

            <div class="ml-auto flex items-center gap-1.5">
                <button @click="toggleAbsensi()" :disabled="absensiLoading"
                        :class="absensiStatus?.has_clocked_in ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200'"
                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-colors disabled:opacity-50">
                    <i class="fa-regular fa-circle-check text-xs" :class="absensiStatus?.has_clocked_in ? 'text-emerald-500' : 'text-gray-400'"></i>
                    <template x-if="absensiLoading">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <template x-if="!absensiLoading && absensiStatus?.has_clocked_in">
                        <span x-text="'Clock In (' + absensiStatus.absensi?.jam_masuk + ')'" class="hidden sm:inline"></span>
                    </template>
                    <template x-if="!absensiLoading && absensiStatus?.has_clocked_out">
                        <span class="hidden sm:inline">| Clock Out</span>
                    </template>
                    <template x-if="!absensiLoading && !absensiStatus?.has_clocked_in">
                        <span class="hidden sm:inline">Clock In</span>
                    </template>
                </button>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="cart-layout overflow-y-auto">
        {{-- LEFT: Cart Items --}}
        <div class="cart-items">
            <div class="section-title">
                <i class="fa-solid fa-cart-shopping text-amber-500"></i>
                Keranjang Belanja
                <span class="text-sm font-normal text-gray-400" x-show="cartJumlah > 0" x-text="'(' + cartJumlah + ' item)'"></span>
                <button @click="clearCart()" x-show="cart.length > 0" class="ml-auto text-xs text-red-500 hover:text-red-700 flex items-center gap-1 bg-red-50 px-2.5 py-1 rounded-lg border border-red-200">
                    <i class="fa-solid fa-trash-can"></i> Kosongkan
                </button>
            </div>

            <template x-if="cart.length === 0">
                <div class="empty-state">
                    <i class="fa-solid fa-cart-plus"></i>
                    <h3>Keranjang Kosong</h3>
                    <p>Belum ada item di keranjang. Silakan pilih menu terlebih dahulu.</p>
                    <a href="{{ route('admin.pos.menu-page') }}" class="btn btn-amber mt-4">
                        <i class="fa-solid fa-utensils"></i> Pilih Menu
                    </a>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.menu_id + '-' + (item.catatan || '')">
                <div class="item-card order-item-enter" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <div class="item-info">
                        <template x-if="item.foto">
                            <img :src="item.foto" :alt="item.nama" class="item-icon" style="object-fit: cover;">
                        </template>
                        <template x-if="!item.foto">
                            <div class="item-icon">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                        </template>
                        <div class="item-details">
                            <div class="item-name" x-text="item.nama"></div>
                            <div class="item-price" x-text="'Rp ' + formatNum(item.harga)"></div>

                            <div class="item-actions">
                                <button class="qty-btn" @click="decrementQty(item)" :disabled="item.jumlah <= 1">
                                    <i class="fa-solid fa-minus fa-xs"></i>
                                </button>
                                <span class="qty-value" @click="editQty(item)" x-text="item.jumlah" title="Klik untuk ubah jumlah"></span>
                                <button class="qty-btn" @click="incrementQty(item)">
                                    <i class="fa-solid fa-plus fa-xs"></i>
                                </button>

                                <input type="text" class="item-notes-input" placeholder="Catatan..." x-model="item.catatan_input"
                                       @change.debounce="updateCart(item)"
                                       @keydown.enter="$event.target.blur()">

                                <span class="item-subtotal" x-text="'Rp ' + formatNum(item.subtotal)"></span>

                                <button class="btn-delete-item" @click="removeFromCart(item)" title="Hapus item">
                                    <i class="fa-solid fa-trash-can fa-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div class="cart-summary">
            <div class="summary-card">
                <div class="summary-header">
                    <i class="fa-solid fa-receipt text-amber-500"></i>
                    Ringkasan Pesanan
                </div>
                <div class="summary-body">
                    {{-- Table Selector --}}
                    <div class="form-group">
                        <label>Meja / Tempat</label>
                        <div class="flex gap-2">
                            <select class="form-control" x-model="selectedMeja" @change="showMapModal = false">
                                <option value="">Pilih Meja</option>
                                <template x-for="meja in mejaList" :key="meja.id">
                                    <option :value="meja.id" x-text="'Meja ' + meja.nomor_meja + (meja.area ? ' (' + meja.area + ')' : '')" :disabled="meja.status === 'penuh' || meja.status === 'full'"></option>
                                </template>
                            </select>
                            <button class="btn btn-outline btn-sm" @click="showMapModal = true" title="Pilih Meja via Peta">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Promo --}}
                    <div class="form-group">
                        <label>Kode Promo</label>
                        <template x-if="!promoApplied">
                            <div class="promo-row">
                                <input type="text" class="form-control" placeholder="Masukkan kode promo" x-model="promoCode" @keydown.enter="validasiPromo">
                                <button class="btn btn-amber btn-sm" @click="validasiPromo" :disabled="!promoCode.trim()">
                    <i class="fa-solid fa-tag fa-xs"></i>
                    Pakai
                </button>
                            </div>
                        </template>
                        <template x-if="promoApplied">
                            <div class="promo-badge">
                                <i class="fa-solid fa-check-circle"></i>
                                <span x-text="promoCode"></span>
                                <button @click="promoApplied = false; promoDiskon = 0; promoCode = ''" title="Hapus promo">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Totals --}}
                    <div class="summary-row">
                        <span class="label">Subtotal</span>
                        <span class="value" x-text="'Rp ' + formatNum(cartTotal)"></span>
                    </div>
                    <template x-if="promoDiskon > 0">
                        <div class="summary-row">
                            <span class="label">Diskon Promo</span>
                            <span class="value discount" x-text="'- Rp ' + formatNum(promoDiskon)"></span>
                        </div>
                    </template>
                    <div class="summary-row total">
                        <span class="label">Total</span>
                        <span class="value grand-total" x-text="'Rp ' + formatNum(Math.max(0, cartTotal - promoDiskon))"></span>
                    </div>

                    <button class="btn btn-amber btn-order" @click="submitOrder" :disabled="orderLoading || cart.length === 0">
                        <template x-if="orderLoading">
                            <span class="loading-spinner"></span>
                        </template>
                        <template x-if="!orderLoading">
                            <i class="fa-solid fa-check"></i>
                        </template>
                        <span x-text="orderLoading ? 'Memproses...' : 'Buat Pesanan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MAP MODAL --}}
    <template x-teleport="body">
        <div x-show="showMapModal" x-cloak class="modal-overlay" @click.self="showMapModal = false">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fa-solid fa-map-location-dot text-amber-500 mr-2"></i>Pilih Meja</h3>
                    <button class="modal-close" @click="showMapModal = false">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="tabs">
                        <button class="tab-btn" :class="{ active: mapActiveTab === 'semua' }" @click="mapActiveTab = 'semua'">Semua</button>
                        <template x-for="area in mapAreaList" :key="area">
                            <button class="tab-btn" :class="{ active: mapActiveTab === area }" @click="mapActiveTab = area" x-text="area"></button>
                        </template>
                    </div>

                    <div class="meja-grid">
                        <template x-for="meja in filteredMeja" :key="meja.id">
                            <div class="meja-item" :class="{ selected: selectedMeja == meja.id }" @click="selectedMeja = meja.id; showMapModal = false">
                                <i class="fa-solid fa-chair text-gray-400 text-lg"></i>
                                <span class="meja-nomor" x-text="'Meja ' + meja.nomor_meja"></span>
                                <span class="meja-status" :class="meja.status === 'tersedia' || meja.status === 'available' ? 'available' : meja.status === 'penuh' || meja.status === 'full' ? 'full' : 'used'" x-text="meja.status || '-'"></span>
                            </div>
                        </template>
                    </div>

                    <template x-if="filteredMeja.length === 0">
                        <p class="text-center text-gray-400 text-sm py-8">Tidak ada meja untuk area ini</p>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function keranjangApp() {
    return {
        cart: [],
        cartJumlah: 0,
        cartTotal: 0,
        selectedMeja: '',
        promoCode: '',
        promoApplied: false,
        promoDiskon: 0,
        orderLoading: false,
        showMapModal: false,
        mapActiveTab: 'semua',
        mapAreaFilter: '',
        mejaList: @json($mejaList),
        jamSekarang: '',
        absensiStatus: null,
        absensiLoading: false,
        clockInterval: null,

        get mapAreaList() {
            const areas = [...new Set(this.mejaList.map(m => m.area).filter(Boolean))];
            return areas.sort();
        },

        get filteredMeja() {
            if (this.mapActiveTab === 'semua') return this.mejaList;
            return this.mejaList.filter(m => m.area === this.mapActiveTab);
        },

        init() {
            this.updateJamSekarang();
            this.clockInterval = setInterval(() => { this.updateJamSekarang(); }, 1000);
            this.getCart();
            this.cekAbsensi();
        },
        destroy() {
            if (this.clockInterval) clearInterval(this.clockInterval);
        },

        formatNum(n) {
            return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        updateJamSekarang() {
            this.jamSekarang = new Date().toLocaleTimeString('id-ID', { hour12: false });
        },

        getCart() {
            axios.get('{{ route("admin.pos.cart") }}')
                .then(res => {
                    this.updateCartState(res.data.data);
                })
                .catch(() => {
                    this.cart = [];
                    this.cartJumlah = 0;
                    this.cartTotal = 0;
                });
        },

        updateCartState(data) {
            if (!data) return;
            this.cart = (data.items || []).map(item => ({
                ...item,
                catatan_input: item.catatan || '',
            }));
            this.cartJumlah = data.jumlah_item || 0;
            this.cartTotal = data.total || 0;
        },

        updateCart(item) {
            axios.post('{{ route("admin.pos.cart.update") }}', {
                menu_id: item.menu_id,
                jumlah: item.jumlah,
                catatan: item.catatan_input || '',
            })
                .then(res => {
                    this.updateCartState(res.data.data);
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Gagal memperbarui keranjang';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    this.getCart();
                });
        },

        decrementQty(item) {
            if (item.jumlah <= 1) return;
            item.jumlah -= 1;
            item.subtotal = item.jumlah * item.harga;
            this.updateCart(item);
        },

        incrementQty(item) {
            item.jumlah += 1;
            item.subtotal = item.jumlah * item.harga;
            this.updateCart(item);
        },

        editQty(item) {
            Swal.fire({
                title: 'Ubah Jumlah',
                input: 'number',
                inputValue: item.jumlah,
                inputAttributes: { min: 1, step: 1 },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d97706',
                background: '#fff',
                color: '#374151',
                inputValidator: (v) => {
                    if (!v || parseInt(v) < 1) return 'Jumlah minimal 1';
                    return null;
                },
            }).then(result => {
                if (result.isConfirmed) {
                    const qty = parseInt(result.value);
                    item.jumlah = qty;
                    item.subtotal = qty * item.harga;
                    this.updateCart(item);
                }
            });
        },

        removeFromCart(item) {
            Swal.fire({
                title: 'Hapus Item?',
                text: item.nama + ' akan dihapus dari keranjang',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                background: '#fff',
                color: '#374151',
            }).then(result => {
                if (result.isConfirmed) {
                    axios.post('{{ route("admin.pos.cart.remove") }}', {
                        menu_id: item.menu_id,
                        catatan: item.catatan || '',
                    })
                        .then(res => {
                            this.updateCartState(res.data.data);
                            Swal.fire({ icon: 'success', title: 'Dihapus', text: 'Item berhasil dihapus', timer: 1500, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                        })
                        .catch(err => {
                            const msg = err.response?.data?.message || 'Gagal menghapus item';
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                        });
                }
            });
        },

        clearCart() {
            if (this.cart.length === 0) return;
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: 'Semua item akan dihapus',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                background: '#fff',
                color: '#374151',
            }).then(result => {
                if (result.isConfirmed) {
                    axios.post('{{ route("admin.pos.cart.clear") }}')
                        .then(res => {
                            this.updateCartState(res.data.data);
                            Swal.fire({ icon: 'success', title: 'Kosong', text: 'Keranjang telah dikosongkan', timer: 1500, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                        })
                        .catch(err => {
                            const msg = err.response?.data?.message || 'Gagal mengosongkan keranjang';
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                        });
                }
            });
        },

        submitOrder() {
            if (this.cart.length === 0) return;
            this.orderLoading = true;

            axios.post('{{ route("admin.pos.order") }}', {
                meja_id: this.selectedMeja || null,
            })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pesanan Dibuat!',
                        text: 'Pesanan berhasil dibuat dengan nomor ' + res.data.data.nomor_order,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d97706',
                        background: '#fff',
                        color: '#374151',
                    }).then(() => {
                        window.location.href = '{{ route("admin.pos") }}';
                    });
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Gagal membuat pesanan';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                })
                .finally(() => {
                    this.orderLoading = false;
                });
        },

        validasiPromo() {
            const kode = this.promoCode.trim();
            if (!kode) return;

            axios.post('{{ url("admin/promo/validasi") }}', {
                kode: kode,
                total: this.cartTotal,
            })
                .then(res => {
                    if (res.data.success) {
                        this.promoApplied = true;
                        this.promoDiskon = res.data.data.diskon || 0;
                        Swal.fire({ icon: 'success', title: 'Promo Berhasil!', text: 'Diskon Rp ' + this.formatNum(this.promoDiskon), timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Kode promo tidak valid';
                    this.promoApplied = false;
                    this.promoDiskon = 0;
                    Swal.fire({ icon: 'error', title: 'Promo Gagal', text: msg, background: '#fff', color: '#374151' });
                });
        },

        cekAbsensi() {
            axios.get('{{ route("admin.absensi.cek-status") }}')
                .then(res => { this.absensiStatus = res.data.data; })
                .catch(() => {});
        },

        toggleAbsensi() {
            if (this.absensiLoading) return;
            this.absensiLoading = true;
            if (this.absensiStatus?.has_clocked_in && !this.absensiStatus?.has_clocked_out) {
                axios.post('{{ route("admin.absensi.clock-out") }}')
                    .then(() => {
                        this.cekAbsensi();
                        Swal.fire({ icon: 'success', title: 'Clock Out Berhasil', text: 'Jam pulang tercatat', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock out';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    })
                    .finally(() => { this.absensiLoading = false; });
            } else {
                axios.post('{{ route("admin.absensi.clock-in") }}')
                    .then(() => {
                        this.cekAbsensi();
                        Swal.fire({ icon: 'success', title: 'Clock In Berhasil!', text: 'Selamat bekerja!', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock in';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    })
                    .finally(() => { this.absensiLoading = false; });
            }
        },
    };
}
</script>
@stack('scripts')
</body>
</html>
