<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Kasir - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/pos.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div id="app"
     x-data="posApp()"
     class="h-screen bg-gray-100 overflow-hidden flex flex-col">

    {{-- ===== TOP BAR ===== --}}
    <div class="topbar">
        <div class="topbar-inner">
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-cash-register text-white text-xs"></i>
                </div>
                <span class="font-bold text-xs tracking-wide text-white hidden sm:inline">D'LARIS POS</span>
            </div>

            <span class="text-white/40 hidden sm:inline">|</span>

            <div class="flex items-center gap-1.5 text-xs min-w-0">
                <i class="fa-regular fa-user text-white/60"></i>
                <span class="text-white font-medium truncate max-w-[80px] sm:max-w-[140px]">{{ Auth::user()->name }}</span>
            </div>

            <div class="flex items-center gap-1.5 text-xs">
                <i class="fa-regular fa-clock text-white/60"></i>
                <span class="text-white font-mono text-xs font-semibold" x-text="jamSekarang"></span>
            </div>

            <div class="ml-auto flex items-center gap-1">
                <button @click="toggleAbsensi()" :disabled="absensiLoading"
                        :class="absensiStatus?.has_clocked_in ? 'bg-emerald-600 text-white' : 'bg-emerald-500 text-white'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg hover:opacity-80 transition-opacity"
                        :title="absensiStatus?.has_clocked_in ? 'Absen Pulang' : 'Absen Masuk'">
                    <i :class="absensiStatus?.has_clocked_in ? 'fa-solid fa-flag-checkered' : 'fa-regular fa-circle-check'" class="text-sm"></i>
                </button>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-600 text-white hover:opacity-80 transition-opacity" title="Logout">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== CONTENT AREA ===== --}}
    <div class="page-content">
        <div class="layout-container">
            {{-- LEFT: MENU --}}
            <div class="layout-menu">
                <div class="tab-panel" :class="{ 'active': tab === 'menu' }">
                    {{-- Category + Search --}}
                    <div class="bg-white border-b border-gray-200">
                        <div class="pt-3 px-3 md:px-4 pb-2 flex flex-col gap-2">
                            <div class="cat-scroll flex items-center gap-2">
                                <template x-for="(kat, idx) in kategoris" :key="kat.id">
                                    <button @click="loadMenu(kat.id)"
                                            :class="selectedKategori === kat.id ? 'active' : ''"
                                            class="cat-pill">
                                        <span x-text="kat.nama"></span>
                                        <template x-if="kat.menus_aktif && kat.menus_aktif.length !== undefined">
                                            <span class="count" x-text="kat.menus_aktif.length"></span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                            <div class="search-wrap">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text"
                                       x-model="searchQuery"
                                       @input="searchMenu()"
                                       placeholder="Cari menu..."
                                       autocomplete="off">
                                <template x-if="searchLoading">
                                    <i class="fa-solid fa-spinner spinner absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Menu Grid --}}
                    <template x-if="loading">
                        <div class="flex items-center justify-center py-20">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <i class="fa-solid fa-spinner spinner text-2xl"></i>
                                <span class="text-sm font-medium">Memuat menu...</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loading && menus.length === 0">
                        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                            <i class="fa-solid fa-utensils text-4xl mb-2 opacity-50"></i>
                            <span class="text-sm font-medium" x-text="searchQuery.trim() ? 'Menu tidak ditemukan' : 'Tidak ada menu tersedia'"></span>
                        </div>
                    </template>

                    <template x-if="!loading && menus.length > 0">
                        <div class="menu-grid">
                            <template x-for="(menu, idx) in menus" :key="menu.id">
                                <div class="menu-card fade-in-up" :style="`animation-delay: ${idx * 0.03}s`" @click="openDetail(menu)">
                                    <template x-if="menu.foto">
                                        <img :src="'{{ asset('storage') }}/' + menu.foto"
                                             :alt="menu.nama" class="card-img" loading="lazy">
                                    </template>
                                    <template x-if="!menu.foto">
                                        <div class="card-img-placeholder">
                                            <i class="fa-solid fa-utensils"></i>
                                        </div>
                                    </template>
                                    <div class="card-body">
                                        <div class="menu-name-price">
                                            <span class="font-semibold text-sm text-gray-800 leading-snug line-clamp-2" x-text="menu.nama"></span>
                                            <span class="price-tag flex-shrink-0">Rp <span x-text="formatNum(menu.harga)"></span></span>
                                        </div>
                                        <div class="card-footer-row">
                                            <template x-if="getMenuQty(menu.id) === 0">
                                                <button @click.stop="addToCart(menu.id)" class="btn-tambah text-xs">
                                                    <i class="fa-solid fa-plus mr-1"></i>Tambah
                                                </button>
                                            </template>
                                            <template x-if="getMenuQty(menu.id) > 0">
                                                <div class="qty-controls">
                                                    <button @click.stop="inlineDecrement(menu.id)" class="qty-btn qty-btn-minus">
                                                        <i class="fa-solid fa-minus fa-xs"></i>
                                                    </button>
                                                    <span @click.stop="editQty(menu.id)" class="qty-value text-sm" x-text="getMenuQty(menu.id)"></span>
                                                    <button @click.stop="inlineIncrement(menu.id)" class="qty-btn qty-btn-plus">
                                                        <i class="fa-solid fa-plus fa-xs"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- RIGHT: SIDEBAR --}}
            <div class="layout-sidebar" :class="{ active: tab !== 'menu' }">
                {{-- Sidebar pill tabs --}}
                <div class="sidebar-pills">
                    <button @click="sidebarTab = 'keranjang'" :class="{ active: sidebarTab === 'keranjang' }">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Keranjang</span>
                        <span x-show="cartJumlah > 0" class="sidebar-badge" x-text="cartJumlah"></span>
                    </button>
                    <button @click="sidebarTab = 'pesanan'" :class="{ active: sidebarTab === 'pesanan' }">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Pesanan</span>
                    </button>
                    <button @click="sidebarTab = 'karaoke'" :class="{ active: sidebarTab === 'karaoke' }">
                        <i class="fa-solid fa-microphone"></i>
                        <span>Karaoke</span>
                    </button>
                </div>

                {{-- Sidebar panels --}}
                <div class="sidebar-panels">
                    {{-- PANEL: KERANJANG --}}
                    <div class="sidebar-panel" x-show="sidebarTab === 'keranjang'">
                        <div class="cart-wrap">
                            <div class="cart-items-panel">
                                <div class="cart-header">
                                    <h2>
                                        <i class="fa-solid fa-cart-shopping text-amber-500"></i>
                                        Keranjang Belanja
                                        <span class="count-label" x-show="cartJumlah > 0" x-text="'(' + cartJumlah + ' item)'"></span>
                                    </h2>
                                    <button @click="clearCart()" x-show="cart.length > 0" class="clear-btn">
                                        <i class="fa-solid fa-trash-can"></i> Kosongkan
                                    </button>
                                </div>

                                <template x-if="cart.length === 0">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        <h3>Keranjang Kosong</h3>
                                        <p>Belum ada item di keranjang. Silakan pilih menu terlebih dahulu.</p>
                                    </div>
                                </template>

                                <template x-for="(item, index) in cart" :key="item.menu_id + '-' + (item.catatan || '')">
                                    <div class="cart-item fade-in-up" :style="'animation-delay: ' + (index * 0.03) + 's'">
                                        <div class="ci-row">
                                            <template x-if="item.foto">
                                                <img :src="'{{ asset('storage') }}/' + item.foto" :alt="item.nama" class="ci-icon" style="object-fit: cover;">
                                            </template>
                                            <template x-if="!item.foto">
                                                <div class="ci-icon"><i class="fa-solid fa-utensils"></i></div>
                                            </template>
                                            <div class="ci-body">
                                                <div class="ci-name" x-text="item.nama"></div>
                                                <div class="ci-price" x-text="'Rp ' + formatNum(item.harga)"></div>
                                                <div class="ci-actions">
                                                    <button class="qty-btn qty-btn-minus" @click="decrementQty(item)" :disabled="item.jumlah <= 1" style="width:28px;height:28px;font-size:0.625rem;">
                                                        <i class="fa-solid fa-minus fa-xs"></i>
                                                    </button>
                                                    <span class="qty-value" @click="editQty(item.menu_id)" x-text="item.jumlah" style="min-width:28px;font-size:0.8125rem;"></span>
                                                    <button class="qty-btn qty-btn-plus" @click="incrementQty(item)" style="width:28px;height:28px;font-size:0.625rem;">
                                                        <i class="fa-solid fa-plus fa-xs"></i>
                                                    </button>
                                                    <input type="text" class="ci-notes" placeholder="Catatan..." x-model="item.catatan_input"
                                                           @change.debounce="updateCart(item)">
                                                    <span class="ci-subtotal" x-text="'Rp ' + formatNum(item.subtotal)"></span>
                                                    <button class="ci-delete" @click="removeFromCart(item)">
                                                        <i class="fa-solid fa-trash-can fa-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="cart-summary-panel">
                                <div class="summary-box">
                                    <div class="sb-head">
                                        <i class="fa-solid fa-receipt text-amber-500"></i>
                                        Ringkasan Pesanan
                                    </div>
                                    <div class="sb-body">
                                        <div class="fgroup">
                                            <label>Meja / Tempat</label>
                                            <div class="flex gap-2">
                                                <select class="fcontrol" x-model="selectedMeja" @change="showMapModal = false">
                                                    <option value="">Pilih Meja</option>
                                                    <template x-for="meja in mejaList" :key="meja.id">
                                                        <option :value="meja.id" x-text="'Meja ' + meja.nomor_meja + (meja.area ? ' (' + meja.area + ')' : '')" :disabled="meja.status === 'penuh' || meja.status === 'full'"></option>
                                                    </template>
                                                </select>
                                                <button class="btn btn-outline btn-sm" @click="showMapModal = true">
                                                    <i class="fa-solid fa-map-location-dot"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="fgroup">
                                            <label>Kode Promo</label>
                                            <template x-if="!promoApplied">
                                                <div class="promo-row">
                                                    <input type="text" class="fcontrol" placeholder="Kode promo" x-model="promoCode" @keydown.enter="validasiPromo">
                                                    <button class="btn btn-amber btn-sm" @click="validasiPromo" :disabled="!promoCode.trim()"><i class="fa-solid fa-tag fa-xs"></i> Pakai</button>
                                                </div>
                                            </template>
                                            <template x-if="promoApplied">
                                                <div class="promo-badge">
                                                    <i class="fa-solid fa-check-circle"></i>
                                                    <span x-text="promoCode"></span>
                                                    <button @click="promoApplied = false; promoDiskon = 0; promoCode = ''"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="srow">
                                            <span class="sl">Subtotal</span>
                                            <span class="sv" x-text="'Rp ' + formatNum(cartTotal)"></span>
                                        </div>
                                        <template x-if="promoDiskon > 0">
                                            <div class="srow">
                                                <span class="sl">Diskon Promo</span>
                                                <span class="sv discount" x-text="'- Rp ' + formatNum(promoDiskon)"></span>
                                            </div>
                                        </template>
                                        <div class="srow total">
                                            <span class="sl">Total</span>
                                            <span class="sv grand" x-text="'Rp ' + formatNum(Math.max(0, cartTotal - promoDiskon))"></span>
                                        </div>

                                        <button class="btn btn-amber btn-block" @click="submitOrder" :disabled="orderLoading || cart.length === 0" style="padding:0.75rem;margin-top:0.5rem;">
                                            <template x-if="orderLoading">
                                                <span class="spinner" style="display:inline-block;width:1rem;height:1rem;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;"></span>
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
                    </div>

                    {{-- PANEL: PESANAN --}}
                    <div class="sidebar-panel" x-show="sidebarTab === 'pesanan'">
                        <div class="order-filter">
                            <button @click="statusFilter = 'all'" class="fl-btn" :class="{'active': statusFilter === 'all'}">Semua</button>
                            <button @click="statusFilter = 'menunggu'" class="fl-btn" :class="{'active': statusFilter === 'menunggu'}">
                                <span class="dot" style="background:#f59e0b;"></span>Menunggu
                            </button>
                            <button @click="statusFilter = 'diproses'" class="fl-btn" :class="{'active': statusFilter === 'diproses'}">
                                <span class="dot" style="background:#3b82f6;"></span>Diproses
                            </button>
                            <button @click="statusFilter = 'selesai'" class="fl-btn" :class="{'active': statusFilter === 'selesai'}">
                                <span class="dot" style="background:#10b981;"></span>Selesai
                            </button>
                        </div>

                        <template x-if="ordersLoading && filteredOrders.length === 0">
                            <div class="empty-state">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <p>Memuat pesanan...</p>
                            </div>
                        </template>

                        <template x-if="!ordersLoading && filteredOrders.length === 0">
                            <div class="empty-state">
                                <i class="fa-solid fa-clipboard-list"></i>
                                <p>Tidak ada pesanan</p>
                            </div>
                        </template>

                        <div class="order-grid">
                            <template x-for="(order, idx) in filteredOrders" :key="order.id">
                                <div class="order-card fade-in-up" :class="'status-' + order.status" :style="'animation-delay:' + (idx * 0.02) + 's'">
                                    <div class="oc-body">
                                        <div class="oc-head">
                                            <div>
                                                <div class="order-no" x-text="order.nomor_order"></div>
                                                <div class="order-meja" x-show="order.meja">
                                                    <i class="fa-solid fa-chair"></i>
                                                    <span x-text="'Meja ' + order.meja.nomor_meja"></span>
                                                </div>
                                            </div>
                                            <span class="badge-status" :class="'badge-' + order.status" x-text="order.status"></span>
                                        </div>
                                        <div class="order-time">
                                            <i class="fa-regular fa-clock"></i>
                                            <span x-text="timeAgo(order.created_at)"></span>
                                        </div>

                                        <div class="oc-info" x-show="!order.meja && order.tipe_pesanan">
                                            <div class="oc-info-item">
                                                <i class="fa-solid fa-bag-shopping"></i>
                                                <span x-text="order.tipe_pesanan === 'takeaway' ? 'Take Away' : order.tipe_pesanan === 'delivery' ? 'Delivery' : ''"></span>
                                            </div>
                                            <div class="oc-info-item" x-show="order.nama_pelanggan">
                                                <i class="fa-solid fa-user"></i>
                                                <span x-text="order.nama_pelanggan"></span>
                                                <template x-if="order.no_hp">
                                                    <span x-text="'(' + order.no_hp + ')'" style="color:#9ca3af;font-weight:400;"></span>
                                                </template>
                                            </div>
                                            <div class="oc-info-item" x-show="order.tipe_pesanan === 'delivery' && order.alamat_pengiriman">
                                                <i class="fa-solid fa-location-dot"></i>
                                                <span x-text="order.alamat_pengiriman" style="font-size:.7rem;color:#6b7280;"></span>
                                            </div>
                                            <div class="oc-info-item" x-show="order.ongkir > 0">
                                                <i class="fa-solid fa-truck"></i>
                                                <span x-text="'Ongkir: Rp ' + formatNum(order.ongkir)" style="font-size:.7rem;color:#f59e0b;"></span>
                                            </div>
                                        </div>

                                        <div class="oi-list">
                                            <template x-for="(item, i) in order.items" :key="item.id">
                                                <div class="oi-row" x-show="i < 9 || order._showAll">
                                                    <div class="oi-name">
                                                        <span class="oi-qty" x-text="item.jumlah + 'x'"></span>
                                                        <span x-text="item.menu?.nama || 'Menu#' + item.menu_id"></span>
                                                    </div>
                                                    <span class="oi-sub">Rp <span x-text="formatNum(item.jumlah * item.harga)"></span></span>
                                                </div>
                                            </template>
                                            <div x-show="order.items.length > 9 && !order._showAll" class="text-center pt-1">
                                                <button @click="order._showAll = true" class="text-blue-600 hover:text-blue-800 text-xs font-medium bg-transparent border-none cursor-pointer">
                                                    + <span x-text="order.items.length - 9"></span> item lainnya
                                                </button>
                                            </div>
                                        </div>

                                        <div class="order-total-row">
                                            <span class="otl">Total</span>
                                            <span class="ota">Rp <span x-text="formatNum(order.total)"></span></span>
                                        </div>

                                        <div class="order-actions">
                                            <template x-if="order.status === 'menunggu'">
                                                <button @click="updateStatus(order.id, 'diproses')" class="btn btn-primary btn-sm flex-1">
                                                    <i class="fa-solid fa-check"></i> Proses
                                                </button>
                                            </template>
                                            <template x-if="order.status === 'diproses'">
                                                <button @click="updateStatus(order.id, 'selesai')" class="btn btn-success btn-sm flex-1">
                                                    <i class="fa-solid fa-check"></i> Selesai
                                                </button>
                                            </template>
                                            <template x-if="order.status === 'selesai'">
                                                <button @click="openPaymentModal(order)" class="btn btn-warning btn-sm flex-1">
                                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- PANEL: KARAOKE --}}
                    <div class="sidebar-panel" x-show="sidebarTab === 'karaoke'">
                        <div class="krv2-wrap">
                            <div class="kr-book">
                                <div class="kr-card">
                                    <div class="kr-head" style="border-left: 4px solid #7c3aed;">
                                        <i class="fa-solid fa-pen-to-square" style="color:#7c3aed;"></i>
                                        <h3>Booking Karaoke</h3>
                                    </div>
                                    <div class="kr-body">
                                        <form @submit.prevent="bookingSubmit()">
                                            <div class="fgroup">
                                                <label>Ruangan</label>
                                                <select x-model="bookingForm.ruangan_id" class="fcontrol">
                                                    <option value="">-- Pilih Ruangan --</option>
                                                    <template x-for="r in ruangans" :key="r.id">
                                                        <option :value="r.id" x-text="r.nama + ' (Rp ' + formatNum(r.tarif_per_jam) + '/jam)'" :disabled="r.status === 'maintenance'"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="fgroup">
                                                <label>Nama Pemesan</label>
                                                <input type="text" x-model="bookingForm.nama_pemesan" class="fcontrol" placeholder="Nama customer">
                                            </div>
                                            <div class="fgroup">
                                                <label>No. HP</label>
                                                <input type="text" x-model="bookingForm.nomor_hp" class="fcontrol" placeholder="0812xxxxxxx">
                                            </div>
                                            <div class="fgroup">
                                                <label>Durasi (Jam)</label>
                                                <input type="number" x-model.number="bookingForm.durasi" min="1" max="6" class="fcontrol">
                                            </div>
                                            <div class="fgroup">
                                                <label>Catatan</label>
                                                <textarea x-model="bookingForm.catatan" class="fcontrol" placeholder="Catatan (opsional)"></textarea>
                                            </div>
                                            <div class="kr-price mb-3">
                                                <span class="kp-label">Total Harga</span>
                                                <span class="kp-amount">Rp <span x-text="formatNum(hargaPreview)"></span></span>
                                            </div>
                                            <button type="submit" class="btn btn-purple btn-block" :disabled="!formValid || bookingLoading">
                                                <i class="fa-solid fa-microphone" x-show="!bookingLoading"></i>
                                                <i class="fa-solid fa-spinner fa-spin" x-show="bookingLoading"></i>
                                                <span x-text="bookingLoading ? 'Memproses...' : 'Booking Sekarang'"></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="kr-sessions">
                                <div class="kr-card">
                                    <div class="kr-head" style="border-left: 4px solid #10b981;">
                                        <i class="fa-solid fa-list" style="color:#10b981;"></i>
                                        <h3>Sesi Aktif</h3>
                                        <button @click="fetchActiveBookings()" class="btn btn-outline btn-sm ml-auto">
                                            <i class="fa-solid fa-rotate" :class="{'fa-spin': bookingLoading}"></i>
                                        </button>
                                    </div>
                                    <div class="kr-body">
                                        <template x-if="activeBookings.length === 0">
                                            <div class="empty-state" style="padding:1.5rem;">
                                                <i class="fa-solid fa-microphone-slash" style="font-size:2rem;"></i>
                                                <p>Tidak ada sesi karaoke aktif</p>
                                            </div>
                                        </template>
                                        <template x-for="b in activeBookings" :key="b.id">
                                            <div class="kr-sess">
                                                <div class="ks-top">
                                                    <div>
                                                        <div class="ks-room">
                                                            <i class="fa-solid fa-door-open" style="color:#7c3aed;margin-right:0.25rem;"></i>
                                                            <span x-text="b.ruangan.nama"></span>
                                                        </div>
                                                        <div class="ks-cust">
                                                            <i class="fa-regular fa-user"></i>
                                                            <span x-text="b.nama_pemesan"></span>
                                                            <span x-text="'(' + b.nomor_hp + ')'" class="text-gray-400 ml-1"></span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="badge-status" :class="{'badge-confirmed': b.status === 'confirmed', 'badge-ongoing': b.status === 'ongoing', 'badge-expired': timerData[b.id]?.is_expired }"
                                                              x-text="timerData[b.id]?.is_expired ? 'Expired' : (b.status === 'ongoing' ? 'Ongoing' : 'Confirmed')"></span>
                                                    </div>
                                                </div>
                                                <div x-show="b.status === 'ongoing' || timerData[b.id]?.is_expired">
                                                    <div class="flex items-center justify-between">
                                                        <span class="kr-timer" x-text="timerData[b.id]?.display || '00:00:00'"></span>
                                                    </div>
                                                    <div class="kr-progress">
                                                        <div class="kr-progress-fill" :style="'width:' + (timerData[b.id]?.progress || 0) + '%'"></div>
                                                    </div>
                                                    <div class="flex justify-between text-xs text-gray-400">
                                                        <span x-text="b.jam_mulai"></span>
                                                        <span x-text="b.jam_selesai"></span>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <template x-if="b.status === 'confirmed'">
                                                        <button @click="startSession(b.id)" class="btn btn-success btn-sm">
                                                            <i class="fa-solid fa-play"></i> Mulai
                                                        </button>
                                                    </template>
                                                    <template x-if="b.status === 'ongoing' && !timerData[b.id]?.is_expired">
                                                        <button @click="openExtendModal(b)" class="btn btn-warning btn-sm">
                                                            <i class="fa-solid fa-clock"></i> Perpanjang
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== BOTTOM TAB NAVIGATION ===== --}}
    <div class="bottom-nav">
        <button class="tab-btn" :class="{ 'active': tab === 'menu' }" @click="tab = 'menu'">
            <i class="fa-solid fa-utensils"></i>
            <span>Menu</span>
        </button>
        <button class="tab-btn" :class="{ 'active': tab === 'keranjang' }" @click="tab = 'keranjang'; sidebarTab = 'keranjang'">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Keranjang</span>
            <template x-if="cartJumlah > 0">
                <span class="badge badge-amber" :class="{ 'badge-pulse': tab !== 'keranjang' }" x-text="cartJumlah"></span>
            </template>
        </button>
        <button class="tab-btn" :class="{ 'active': tab === 'pesanan' }" @click="tab = 'pesanan'; sidebarTab = 'pesanan'; fetchOrders()">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>Pesanan</span>
            <template x-if="notifTotal > 0">
                <span class="badge badge-red" :class="{ 'badge-pulse': tab !== 'pesanan' }" x-text="notifTotal"></span>
            </template>
        </button>
        <button class="tab-btn" :class="{ 'active': tab === 'karaoke' }" @click="tab = 'karaoke'; sidebarTab = 'karaoke'">
            <i class="fa-solid fa-microphone"></i>
            <span>Karaoke</span>
        </button>
    </div>

    {{-- ===== MAP MODAL ===== --}}
    <template x-teleport="body">
        <div x-show="showMapModal" x-cloak class="modal-overlay" @click.self="showMapModal = false">
            <div class="modal-box">
                <div class="md-head">
                    <h3><i class="fa-solid fa-map-location-dot text-amber-500 mr-2"></i>Pilih Meja</h3>
                    <button class="md-close" @click="showMapModal = false"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="md-body">
                    <div class="tab-pills">
                        <button class="tab-pill" :class="{ active: mapActiveTab === 'semua' }" @click="mapActiveTab = 'semua'">Semua</button>
                        <template x-for="area in mapAreaList" :key="area">
                            <button class="tab-pill" :class="{ active: mapActiveTab === area }" @click="mapActiveTab = area" x-text="area"></button>
                        </template>
                    </div>
                    <div class="meja-grid">
                        <template x-for="meja in filteredMeja" :key="meja.id">
                            <div class="m-item" :class="{ selected: selectedMeja == meja.id }" @click="selectedMeja = meja.id; showMapModal = false">
                                <i class="fa-solid fa-chair text-gray-400 text-lg"></i>
                                <span class="m-no" x-text="'Meja ' + meja.nomor_meja"></span>
                                <span class="m-st" :class="meja.status === 'tersedia' || meja.status === 'available' ? 'available' : meja.status === 'penuh' || meja.status === 'full' ? 'full' : 'used'" x-text="meja.status || '-'"></span>
                            </div>
                        </template>
                    </div>
                    <template x-if="filteredMeja.length === 0">
                        <p class="text-center text-gray-400 text-sm py-6">Tidak ada meja untuk area ini</p>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- ===== PAYMENT MODAL ===== --}}
    <template x-if="showPaymentModal">
        <div class="modal-overlay" @click.self="closePaymentModal()">
            <div class="modal-box" style="max-width:480px;">
                <div class="md-head">
                    <h3><i class="fa-solid fa-money-bill-wave text-amber-500 mr-2"></i>Pembayaran</h3>
                    <button @click="closePaymentModal()" class="md-close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="md-body">
                    <div class="pay-info">
                        <div class="pi-row">
                            <span class="pi-label">No. Order</span>
                            <span class="pi-value" x-text="paymentOrder?.nomor_order"></span>
                        </div>
                        <div class="pi-row" x-show="paymentOrder?.meja">
                            <span class="pi-label">Meja</span>
                            <span class="pi-value" x-text="'Meja ' + paymentOrder?.meja?.nomor_meja"></span>
                        </div>
                        <div class="pi-row" style="border-top:1px solid #e5e7eb;padding-top:0.375rem;margin-top:0.125rem;">
                            <span class="pi-label font-bold">Total</span>
                            <span class="pi-value font-bold" style="color:#d97706;font-size:1.0625rem;">Rp <span x-text="formatNum(paymentTotal)"></span></span>
                        </div>
                    </div>

                    <template x-if="!showSplitPayment">
                        <form @submit.prevent="submitPayment()">
                            <div class="fgroup">
                                <label>Metode Pembayaran</label>
                                <select x-model="paymentForm.metode_bayar" class="fcontrol" @change="onMetodeChange()">
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            <template x-if="paymentForm.metode_bayar === 'tunai'">
                                <div class="fgroup">
                                    <label>Nominal Bayar</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Rp</span>
                                        <input type="number" x-model.number="paymentForm.nominal_bayar" min="0" class="fcontrol" placeholder="0" @input.debounce.300ms="calculateKembalian()">
                                    </div>
                                    <div class="kembalian-text" :class="kembalianClass" x-show="paymentForm.kembalian !== null">
                                        <span x-text="kembalianLabel"></span>
                                    </div>
                                </div>
                            </template>
                            <div class="flex gap-3 mt-3">
                                <button type="button" @click="closePaymentModal()" class="btn btn-outline flex-1">Batal</button>
                                <button type="submit" class="btn btn-amber flex-1" :disabled="payLoading || !paymentValid">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="payLoading"></i>
                                    <i class="fa-solid fa-check" x-show="!payLoading"></i>
                                    <span x-text="payLoading ? 'Memproses...' : 'Bayar Sekarang'"></span>
                                </button>
                            </div>
                        </form>
                    </template>

                    <div class="split-toggle">
                        <label class="switch">
                            <input type="checkbox" x-model="showSplitPayment" @change="onSplitToggle()">
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label" style="font-size:0.8125rem;color:#6b7280;">Split Payment</span>
                    </div>

                    <template x-if="showSplitPayment">
                        <div>
                            <template x-for="(sp, idx) in splitPayments" :key="idx">
                                <div class="split-row">
                                    <div class="fgroup">
                                        <label>Metode</label>
                                        <select x-model="sp.metode_bayar" class="fcontrol">
                                            <option value="tunai">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="qris">QRIS</option>
                                            <option value="kartu">Kartu</option>
                                        </select>
                                    </div>
                                    <div class="fgroup">
                                        <label>Jumlah (Rp)</label>
                                        <input type="number" x-model.number="sp.jumlah" min="0" class="fcontrol" placeholder="0" @input="recalculateSplitTotal()">
                                    </div>
                                    <button type="button" @click="removeSplitPayment(idx)" class="remove-split" x-show="splitPayments.length > 2">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>
                            <div class="split-summary">
                                <span>Total dibayar:</span>
                                <span :class="splitTotal === paymentTotal ? 'match' : 'mismatch'">
                                    Rp <span x-text="formatNum(splitTotal)"></span> / Rp <span x-text="formatNum(paymentTotal)"></span>
                                </span>
                            </div>
                            <button type="button" @click="addSplitPayment()" class="btn btn-outline btn-sm btn-block mb-2" x-show="splitPayments.length < 4">
                                <i class="fa-solid fa-plus"></i> Tambah Metode
                            </button>
                            <div class="flex gap-3 mt-2">
                                <button type="button" @click="closePaymentModal()" class="btn btn-outline flex-1">Batal</button>
                                <button type="button" @click="submitSplitPayment()" class="btn btn-amber flex-1" :disabled="payLoading || !splitValid">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="payLoading"></i>
                                    <i class="fa-solid fa-check" x-show="!payLoading"></i>
                                    <span x-text="payLoading ? 'Memproses...' : 'Bayar Split'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- ===== EXTEND MODAL ===== --}}
    <template x-if="showExtendModal">
        <div class="modal-overlay" @click.self="showExtendModal = false">
            <div class="modal-box" style="max-width:400px;">
                <div class="md-body">
                    <h3 class="font-bold text-base mb-2"><i class="fa-solid fa-clock"></i> Perpanjang Sesi</h3>
                    <p class="text-sm text-gray-600 mb-3" x-text="'Tambahan waktu untuk booking #' + extendForm.booking_id"></p>
                    <div class="fgroup">
                        <label>Tambahan Jam</label>
                        <input type="number" x-model.number="extendForm.tambah_jam" min="1" max="6" class="fcontrol">
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button @click="showExtendModal = false" class="btn btn-outline flex-1">Batal</button>
                        <button @click="extendSession()" class="btn btn-purple flex-1" :disabled="bookingLoading">
                            <i class="fa-solid fa-check" x-show="!bookingLoading"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="bookingLoading"></i>
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ===== DETAIL MENU MODAL ===== --}}
    <div class="detail-modal-overlay" :class="{ 'open': showDetailModal }" @click.self="closeDetail()">
        <div class="detail-modal" x-show="detailMenu" x-cloak>
            <div class="detail-modal-img">
                <template x-if="detailMenu?.foto">
                    <img :src="'{{ asset('storage') }}/' + detailMenu.foto" :alt="detailMenu.nama">
                </template>
                <template x-if="!detailMenu?.foto">
                    <div class="no-img-lg"><i class="fa-solid fa-utensils"></i></div>
                </template>
                <button class="btn-close-detail" @click="closeDetail()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="detail-modal-body">
                <div class="detail-modal-tags">
                    <template x-if="detailMenu?.is_best_seller">
                        <span class="detail-modal-tag" style="background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--coffee);">Best Seller</span>
                    </template>
                    <template x-if="detailMenu?.is_new">
                        <span class="detail-modal-tag" style="background:linear-gradient(135deg,var(--coffee),var(--coffee-light));color:var(--gold-light);">Baru</span>
                    </template>
                </div>
                <div class="detail-modal-name" x-text="detailMenu?.nama"></div>
                <div class="detail-modal-price">Rp <span x-text="formatNum(detailMenu?.harga)"></span></div>
                <div class="detail-modal-desc" x-text="detailMenu?.deskripsi || 'Tidak ada deskripsi.'"></div>
                <div class="detail-qty-row">
                    <div class="detail-qty-ctrl">
                        <button @click="detailQty = Math.max(1, detailQty - 1)">−</button>
                        <span x-text="detailQty"></span>
                        <button @click="detailQty += 1">+</button>
                    </div>
                    <button class="btn-add-detail" @click="addFromDetail()">
                        <i class="fa-solid fa-plus"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
function posApp() {
    return {
        // ===== TAB STATE =====
        tab: 'menu',
        sidebarTab: 'keranjang',

        // ===== MENU STATE =====
        kategoris: @json($kategoris),
        selectedKategori: null,
        menus: [],
        searchQuery: '',
        loading: false,
        searchLoading: false,
        debounceTimer: null,

        // ===== CART STATE =====
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
        mejaList: @json($mejaList),

        get mapAreaList() {
            const areas = [...new Set(this.mejaList.map(m => m.area).filter(Boolean))];
            return areas.sort();
        },
        get filteredMeja() {
            if (this.mapActiveTab === 'semua') return this.mejaList;
            return this.mejaList.filter(m => m.area === this.mapActiveTab);
        },

        // ===== PESANAN STATE =====
        orders: [],
        statusFilter: 'all',
        ordersLoading: false,
        notifTotal: 0,
        prevNotifTotal: 0,
        showPaymentModal: false,
        paymentOrder: null,
        paymentForm: { metode_bayar: 'tunai', nominal_bayar: 0, kembalian: null },
        showSplitPayment: false,
        splitPayments: [],
        payLoading: false,

        // ===== KARAOKE STATE =====
        ruangans: @json($ruangans),
        bookingForm: { ruangan_id: '', nama_pemesan: '', nomor_hp: '', durasi: 2, catatan: '' },
        bookingLoading: false,
        activeBookings: [],
        timerData: {},
        showExtendModal: false,
        extendForm: { booking_id: null, tambah_jam: 1 },
        timerInterval: null,

        // ===== DETAIL MODAL =====
        showDetailModal: false,
        detailMenu: null,
        detailQty: 1,

        // ===== SHARED =====
        jamSekarang: '',
        clockInterval: null,
        pollingInterval: null,
        absensiStatus: null,
        absensiLoading: false,

        // ==================== INIT ====================
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && ['menu', 'keranjang', 'pesanan', 'karaoke'].includes(tabParam)) {
                this.tab = tabParam;
                if (tabParam !== 'menu') this.sidebarTab = tabParam;
            }
            this.updateJamSekarang();
            this.clockInterval = setInterval(() => { this.updateJamSekarang(); }, 1000);
            this.cekAbsensi();
            this.getCart();
            if (this.kategoris && this.kategoris.length > 0) {
                this.selectedKategori = this.kategoris[0].id;
                this.loadMenu(this.selectedKategori);
            } else { this.loading = false; }
            this.fetchOrders();
            this.fetchNotifTotal();
            this.fetchActiveBookings();
            this.pollingInterval = setInterval(() => {
                this.getCart();
                this.fetchNotifTotal();
                this.fetchOrders();
            }, 15000);
        },

        destroy() {
            if (this.clockInterval) clearInterval(this.clockInterval);
            if (this.pollingInterval) clearInterval(this.pollingInterval);
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            if (this.timerInterval) clearInterval(this.timerInterval);
        },

        formatNum(n) { return new Intl.NumberFormat('id-ID').format(n || 0); },

        updateJamSekarang() {
            this.jamSekarang = new Date().toLocaleTimeString('id-ID', { hour12: false });
        },

        // ==================== MENU ====================
        loadMenu(kategoriId) {
            this.loading = true;
            this.selectedKategori = kategoriId;
            this.searchQuery = '';
            this.menus = [];
            axios.get('{{ url("admin/pos/menu") }}/' + kategoriId)
                .then(res => { this.menus = res.data.data || []; })
                .catch(() => { this.menus = []; })
                .finally(() => { this.loading = false; });
        },

        searchMenu() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                const q = this.searchQuery.trim();
                if (q.length === 0) {
                    if (this.kategoris && this.kategoris.length > 0) {
                        const targetId = this.selectedKategori || this.kategoris[0].id;
                        this.selectedKategori = targetId;
                        this.loadMenu(targetId);
                    }
                    return;
                }
                this.searchLoading = true;
                this.selectedKategori = null;
                axios.get('{{ route("admin.pos.search-menu") }}', { params: { q } })
                    .then(res => { this.menus = res.data.data || []; })
                    .catch(() => { this.menus = []; })
                    .finally(() => { this.searchLoading = false; });
            }, 300);
        },

        getCart() {
            axios.get('{{ route("admin.pos.cart") }}')
                .then(res => {
                    const d = res.data.data;
                    if (d) {
                        this.cart = (d.items || []).map(item => ({ ...item, catatan_input: item.catatan || '' }));
                        this.cartJumlah = d.jumlah_item || 0;
                        this.cartTotal = d.total || 0;
                    }
                }).catch(() => {});
        },

        updateCartState(responseData) {
            if (responseData && responseData.data) {
                this.cart = (responseData.data.items || []).map(item => ({ ...item, catatan_input: item.catatan || '' }));
                this.cartJumlah = responseData.data.jumlah_item || 0;
                this.cartTotal = responseData.data.total || 0;
            }
        },

        getMenuQty(menuId) { const item = this.cart.find(i => i.menu_id === menuId); return item ? item.jumlah : 0; },

        addToCart(menuId) {
            axios.post('{{ route("admin.pos.cart.add") }}', { menu_id: menuId, jumlah: 1 })
                .then(res => { this.updateCartState(res.data); this.playNotifSound(); Swal.fire({ icon: 'success', title: 'Ditambahkan!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }); })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal menambahkan menu' }); });
        },

        inlineIncrement(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            if (item) { axios.post('{{ route("admin.pos.cart.update") }}', { menu_id: menuId, jumlah: item.jumlah + 1, catatan: item.catatan || '' }).then(res => { this.updateCartState(res.data); }).catch(() => {}); }
            else { this.addToCart(menuId); }
        },

        inlineDecrement(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            if (!item) return;
            if (item.jumlah <= 1) { axios.post('{{ route("admin.pos.cart.remove") }}', { menu_id: menuId, catatan: item.catatan || '' }).then(res => { this.updateCartState(res.data); }).catch(() => {}); }
            else { axios.post('{{ route("admin.pos.cart.update") }}', { menu_id: menuId, jumlah: item.jumlah - 1, catatan: item.catatan || '' }).then(res => { this.updateCartState(res.data); }).catch(() => {}); }
        },

        editQty(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            Swal.fire({
                title: 'Edit Jumlah', input: 'number', inputValue: item ? item.jumlah : 0,
                inputAttributes: { min: 0, step: 1, style: 'text-align:center;font-size:1.25rem;font-weight:700;' },
                showCancelButton: true, confirmButtonText: 'Simpan', cancelButtonText: 'Batal', confirmButtonColor: '#059669',
                preConfirm: (val) => { const p = parseInt(val); if (isNaN(p) || p < 0) { Swal.showValidationMessage('Jumlah minimal 0'); return; } return p; }
            }).then(result => {
                if (result.isConfirmed) {
                    const newQty = result.value;
                    if (newQty > 0) { axios.post('{{ route("admin.pos.cart.update") }}', { menu_id: menuId, jumlah: newQty, catatan: item ? item.catatan || '' : '' }).then(res => { this.updateCartState(res.data); }).catch(() => {}); }
                    else { axios.post('{{ route("admin.pos.cart.remove") }}', { menu_id: menuId, catatan: item ? item.catatan || '' : '' }).then(res => { this.updateCartState(res.data); }).catch(() => {}); }
                }
            });
        },

        // ==================== CART ====================
        updateCart(item) {
            axios.post('{{ route("admin.pos.cart.update") }}', { menu_id: item.menu_id, jumlah: item.jumlah, catatan: item.catatan_input || '' })
                .then(res => { this.updateCartState(res.data); })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal memperbarui keranjang' }); this.getCart(); });
        },

        decrementQty(item) { if (item.jumlah <= 1) return; item.jumlah -= 1; item.subtotal = item.jumlah * item.harga; this.updateCart(item); },
        incrementQty(item) { item.jumlah += 1; item.subtotal = item.jumlah * item.harga; this.updateCart(item); },

        removeFromCart(item) {
            Swal.fire({ title: 'Hapus Item?', text: item.nama + ' akan dihapus', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' })
                .then(result => {
                    if (!result.isConfirmed) return;
                    axios.post('{{ route("admin.pos.cart.remove") }}', { menu_id: item.menu_id, catatan: item.catatan || '' })
                        .then(res => { this.updateCartState(res.data); Swal.fire({ icon: 'success', title: 'Dihapus', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); })
                        .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal menghapus item' }); });
                });
        },

        clearCart() {
            if (this.cart.length === 0) return;
            Swal.fire({ title: 'Kosongkan Keranjang?', text: 'Semua item akan dihapus', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Kosongkan', confirmButtonColor: '#ef4444' })
                .then(result => {
                    if (!result.isConfirmed) return;
                    axios.post('{{ route("admin.pos.cart.clear") }}')
                        .then(res => { this.updateCartState(res.data); Swal.fire({ icon: 'success', title: 'Kosong', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); })
                        .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal mengosongkan keranjang' }); });
                });
        },

        submitOrder() {
            if (this.cart.length === 0) return;
            this.orderLoading = true;
            axios.post('{{ route("admin.pos.order") }}', { meja_id: this.selectedMeja || null })
                .then(res => { Swal.fire({ icon: 'success', title: 'Pesanan Dibuat!', text: 'No. ' + res.data.data.nomor_order, confirmButtonColor: '#d97706' }).then(() => { this.getCart(); this.fetchOrders(); this.tab = 'pesanan'; this.sidebarTab = 'pesanan'; }); })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal membuat pesanan' }); })
                .finally(() => { this.orderLoading = false; });
        },

        // ==================== DETAIL MODAL ====================
        openDetail(menu) { if (!menu) return; this.detailMenu = menu; this.detailQty = 1; this.showDetailModal = true; document.body.style.overflow = 'hidden'; },
        closeDetail() { this.showDetailModal = false; this.detailMenu = null; document.body.style.overflow = ''; },

        addFromDetail() {
            if (!this.detailMenu) return;
            axios.post('{{ route("admin.pos.cart.add") }}', { menu_id: this.detailMenu.id, jumlah: this.detailQty })
                .then(res => { this.updateCartState(res.data); this.playNotifSound(); this.closeDetail(); Swal.fire({ icon: 'success', title: 'Ditambahkan!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }); })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal menambahkan menu' }); });
        },

        validasiPromo() {
            const kode = this.promoCode.trim();
            if (!kode) return;
            axios.post('{{ url("admin/promo/validasi") }}', { kode, total: this.cartTotal })
                .then(res => { if (res.data.success) { this.promoApplied = true; this.promoDiskon = res.data.data.diskon || 0; Swal.fire({ icon: 'success', title: 'Promo Berhasil!', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); } })
                .catch(err => { this.promoApplied = false; this.promoDiskon = 0; Swal.fire({ icon: 'error', title: 'Promo Gagal', text: err.response?.data?.message || 'Kode promo tidak valid' }); });
        },

        // ==================== PESANAN ====================
        timeAgo(dateStr) {
            if (!dateStr) return '';
            const now = new Date(), date = new Date(dateStr), diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        get filteredOrders() { return this.statusFilter === 'all' ? this.orders : this.orders.filter(o => o.status === this.statusFilter); },

        fetchOrders() {
            this.ordersLoading = true;
            axios.get('{{ route("admin.pos.orders") }}')
                .then(res => { if (res.data.success) { const data = res.data.data || []; data.forEach(o => { o._showAll = false; }); this.orders = data; } })
                .catch(() => {}).finally(() => { this.ordersLoading = false; });
        },

        fetchNotifTotal() {
            axios.get('{{ route("admin.pos.notifications") }}')
                .then(res => {
                    const d = res.data.data;
                    if (d) {
                        const prev = this.prevNotifTotal;
                        this.notifTotal = d.total_new || 0;
                        if (this.notifTotal > prev && prev > 0 && d.new_orders) {
                            this.playNotifSound();
                            Swal.fire({ icon: 'info', title: 'Pesanan Baru!', toast: true, position: 'top-end', timer: 4000, showConfirmButton: false });
                        }
                        this.prevNotifTotal = this.notifTotal;
                    }
                }).catch(() => {});
        },

        updateStatus(id, status) {
            const labels = { diproses: 'Proses pesanan?', selesai: 'Tandai selesai?' };
            Swal.fire({ title: labels[status] || 'Ubah status?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal' })
                .then(r => {
                    if (!r.isConfirmed) return;
                    axios.patch('{{ url("admin/pos/order") }}/' + id + '/status', { status })
                        .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); this.fetchOrders(); } })
                        .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal mengubah status' }); });
                });
        },

        // ==================== PAYMENT ====================
        get paymentTotal() { const order = this.paymentOrder; return order ? ((order.grand_total && order.grand_total > 0) ? order.grand_total : (order.total || 0)) : 0; },

        openPaymentModal(order) {
            this.paymentOrder = order;
            const total = this.paymentTotal;
            this.paymentForm = { metode_bayar: 'tunai', nominal_bayar: total, kembalian: 0 };
            this.showSplitPayment = false;
            this.splitPayments = [{ metode_bayar: 'tunai', jumlah: Math.round(total / 2) }, { metode_bayar: 'transfer', jumlah: Math.ceil(total / 2) }];
            this.showPaymentModal = true;
            this.calculateKembalian();
        },

        closePaymentModal() { this.showPaymentModal = false; this.paymentOrder = null; this.payLoading = false; },

        onMetodeChange() {
            const total = this.paymentTotal;
            if (this.paymentForm.metode_bayar !== 'tunai') { this.paymentForm.nominal_bayar = total; this.paymentForm.kembalian = 0; }
            else { this.paymentForm.nominal_bayar = total; this.calculateKembalian(); }
        },

        calculateKembalian() {
            const total = this.paymentTotal, bayar = this.paymentForm.nominal_bayar || 0;
            if (this.paymentForm.metode_bayar !== 'tunai') { this.paymentForm.kembalian = 0; return; }
            if (bayar < total) { this.paymentForm.kembalian = -(total - bayar); return; }
            axios.post('{{ route("admin.pos.hitung-kembalian") }}', { total, nominal_bayar: bayar })
                .then(res => { if (res.data.success) this.paymentForm.kembalian = res.data.data.kembalian; })
                .catch(() => { this.paymentForm.kembalian = bayar - total; });
        },

        get kembalianClass() { const k = this.paymentForm.kembalian; return k === null ? '' : k >= 0 ? 'positive' : 'negative'; },
        get kembalianLabel() { const k = this.paymentForm.kembalian; return k === null ? '' : k >= 0 ? 'Kembalian: Rp ' + this.formatNum(k) : 'Kurang: Rp ' + this.formatNum(Math.abs(k)); },
        get paymentValid() { const total = this.paymentTotal; return this.paymentForm.metode_bayar === 'tunai' ? (this.paymentForm.nominal_bayar || 0) >= total : true; },

        submitPayment() {
            if (this.payLoading || !this.paymentValid) return;
            this.payLoading = true;
            axios.post('{{ url("admin/pos/order") }}/' + this.paymentOrder.id + '/bayar', { metode_bayar: this.paymentForm.metode_bayar, nominal_bayar: this.paymentForm.metode_bayar === 'tunai' ? (this.paymentForm.nominal_bayar || 0) : this.paymentTotal })
                .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil!', confirmButtonText: '<i class=\"fa-solid fa-print mr-1\"></i> Cetak Struk', showCancelButton: true, cancelButtonText: 'Selesai', confirmButtonColor: '#10b981' }).then(r => { if (r.isConfirmed) window.open('{{ url("admin/pos/order") }}/' + res.data.data.id + '/cetak', '_blank'); this.closePaymentModal(); this.fetchOrders(); }); } })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Terjadi kesalahan server' }); })
                .finally(() => { this.payLoading = false; });
        },

        onSplitToggle() { if (!this.showSplitPayment || this.splitPayments.length > 0) return; const total = this.paymentTotal; this.splitPayments = [{ metode_bayar: 'tunai', jumlah: Math.round(total / 2) }, { metode_bayar: 'transfer', jumlah: Math.ceil(total / 2) }]; },
        addSplitPayment() { if (this.splitPayments.length < 4) this.splitPayments.push({ metode_bayar: 'tunai', jumlah: 0 }); },
        removeSplitPayment(idx) { if (this.splitPayments.length > 2) { this.splitPayments.splice(idx, 1); this.recalculateSplitTotal(); } },
        recalculateSplitTotal() { this.splitPayments = [...this.splitPayments]; },
        get splitTotal() { return (this.splitPayments || []).reduce((s, sp) => s + (sp.jumlah || 0), 0); },
        get splitValid() { const total = this.paymentTotal, sum = this.splitTotal; return sum > 0 && Math.abs(sum - total) <= 100; },

        submitSplitPayment() {
            if (this.payLoading || !this.splitValid) return;
            this.payLoading = true;
            const payments = this.splitPayments.map(sp => ({ metode_bayar: sp.metode_bayar, jumlah: sp.jumlah || 0, nominal_bayar: sp.jumlah || 0 }));
            axios.post('{{ url("admin/pos/order") }}/' + this.paymentOrder.id + '/split-bayar', { payments })
                .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Pembayaran Split Berhasil!', confirmButtonText: '<i class=\"fa-solid fa-print mr-1\"></i> Cetak Struk', showCancelButton: true, cancelButtonText: 'Selesai', confirmButtonColor: '#10b981' }).then(r => { if (r.isConfirmed) window.open('{{ url("admin/pos/order") }}/' + res.data.data.id + '/cetak', '_blank'); this.closePaymentModal(); this.fetchOrders(); }); } })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Terjadi kesalahan server' }); })
                .finally(() => { this.payLoading = false; });
        },

        // ==================== KARAOKE ====================
        get hargaPreview() {
            if (!this.bookingForm.ruangan_id) return 0;
            const r = this.ruangans.find(r => r.id == this.bookingForm.ruangan_id);
            return r ? (r.tarif_per_jam || 0) * (this.bookingForm.durasi || 0) : 0;
        },

        get formValid() { const f = this.bookingForm; return f.ruangan_id && f.nama_pemesan.trim() && f.nomor_hp.trim() && f.durasi >= 1 && f.durasi <= 6; },

        bookingSubmit() {
            if (!this.formValid || this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ route("admin.pos.booking-karaoke") }}', this.bookingForm)
                .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Booking Berhasil', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); this.bookingForm = { ruangan_id: '', nama_pemesan: '', nomor_hp: '', durasi: 2, catatan: '' }; this.fetchActiveBookings(); } })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal booking' }); })
                .finally(() => { this.bookingLoading = false; });
        },

        fetchActiveBookings() {
            axios.get('{{ route("admin.pos.booking-aktif") }}')
                .then(res => { if (res.data.success) { this.activeBookings = res.data.data; this.startTimerPolling(); } })
                .catch(() => {});
        },

        startTimerPolling() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            const ongoing = this.activeBookings.filter(b => b.status === 'ongoing');
            if (ongoing.length === 0) { this.timerData = {}; return; }
            const poll = () => { ongoing.forEach(b => { this.fetchTimerStatus(b.id); }); };
            poll();
            this.timerInterval = setInterval(poll, 30000);
        },

        fetchTimerStatus(id) {
            axios.get('{{ url("admin/pos/booking") }}/' + id + '/timer')
                .then(res => {
                    if (res.data.success) {
                        const d = res.data.data;
                        this.timerData = { ...this.timerData, [id]: { progress: d.progress || 0, display: this.formatTimer(d.sisa_detik || 0), is_expired: d.is_expired } };
                    }
                }).catch(() => {});
        },

        formatTimer(detik) {
            if (detik <= 0) return '00:00:00';
            const h = Math.floor(detik / 3600), m = Math.floor((detik % 3600) / 60), s = detik % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        },

        startSession(id) {
            if (this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ url("admin/pos/booking") }}/' + id + '/start')
                .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Sesi Dimulai', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); this.fetchActiveBookings(); } })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal memulai sesi' }); })
                .finally(() => { this.bookingLoading = false; });
        },

        openExtendModal(booking) { this.extendForm = { booking_id: booking.id, tambah_jam: 1 }; this.showExtendModal = true; },

        extendSession() {
            if (this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ url("admin/pos/booking") }}/' + this.extendForm.booking_id + '/extend', { tambah_jam: this.extendForm.tambah_jam })
                .then(res => { if (res.data.success) { Swal.fire({ icon: 'success', title: 'Diperpanjang', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); this.showExtendModal = false; this.fetchActiveBookings(); } })
                .catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal memperpanjang' }); })
                .finally(() => { this.bookingLoading = false; });
        },

        // ==================== ABSENSI ====================
        cekAbsensi() { axios.get('{{ route("admin.absensi.cek-status") }}').then(res => { this.absensiStatus = res.data.data; }).catch(() => {}); },

        toggleAbsensi() {
            if (this.absensiLoading) return;
            this.absensiLoading = true;
            if (this.absensiStatus?.has_clocked_in && !this.absensiStatus?.has_clocked_out) {
                axios.post('{{ route("admin.absensi.clock-out") }}').then(() => { this.cekAbsensi(); Swal.fire({ icon: 'success', title: 'Clock Out Berhasil', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); }).catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal clock out' }); }).finally(() => { this.absensiLoading = false; });
            } else {
                axios.post('{{ route("admin.absensi.clock-in") }}').then(() => { this.cekAbsensi(); Swal.fire({ icon: 'success', title: 'Clock In Berhasil!', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' }); }).catch(err => { Swal.fire({ icon: 'error', title: 'Gagal', text: err.response?.data?.message || 'Gagal clock in' }); }).finally(() => { this.absensiLoading = false; });
            }
        },

        playNotifSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.frequency.value = 880; osc.type = 'sine';
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
                osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.15);
            } catch (e) {}
        },
    };
}
</script>
</body>
</html>
