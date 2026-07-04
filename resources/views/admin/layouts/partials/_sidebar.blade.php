    {{-- ═══ SIDEBAR ════════════════════════════════════════════ --}}
    <aside :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-16 -translate-x-full lg:translate-x-0'"
           class="text-white transition-all duration-300 flex-shrink-0 flex flex-col overflow-hidden fixed h-screen z-50">

        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 flex-shrink-0"
             style="border-bottom:1px solid rgba(255,255,255,0.06);">
            <a href="{{ route('admin.dashboard') }}" :class="sidebarOpen ? 'flex' : 'hidden'" class="items-center gap-2.5 no-underline">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,var(--gold),var(--gold-light));">
                    <i class="fa-solid fa-mug-hot" style="color:var(--coffee);font-size:0.8rem;"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-wide leading-tight m-0" style="color:var(--gold-light);">D'LARIS</h1>
                    <p class="leading-tight m-0" style="font-size:9px;color:rgba(255,255,255,0.35);">Cafe &amp; Karaoke</p>
                </div>
            </a>
            <button @click="sidebarOpen = !sidebarOpen"
                    class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200"
                    style="color:rgba(255,255,255,0.35);"
                    @mouseenter="$el.style.background='rgba(255,255,255,0.08)'; $el.style.color='#fff';"
                    @mouseleave="$el.style.background='transparent'; $el.style.color='rgba(255,255,255,0.35)';">
                <i :class="sidebarOpen ? 'fa-solid fa-angles-left' : 'fa-solid fa-angles-right'" class="text-xs"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-2 py-3">

            {{-- UTAMA --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Utama</p>
            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.dashboard*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Dashboard</span>
            </a>

            @if(Auth::user()->isAdmin())
            {{-- MANAJEMEN --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Manajemen</p>
            <a href="{{ route('admin.menu.index') }}" title="Menu"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.menu*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-utensils w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Menu</span>
            </a>
            <a href="{{ route('admin.kategori.index') }}" title="Kategori"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.kategori*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-tags w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Kategori</span>
            </a>
            <a href="{{ route('admin.meja.index') }}" title="Meja"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.meja*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-chair w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Meja</span>
            </a>
            <a href="{{ route('admin.ruangan.index') }}" title="Ruangan Karaoke"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.ruangan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-music w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Ruangan Karaoke</span>
            </a>

            {{-- PESANAN --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Pesanan &amp; Keuangan</p>
            <a href="{{ route('admin.booking.index') }}" title="Booking Karaoke"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.booking*') && !request()->routeIs('admin.booking.calendar') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-calendar-check w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Booking Karaoke</span>
            </a>
            <a href="{{ route('admin.booking.calendar') }}" title="Kalender"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.booking.calendar') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-calendar-days w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Kalender</span>
            </a>
            <a href="{{ route('admin.transaksi.index') }}" title="Transaksi"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.transaksi*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-receipt w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Transaksi</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" title="Laporan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.laporan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-chart-bar w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Laporan</span>
            </a>
            <a href="{{ route('admin.pengeluaran.index') }}" title="Pengeluaran"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.pengeluaran*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-money-bill-wave w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Pengeluaran</span>
            </a>

            {{-- PROMO & PELANGGAN --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Promo &amp; Pelanggan</p>
            <a href="{{ route('admin.promo.index') }}" title="Promo & Diskon"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.promo*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-percent w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Promo &amp; Diskon</span>
            </a>
            <a href="{{ route('admin.pelanggan.index') }}" title="Pelanggan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.pelanggan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-users w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Pelanggan</span>
            </a>

            {{-- INVENTORY --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Inventory &amp; SDM</p>
            <a href="{{ route('admin.bahan.index') }}" title="Bahan / Inventory"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.bahan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-boxes-stacked w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Bahan / Inventory</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}" title="Absensi"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.absensi*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-clock w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Absensi</span>
            </a>

            {{-- LAINNYA --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Lainnya</p>
            <a href="{{ route('pelanggan.menu') }}" target="_blank" title="Lihat Menu Pelanggan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5 nav-item-normal">
                <i class="fa-solid fa-eye w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'flex items-center gap-2' : 'hidden'" class="truncate font-medium">
                    Lihat Menu Pelanggan
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-40"></i>
                </span>
            </a>
            @endif

            @if(Auth::user()->isSuperAdmin())
            {{-- PENGATURAN --}}
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Pengaturan</p>
            <a href="{{ route('admin.karyawan.index') }}" title="Karyawan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.karyawan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-users-cog w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Karyawan</span>
            </a>
            <a href="{{ route('admin.pengaturan') }}" title="Pengaturan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.pengaturan') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-gear w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Pengaturan</span>
            </a>
            <a href="{{ route('admin.backup.index') }}" title="Backup Database"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.backup*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-database w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Backup Database</span>
            </a>
            <a href="{{ route('admin.activity-log.index') }}" title="Riwayat Aktivitas"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.activity-log*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-history w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Riwayat Aktivitas</span>
            </a>
            @endif

            @if(Auth::user()->isKasir())
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Kasir</p>
            <a href="{{ route('admin.pos') }}" title="POS / Kasir"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.pos*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-cash-register w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">POS / Kasir</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}" title="Absensi"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.absensi*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-clock w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Absensi</span>
            </a>
            @endif

            @if(Auth::user()->isKaryawan())
            <p :class="sidebarOpen ? 'block' : 'hidden'" class="sidebar-section-label">Karyawan</p>
            <a href="{{ route('admin.karyawan.dashboard') }}" title="Dashboard"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.karyawan.dashboard*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.karyawan.menu') }}" title="Menu"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.karyawan.menu*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-utensils w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Menu</span>
            </a>
            <a href="{{ route('admin.karyawan.booking') }}" title="Booking"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.karyawan.booking*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-calendar-check w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Booking</span>
            </a>
            <a href="{{ route('admin.karyawan.pesanan') }}" title="Pesanan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.karyawan.pesanan*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-receipt w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Pesanan</span>
            </a>
            <a href="{{ route('admin.transaksi.index') }}" title="Transaksi"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.transaksi*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-receipt w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Transaksi</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}" title="Absensi"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 mb-0.5
                      {{ request()->routeIs('admin.absensi*') ? 'nav-item-active' : 'nav-item-normal' }}">
                <i class="fa-solid fa-clock w-5 text-center text-sm flex-shrink-0"></i>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="truncate font-medium">Absensi</span>
            </a>
            @endif
        </nav>

        {{-- User Info Bottom --}}
        <div class="flex-shrink-0 p-2" style="border-top:1px solid rgba(255,255,255,0.06);">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl nav-item-normal transition-all duration-200">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden"
                     style="background:linear-gradient(135deg,var(--gold),var(--gold-light));">
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xs font-bold" style="color:var(--coffee);">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div :class="sidebarOpen ? 'block' : 'hidden'" class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate leading-tight m-0" style="color:var(--gold-light);">{{ Auth::user()->name }}</p>
                    <p class="leading-tight m-0 capitalize" style="font-size:10px;color:rgba(255,255,255,0.35);">{{ Auth::user()->role }}</p>
                </div>
            </a>
        </div>
    </aside>