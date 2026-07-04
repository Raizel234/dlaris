<!DOCTYPE html>
<html lang="id" x-cloak x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'); $watch('darkMode', val => { document.documentElement.classList.toggle('dark', val); localStorage.setItem('darkMode', val); })">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="@yield('title', 'Admin') — {{ config('app.name') }}">
    <meta property="og:description" content="{{ config('app.name') }} — Cafe & Karaoke Premium. Sistem manajemen restoran dan karaoke.">
    <meta property="og:image" content="{{ asset('images/dlaris.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
<div x-data="{ sidebarOpen: true, touchStartX: 0 }" x-init="sidebarOpen = window.innerWidth >= 1024; window.addEventListener('resize', () => { if (window.innerWidth < 1024) sidebarOpen = false; else sidebarOpen = true; })" class="min-h-screen flex"
     @touchstart="touchStartX = $event.touches[0].clientX"
     @touchend="if (!sidebarOpen && $event.changedTouches[0].clientX - touchStartX > 80) sidebarOpen = true; if (sidebarOpen && touchStartX - $event.changedTouches[0].clientX > 80) sidebarOpen = false;">

    @include('admin.layouts.partials._sidebar')

    {{-- Sidebar overlay (mobile) --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>

    {{-- ═══ MAIN CONTENT ════════════════════════════════════════ --}}
    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'"
         class="flex-1 flex flex-col min-h-screen overflow-hidden transition-all duration-300">

        {{-- TOP HEADER --}}
        <header class="flex-shrink-0 border-b border-gray-100 sticky top-0 z-30">
            <div class="px-5 h-16 flex items-center justify-between gap-4">

                {{-- Left: Hamburger + Breadcrumb --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all flex-shrink-0">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                    {{-- Breadcrumb --}}
                    <nav class="breadcrumb-nav hidden sm:flex items-center">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        @hasSection('title')
                        <span class="breadcrumb-sep text-gray-300">/</span>
                        <span>@yield('title')</span>
                        @endif
                    </nav>
                </div>

                {{-- Right: Search + DarkMode + Notif + Profile --}}
                <div class="flex items-center gap-2">

                    {{-- SEARCH --}}
                    <div class="relative search-wrap hidden md:block" x-data="{ open: false, q: '', res: [], loading: false }">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" x-model="q"
                               @focus="open=true"
                               @keydown.escape="open=false; q=''; res=[]"
                               @input.debounce.400ms="if(q.length>1){loading=true;fetch('{{ url('admin/menu') }}?search='+encodeURIComponent(q),{headers:{'Accept':'application/json'}}).then(r=>r.json()).then(d=>{res=d.data||[];open=true;}).finally(()=>loading=false);}else{res=[];}"
                               placeholder="Cari menu... (Ctrl+K)"
                               class="pl-9 pr-4 py-2 text-sm w-64 rounded-xl transition-all outline-none">
                        <div x-show="open && (res.length>0||loading)" @click.away="open=false;res=[]"
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 max-h-72 overflow-y-auto">
                            <template x-if="loading">
                                <div class="p-4 text-center text-gray-400 text-sm"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Mencari...</div>
                            </template>
                            <template x-for="m in res" :key="m.id">
                                <a :href="'{{ url('admin/menu') }}/'+m.id+'/edit'"
                                   class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b last:border-0">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        <img x-show="m.foto" :src="'{{ asset('storage') }}/'+m.foto" class="w-full h-full object-cover">
                                        <i x-show="!m.foto" class="fa-solid fa-utensils text-gray-300 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate m-0" x-text="m.nama"></p>
                                        <p class="text-xs text-gray-400 m-0" x-text="'Rp '+new Intl.NumberFormat('id-ID').format(m.harga)"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- DARK MODE TOGGLE --}}
                    <button @click="darkMode = !darkMode"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all"
                            :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                        <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-regular fa-moon'" class="text-sm"></i>
                    </button>

                    {{-- NOTIFICATIONS --}}
                    <div class="relative" id="notifWrapper">
                        <button id="notifBtn"
                                class="relative w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                            <i class="fa-regular fa-bell text-sm"></i>
                            <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <h6>Notifikasi</h6>
                                <span class="text-xs text-gray-400" id="notifTime">Baru saja</span>
                            </div>
                            <div id="notifList" style="max-height:320px;overflow-y:auto;">
                                <div class="notif-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PROFILE DROPDOWN --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl hover:bg-gray-100 transition-all">
                            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center"
                                 style="background:linear-gradient(135deg,var(--gold),var(--gold-light));">
                                @if(Auth::user()->foto)
                                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold" style="color:var(--coffee);">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold leading-tight m-0" style="color:var(--coffee);">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 capitalize leading-tight m-0">{{ Auth::user()->role }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-1 hidden md:block transition-transform duration-200" :class="open?'rotate-180':''"></i>
                        </button>

                        <div x-show="open" @click.away="open=false" x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <div class="px-4 py-3 border-b border-gray-50" style="background:linear-gradient(135deg,var(--cream),var(--cream-dark));">
                                <p class="text-sm font-bold text-gray-800 m-0">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize m-0">{{ Auth::user()->role }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fa-regular fa-user text-gray-400 w-4 text-center"></i> Profil Saya
                            </a>
                            <hr class="border-gray-50 m-0">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <i class="fa-solid fa-right-from-bracket text-red-400 w-4 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-5 lg:p-6">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flash-msg flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-msg flash-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="flash-msg flash-warning"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
// Skeleton loader helper for admin tables
function skeletonRows(colspan, count = 5) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `<tr class="skeleton-loader"><td colspan="${colspan}" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex: 0 0 60px;"></div></div></td></tr>`;
    }
    return html;
}
function showEmpty(tableBody, colspan, icon = 'fa-solid fa-database', title = 'Tidak ada data', msg = 'Belum ada data untuk ditampilkan') {
    tableBody.innerHTML = `<tr><td colspan="${colspan}"><div class="empty-state"><div class="empty-icon"><i class="${icon}"></i></div><h4>${title}</h4><p>${msg}</p></div></td></tr>`;
}
</script>
@stack('scripts')
</body>
</html>
