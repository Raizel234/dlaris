<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - POS Kasir {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; }
        #app { min-height: 100vh; display: flex; flex-direction: column; }

        .cat-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .cat-scroll::-webkit-scrollbar { display: none; }

        .menu-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, 1fr);
        }
        @media (min-width: 768px) {
            .menu-grid { grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        }
        @media (min-width: 1024px) {
            .menu-grid { grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
        }
        @media (min-width: 1280px) {
            .menu-grid { grid-template-columns: repeat(5, 1fr); }
        }

        .menu-card {
            background: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            border: 2px solid transparent;
            position: relative;
        }
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.04);
            border-color: #d1fae5;
        }
        .menu-card .card-img {
            width: 100%;
            height: 112px;
            object-fit: cover;
            display: block;
            background: #f9fafb;
        }
        .menu-card .card-img-placeholder {
            width: 100%;
            height: 112px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0fdf4;
            color: #86efac;
            font-size: 2.5rem;
        }
        @media (max-width: 767px) {
            .menu-card .card-img,
            .menu-card .card-img-placeholder { height: 80px; }
            .menu-card .card-img-placeholder { font-size: 1.75rem; }
        }
        .menu-card .card-body {
            padding: 0.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        @media (max-width: 767px) {
            .menu-card .card-body { padding: 0.5rem; }
        }

        .cat-pill {
            flex-shrink: 0;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 2px solid transparent;
            white-space: nowrap;
            user-select: none;
        }
        .cat-pill:hover { background: #d1fae5; }
        .cat-pill.active {
            background: #059669;
            color: #fff;
            border-color: #059669;
        }
        .cat-pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 700;
            margin-left: 0.375rem;
            padding: 0 0.375rem;
        }
        .cat-pill.active .cat-pill-count { background: rgba(255,255,255,0.25); color: #fff; }
        .cat-pill:not(.active) .cat-pill-count { background: #e5e7eb; color: #6b7280; }

        .qty-controls {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .qty-btn {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .qty-btn-minus { background: #fee2e2; color: #dc2626; }
        .qty-btn-minus:hover { background: #fecaca; }
        .qty-btn-plus { background: #d1fae5; color: #059669; }
        .qty-btn-plus:hover { background: #a7f3d0; }
        .qty-value {
            min-width: 1.5rem;
            text-align: center;
            font-weight: 700;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
            padding: 0.125rem 0.25rem;
            border-radius: 0.375rem;
            transition: background 0.15s ease;
        }
        .qty-value:hover { background: #f3f4f6; }

        .btn-tambah {
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            border: none;
            background: #059669;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-tambah:hover { background: #047857; }

        .price-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .search-wrap {
            position: relative;
            width: 100%;
        }
        .search-wrap input {
            width: 100%;
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            border-radius: 9999px;
            border: 2px solid #e5e7eb;
            background: #fff;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s ease;
        }
        .search-wrap input:focus { border-color: #059669; }
        .search-wrap .search-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.875rem;
            pointer-events: none;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.3s ease-out both; }

        .spinner { animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .badge-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.15); } }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        @media (min-width: 768px) {
            .content-area { padding: 1.25rem 1.5rem; }
        }
        @media (min-width: 1024px) {
            .content-area { padding: 1.5rem 2rem; }
        }
    </style>
</head>
<body>
<div id="app"
     x-data="posApp()"
     class="h-screen bg-gray-100 overflow-hidden flex flex-col select-none">

    {{-- TOP BAR --}}
    <div class="bg-white border-b border-gray-200 shadow-sm flex-shrink-0 px-4 lg:px-6 py-2.5 flex items-center gap-2 lg:gap-3">
        <a href="{{ route('admin.pos') }}"
           class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors flex-shrink-0"
           title="Kembali ke Dashboard">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>

        <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-utensils text-white text-sm"></i>
            </div>
            <span class="font-bold text-sm tracking-wide text-gray-800 hidden sm:inline">D'LARIS POS</span>
        </div>

        <span class="text-gray-300 hidden sm:inline">|</span>

        <div class="flex items-center gap-2 text-sm min-w-0">
            <i class="fa-regular fa-user text-gray-400 flex-shrink-0"></i>
            <span class="text-gray-600 font-medium truncate max-w-[100px] lg:max-w-[160px]">{{ Auth::user()->name }}</span>
        </div>

        <div class="flex items-center gap-2 text-sm ml-1 lg:ml-2">
            <i class="fa-regular fa-clock text-gray-400"></i>
            <span class="text-gray-500 font-mono text-xs font-semibold" x-text="jamSekarang"></span>
        </div>

        <div class="ml-auto flex items-center gap-1.5 lg:gap-2">
            <a href="{{ route('admin.pos.keranjang-page') }}"
               class="relative flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-cart-shopping text-sm"></i>
                <template x-if="cartJumlah > 0">
                    <span class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] rounded-full bg-amber-500 text-white flex items-center justify-center text-[0.625rem] font-bold px-1 leading-none badge-pulse" x-text="cartJumlah"></span>
                </template>
            </a>

            <button @click="toggleAbsensi()" :disabled="absensiLoading"
                    :class="absensiStatus?.has_clocked_in ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                    class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors disabled:opacity-50">
                <i class="fa-regular fa-circle-check text-xs" :class="absensiStatus?.has_clocked_in ? 'text-emerald-500' : 'text-gray-400'"></i>
                <template x-if="absensiLoading">
                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                </template>
                <template x-if="!absensiLoading && absensiStatus?.has_clocked_in">
                    <span x-text="'Clock In (' + absensiStatus.absensi?.jam_masuk + ')'"></span>
                </template>
                <template x-if="!absensiLoading && absensiStatus?.has_clocked_out">
                    <span>| Clock Out</span>
                </template>
                <template x-if="!absensiLoading && !absensiStatus?.has_clocked_in">
                    <span>Clock In</span>
                </template>
            </button>

            <button @click="toggleAbsensi()" :disabled="absensiLoading"
                    :class="absensiStatus?.has_clocked_in ? 'text-emerald-600' : 'text-gray-500'"
                    class="flex sm:hidden items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fa-regular fa-circle-check text-sm"></i>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>

    {{-- CATEGORY PILLS + SEARCH --}}
    <div class="bg-white border-b border-gray-200 flex-shrink-0">
        <div class="px-4 lg:px-6 py-3 flex flex-col gap-3">
            <div class="cat-scroll flex items-center gap-2">
                <template x-for="(kat, idx) in kategoris" :key="kat.id">
                    <button @click="loadMenu(kat.id)"
                            :class="selectedKategori === kat.id ? 'active' : ''"
                            class="cat-pill">
                        <span x-text="kat.nama"></span>
                        <template x-if="kat.menus_aktif && kat.menus_aktif.length !== undefined">
                            <span class="cat-pill-count" x-text="kat.menus_aktif.length"></span>
                        </template>
                    </button>
                </template>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="search-wrap flex-1 min-w-[200px]">
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
                <select x-model="sortBy" @change="sortMenus()"
                        class="border-2 border-gray-200 rounded-full px-3 py-2 text-xs font-medium text-gray-600 bg-white focus:border-emerald-500 outline-none cursor-pointer">
                    <option value="">Default</option>
                    <option value="termurah">Termurah</option>
                    <option value="termahal">Termahal</option>
                    <option value="a-z">A-Z</option>
                    <option value="z-a">Z-A</option>
                </select>
                <label class="flex items-center gap-1.5 text-xs font-medium text-gray-500 cursor-pointer select-none">
                    <input type="checkbox" x-model="filterTersedia" @change="applyFilters()"
                           class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    Tersedia
                </label>
            </div>
        </div>
    </div>

    {{-- MENU GRID --}}
    <div class="content-area">
        <template x-if="loading">
            <div class="flex items-center justify-center py-20">
                <div class="flex flex-col items-center gap-3 text-gray-400">
                    <i class="fa-solid fa-spinner spinner text-2xl"></i>
                    <span class="text-sm font-medium">Memuat menu...</span>
                </div>
            </div>
        </template>

        <template x-if="!loading && menus.length === 0">
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <i class="fa-solid fa-utensils text-4xl mb-3 opacity-50"></i>
                <span class="text-sm font-medium" x-text="searchQuery.trim() ? 'Menu tidak ditemukan' : 'Tidak ada menu tersedia'"></span>
            </div>
        </template>

        <template x-if="!loading && menus.length > 0">
            <div class="menu-grid">
                <template x-for="(menu, idx) in menus" :key="menu.id">
                    <div class="menu-card fade-in-up" :style="`animation-delay: ${idx * 0.04}s`">
                        <template x-if="menu.foto">
                            <img :src="'{{ asset('storage') }}/' + menu.foto"
                                 :alt="menu.nama"
                                 class="card-img"
                                 loading="lazy">
                        </template>
                        <template x-if="!menu.foto">
                            <div class="card-img-placeholder">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                        </template>

                        <div class="card-body">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-semibold text-sm text-gray-800 leading-snug line-clamp-2" x-text="menu.nama"></span>
                                <span class="price-badge flex-shrink-0">Rp <span x-text="formatNum(menu.harga)"></span></span>
                            </div>

                            <div class="mt-auto flex items-center justify-between pt-1">
                                <template x-if="getMenuQty(menu.id) === 0">
                                    <button @click="addToCart(menu.id)"
                                            class="btn-tambah text-xs">
                                        <i class="fa-solid fa-plus mr-1"></i>Tambah
                                    </button>
                                </template>
                                <template x-if="getMenuQty(menu.id) > 0">
                                    <div class="qty-controls">
                                        <button @click="inlineDecrement(menu.id)" class="qty-btn qty-btn-minus">
                                            <i class="fa-solid fa-minus fa-xs"></i>
                                        </button>
                                        <span @click="editQty(menu.id)" class="qty-value text-sm" x-text="getMenuQty(menu.id)"></span>
                                        <button @click="inlineIncrement(menu.id)" class="qty-btn qty-btn-plus">
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

<script>
function posApp() {
    return {
        kategoris: @json($kategoris),
        selectedKategori: null,
        menus: [],
        menusBackup: [],
        searchQuery: '',
        sortBy: '',
        filterTersedia: false,
        loading: false,
        searchLoading: false,
        cart: [],
        cartJumlah: 0,
        cartTotal: 0,
        jamSekarang: '',
        absensiStatus: null,
        absensiLoading: false,
        debounceTimer: null,
        clockInterval: null,
        pollingInterval: null,

        init() {
            this.updateJamSekarang();
            this.clockInterval = setInterval(() => { this.updateJamSekarang(); }, 1000);
            this.getCart();
            this.cekAbsensi();
            if (this.kategoris && this.kategoris.length > 0) {
                this.selectedKategori = this.kategoris[0].id;
                this.loadMenu(this.selectedKategori);
            } else {
                this.loading = false;
            }
            this.$watch('sortBy', () => this.sortMenus());
            this.$watch('filterTersedia', () => this.applyFilters());
            this.pollingInterval = setInterval(() => { this.getCart(); }, 15000);
        },

        destroy() {
            if (this.clockInterval) clearInterval(this.clockInterval);
            if (this.pollingInterval) clearInterval(this.pollingInterval);
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
        },

        formatNum(n) {
            return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        updateJamSekarang() {
            const now = new Date();
            this.jamSekarang = now.toLocaleTimeString('id-ID', { hour12: false });
        },

        loadMenu(kategoriId) {
            this.loading = true;
            this.selectedKategori = kategoriId;
            this.searchQuery = '';
            this.menus = [];
            axios.get('{{ url("admin/pos/menu") }}/' + kategoriId)
                .then(res => {
                    this.menusBackup = res.data.data || [];
                    this.menus = [...this.menusBackup];
                    this.applyFilters();
                    this.$nextTick(() => {
                        const el = document.querySelector('.content-area');
                        if (el) el.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                })
                .catch(() => {
                    this.menus = [];
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        sortMenus() {
            if (!this.sortBy) { this.applyFilters(); return; }
            const sorted = [...this.menus];
            switch (this.sortBy) {
                case 'termurah': sorted.sort((a,b) => a.harga - b.harga); break;
                case 'termahal': sorted.sort((a,b) => b.harga - a.harga); break;
                case 'a-z': sorted.sort((a,b) => a.nama.localeCompare(b.nama)); break;
                case 'z-a': sorted.sort((a,b) => b.nama.localeCompare(a.nama)); break;
            }
            this.menus = sorted;
        },

        applyFilters() {
            let result = this.menusBackup.length ? [...this.menusBackup] : [...this.menus];
            if (this.filterTersedia) {
                result = result.filter(m => m.is_tersedia === 1 || m.is_tersedia === true);
            }
            this.menus = result;
            if (this.sortBy) this.sortMenus();
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
                    .then(res => {
                        this.menusBackup = res.data.data || [];
                        this.menus = [...this.menusBackup];
                        this.applyFilters();
                    })
                    .catch(() => {
                        this.menus = [];
                    })
                    .finally(() => {
                        this.searchLoading = false;
                    });
            }, 300);
        },

        getCart() {
            axios.get('{{ route("admin.pos.cart") }}')
                .then(res => {
                    const d = res.data.data;
                    if (d) {
                        this.cart = d.items || [];
                        this.cartJumlah = d.jumlah_item || 0;
                        this.cartTotal = d.total || 0;
                    }
                })
                .catch(() => {});
        },

        updateCartState(responseData) {
            if (responseData && responseData.data) {
                this.cart = responseData.data.items || [];
                this.cartJumlah = responseData.data.jumlah_item || 0;
                this.cartTotal = responseData.data.total || 0;
            }
        },

        getMenuQty(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            return item ? item.jumlah : 0;
        },

        addToCart(menuId) {
            axios.post('{{ route("admin.pos.cart.add") }}', {
                menu_id: menuId,
                jumlah: 1
            })
            .then(res => {
                this.updateCartState(res.data);
                this.playNotifSound();
                Swal.fire({
                    icon: 'success',
                    title: 'Ditambahkan!',
                    text: 'Menu berhasil ditambahkan ke keranjang',
                    timer: 1200,
                    showConfirmButton: false,
                    background: '#fff',
                    color: '#374151',
                    toast: true,
                    position: 'top-end',
                });
            })
            .catch(err => {
                const msg = err.response?.data?.message || 'Gagal menambahkan menu';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: msg,
                    background: '#fff',
                    color: '#374151',
                });
            });
        },

        inlineIncrement(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            if (item) {
                axios.post('{{ route("admin.pos.cart.update") }}', {
                    menu_id: menuId,
                    jumlah: item.jumlah + 1,
                    catatan: item.catatan || ''
                })
                .then(res => { this.updateCartState(res.data); })
                .catch(() => {});
            } else {
                this.addToCart(menuId);
            }
        },

        inlineDecrement(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            if (!item) return;
            if (item.jumlah <= 1) {
                axios.post('{{ route("admin.pos.cart.remove") }}', {
                    menu_id: menuId,
                    catatan: item.catatan || ''
                })
                .then(res => { this.updateCartState(res.data); })
                .catch(() => {});
            } else {
                axios.post('{{ route("admin.pos.cart.update") }}', {
                    menu_id: menuId,
                    jumlah: item.jumlah - 1,
                    catatan: item.catatan || ''
                })
                .then(res => { this.updateCartState(res.data); })
                .catch(() => {});
            }
        },

        editQty(menuId) {
            const item = this.cart.find(i => i.menu_id === menuId);
            const currentQty = item ? item.jumlah : 0;
            Swal.fire({
                title: 'Edit Jumlah',
                input: 'number',
                inputValue: currentQty,
                inputAttributes: {
                    min: 0,
                    step: 1,
                    style: 'text-align: center; font-size: 1.25rem; font-weight: 700;',
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                background: '#fff',
                color: '#374151',
                preConfirm: (val) => {
                    const parsed = parseInt(val);
                    if (isNaN(parsed) || parsed < 0) {
                        Swal.showValidationMessage('Jumlah harus angka minimal 0');
                        return;
                    }
                    return parsed;
                },
            }).then(result => {
                if (result.isConfirmed) {
                    const newQty = result.value;
                    if (newQty > 0) {
                        axios.post('{{ route("admin.pos.cart.update") }}', {
                            menu_id: menuId,
                            jumlah: newQty,
                            catatan: item ? item.catatan || '' : ''
                        })
                        .then(res => { this.updateCartState(res.data); })
                        .catch(() => {});
                    } else {
                        axios.post('{{ route("admin.pos.cart.remove") }}', {
                            menu_id: menuId,
                            catatan: item ? item.catatan || '' : ''
                        })
                        .then(res => { this.updateCartState(res.data); })
                        .catch(() => {});
                    }
                }
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
                    .then(res => {
                        this.cekAbsensi();
                        Swal.fire({
                            icon: 'success',
                            title: 'Clock Out Berhasil',
                            text: 'Jam pulang tercatat',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#fff',
                            color: '#374151',
                            toast: true,
                            position: 'top-end',
                        });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock out';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg,
                            background: '#fff',
                            color: '#374151',
                        });
                    })
                    .finally(() => { this.absensiLoading = false; });
            } else {
                axios.post('{{ route("admin.absensi.clock-in") }}')
                    .then(res => {
                        this.cekAbsensi();
                        Swal.fire({
                            icon: 'success',
                            title: 'Clock In Berhasil!',
                            text: 'Selamat bekerja!',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#fff',
                            color: '#374151',
                            toast: true,
                            position: 'top-end',
                        });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock in';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg,
                            background: '#fff',
                            color: '#374151',
                        });
                    })
                    .finally(() => { this.absensiLoading = false; });
            }
        },

        playNotifSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.15);
            } catch (e) {}
        },
    };
}
</script>
</body>
</html>
