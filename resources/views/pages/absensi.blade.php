<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Gudang - Absensi Kehadiran</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f3f6f9] font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR NAVIGASI (KIRI) -->
    <aside class="w-64 bg-[#0a1427] text-slate-300 flex flex-col p-4 shrink-0">
        <!-- Logo Header -->
        <div class="flex items-center gap-3 mb-8 px-2 pt-2">
            <div class="bg-slate-800/80 p-2.5 rounded-xl text-blue-400">
                <i class="fa-solid fa-boxes-stacked text-xl"></i>
            </div>
            <div>
                <h1 class="font-bold text-white tracking-wide text-sm">STOCK GUDANG</h1>
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Manajemen Stok</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <nav class="space-y-1 text-sm font-medium">
            <!-- Navigasi Ke Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800/60 text-slate-300 transition">
                <i class="fa-solid fa-gauge w-5 text-center"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Menu Utama Absensi -->
            <div class="space-y-1 pt-1">
                <div class="flex items-center gap-3 px-3 py-2 text-slate-300">
                    <i class="fa-solid fa-id-card-clip w-5 text-center"></i>
                    <span>Absensi</span>
                </div>
                
                <!-- Submenu Absensi -->
                <div class="pl-3 space-y-1">
                    <!-- Navigasi Ke Input Absensi (Aktif) -->
                    <a href="{{ route('absensi-karyawan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-md">
                        <span>Input</span>
                    </a>
                    <!-- Navigasi Ke Output / Rekap Absensi -->
                    <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 transition">
                        <span>Output</span>
                    </a>
                </div>
            </div>

            <!-- Menu Stok -->
            <a href="{{ route('daftar-stok') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800/60 text-slate-300 pt-2 transition">
                <i class="fa-solid fa-list-check w-5 text-center"></i>
                <span>Stok</span>
            </a>

            <!-- Menu Laporan -->
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800/60 text-slate-300 transition">
                <i class="fa-solid fa-chart-line w-5 text-center"></i>
                <span>Laporan</span>
            </a>
        </nav>
    </aside>

    <!-- AREA TAMPILAN KANAN -->
    <main class="flex-1 p-8 flex flex-col justify-start">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 max-w-4xl w-full mx-auto">
            
            <!-- Title & Date Header -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Catat Kehadiran Karyawan</h2>
                <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ $today ?? now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>

            <!-- Jam Digital Box -->
            <div class="bg-slate-50/70 border border-slate-100/80 rounded-2xl p-6 text-center max-w-lg mx-auto mb-8">
                <p class="text-[10px] font-bold tracking-widest text-blue-600 uppercase mb-3">JAM MASUK (SAAT INI)</p>
                
                <div class="flex justify-center items-center gap-3 mb-4">
                    <div id="clock-hours" class="bg-blue-100/60 text-blue-900 text-3xl font-extrabold py-3 px-5 rounded-xl min-w-[65px]">
                        {{ now()->format('H') }}
                    </div>
                    <span class="text-2xl font-bold text-slate-400">:</span>
                    <div id="clock-minutes" class="bg-blue-100/60 text-blue-900 text-3xl font-extrabold py-3 px-5 rounded-xl min-w-[65px]">
                        {{ now()->format('i') }}
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold tracking-wider uppercase">
                    <i class="fa-solid fa-location-crosshairs text-[10px]"></i>
                    LOKASI TERDETEKSI
                </span>
            </div>

            <!-- Form Input Absensi -->
            <form action="{{ route('absensi.store') }}" method="POST" class="max-w-lg mx-auto space-y-5">
                @csrf
                
                <!-- ID NOPK / NIK -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">ID NOPK / NIK KARYAWAN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-solid fa-address-card text-sm"></i>
                        </span>
                        <input type="text" name="employee_no" required placeholder="Misal: P-001" 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- NAMA LENGKAP -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">NAMA LENGKAP KARYAWAN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" required placeholder="Masukkan nama lengkap" 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- JABATAN -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">JABATAN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-solid fa-briefcase text-sm"></i>
                        </span>
                        <select name="jabatan" required class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-slate-600 focus:outline-none focus:border-blue-500 focus:bg-white appearance-none transition">
                            <option value="">Pilih Jabatan</option>
                            <option value="Staff Gudang">Staff Gudang</option>
                            <option value="Operator">Operator</option>
                            <option value="Supervisor">Supervisor</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                <!-- BUTTON SUBMIT -->
                <button type="submit" class="w-full mt-2 bg-[#0a3b8c] hover:bg-blue-900 text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 transition duration-200 shadow-md">
                    <i class="fa-solid fa-play text-xs"></i>
                    <span>Kirim Absensi</span>
                </button>
            </form>

        </div>
    </main>

    <!-- Script Jam Real-Time -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            document.getElementById('clock-hours').textContent = hours;
            document.getElementById('clock-minutes').textContent = minutes;
        }
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
