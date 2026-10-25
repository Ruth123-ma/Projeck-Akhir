<aside class="w-full bg-[#081b2d] text-slate-300 flex flex-col justify-between p-3 shrink-0 md:w-72 md:h-screen md:sticky md:top-0 md:p-4">
    <div>
        <div class="flex items-center space-x-3 px-2 py-3 mb-4 md:py-4 md:mb-6">
            <div class="bg-white/10 border border-white/10 p-2 rounded-lg text-white shadow-sm">
                <i class="fa-solid fa-boxes-stacked text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-white tracking-wider text-sm leading-tight">STOCK GUDANG</h1>
                <span class="text-[10px] text-slate-400 tracking-widest uppercase">Manajemen Stok</span>
            </div>
        </div>

        <nav class="grid grid-cols-2 gap-2 text-sm font-medium md:grid-cols-1 md:gap-1">
            <a href="{{ url('/dashboard') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('dashboard*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-gauge-high w-4 md:w-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ url('/input-material') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('input-material*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-arrow-down-to-box w-4 md:w-5"></i>
                <span>Input</span>
            </a>

            <a href="{{ url('/output-material') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('output-material*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-arrow-up-from-box w-4 md:w-5"></i>
                <span>Output</span>
            </a>

            <a href="{{ url('/daftar-stok') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('daftar-stok*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-list w-4 md:w-5"></i>
                <span>Daftar Stock</span>
            </a>

            <a href="{{ url('/laporan-material') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('laporan-material*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-line w-4 md:w-5"></i>
                <span>Laporan Rekap</span>
            </a>

            <a href="{{ route('laporan-stok') }}"
               class="flex items-center space-x-2 px-3 py-3 rounded-lg transition md:space-x-3 md:px-4 {{ request()->is('laporan-stok*') ? 'bg-[#1E88E5] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-clipboard-list w-4 md:w-5"></i>
                <span>Laporan Stok</span>
            </a>
        </nav>
    </div>

    <div class="border-t border-white/10 pt-3 mt-3 md:pt-4 md:mt-0">
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-white/5 text-slate-200 text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <span>Live Monitoring</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-semibold text-slate-300 transition hover:bg-rose-500/15 hover:text-rose-300">
                <i class="fa-solid fa-right-from-bracket w-4 md:w-5"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>