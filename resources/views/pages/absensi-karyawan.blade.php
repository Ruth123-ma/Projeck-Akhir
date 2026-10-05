<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans text-slate-800">
<div class="flex min-h-screen flex-col md:flex-row">
    @include('components.sidebar')

    <main class="min-w-0 flex-1">
        <header class="border-b border-slate-200 bg-white px-4 py-5 md:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-blue-600"><i class="fa-solid fa-fingerprint"></i> Operasional Gudang</p>
                    <h1 class="mt-1 text-2xl font-black text-slate-900">Absensi Karyawan</h1>
                    <p class="mt-1 text-sm text-slate-500">Kelola kehadiran masuk dan pulang seluruh personel gudang.</p>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white"><i class="fa-regular fa-clock"></i></span>
                    <div><p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Waktu saat ini</p><p id="live-clock" class="font-mono text-lg font-black text-blue-950">{{ now()->format('H:i:s') }}</p></div>
                </div>
            </div>
        </header>

        <div class="space-y-6 p-4 md:p-8">
            @foreach(['success' => 'emerald', 'error' => 'rose', 'info' => 'blue'] as $messageType => $color)
                @if(session($messageType))
                    <div class="flex items-center gap-3 rounded-xl border border-{{ $color }}-200 bg-{{ $color }}-50 px-4 py-3 text-sm font-medium text-{{ $color }}-700">
                        <i class="fa-solid {{ $messageType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' }}"></i>{{ session($messageType) }}
                    </div>
                @endif
            @endforeach
            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}</div>
            @endif

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Hadir Hari Ini</p><p class="mt-2 text-3xl font-black text-emerald-600">{{ number_format($hadirHariIni) }}</p><p class="mt-1 text-xs text-slate-500">Karyawan sudah masuk</p></div><span class="rounded-xl bg-emerald-50 p-3 text-emerald-600"><i class="fa-solid fa-user-check"></i></span></div></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Belum Absen Pulang</p><p class="mt-2 text-3xl font-black text-amber-500">{{ number_format($belumPulang) }}</p><p class="mt-1 text-xs text-slate-500">Masih berada di area kerja</p></div><span class="rounded-xl bg-amber-50 p-3 text-amber-500"><i class="fa-solid fa-person-walking-arrow-right"></i></span></div></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Terlambat Bulan Ini</p><p class="mt-2 text-3xl font-black text-rose-600">{{ number_format($totalTerlambat) }}</p><p class="mt-1 text-xs text-slate-500">Dari {{ number_format($totalHadir) }} presensi</p></div><span class="rounded-xl bg-rose-50 p-3 text-rose-600"><i class="fa-solid fa-clock-rotate-left"></i></span></div></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Karyawan Terdaftar</p><p class="mt-2 text-3xl font-black text-blue-600">{{ number_format($totalKaryawan) }}</p><p class="mt-1 text-xs text-slate-500">Data master karyawan</p></div><span class="rounded-xl bg-blue-50 p-3 text-blue-600"><i class="fa-solid fa-users"></i></span></div></div>
            </section>

            <div class="grid grid-cols-1 gap-6 2xl:grid-cols-5">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm 2xl:col-span-2">
                    <div class="border-b border-slate-100 px-5 py-5 md:px-6"><h2 class="font-extrabold text-slate-900">Catat Kehadiran</h2><p class="mt-1 text-xs text-slate-500">Gunakan NOPK yang sama untuk presensi berikutnya.</p></div>
                    <form action="{{ route('absensi.store') }}" method="POST" class="space-y-4 p-5 md:p-6">
                        @csrf
                        <div>
                            <label for="employee_no" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">NOPK / ID Karyawan</label>
                            <div class="relative"><i class="fa-regular fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="employee_no" name="employee_no" list="employee-options" value="{{ old('employee_no') }}" required autocomplete="off" placeholder="Contoh: EMP-005" class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"></div>
                            <datalist id="employee-options">@foreach($members as $member)<option value="{{ $member->employee_no }}">{{ $member->name }} — {{ $member->role }}</option>@endforeach</datalist>
                        </div>
                        <div><label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Karyawan</label><div class="relative"><i class="fa-regular fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="name" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap" class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"></div></div>
                        <div><label for="jabatan" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jabatan</label><select id="jabatan" name="jabatan" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"><option value="">Pilih jabatan</option>@foreach(['Staff Gudang', 'Operator', 'Admin Gudang', 'Supervisor'] as $jabatan)<option value="{{ $jabatan }}" @selected(old('jabatan') === $jabatan)>{{ $jabatan }}</option>@endforeach</select></div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500"><i class="fa-solid fa-circle-info mr-1 text-blue-500"></i> Status <strong>terlambat</strong> otomatis diberikan untuk absensi setelah pukul 08.00.</div>
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200"><i class="fa-solid fa-right-to-bracket"></i> Simpan Absensi Masuk</button>
                    </form>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm 2xl:col-span-3">
                    <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 md:flex-row md:items-center md:justify-between md:px-6">
                        <div><h2 class="font-extrabold text-slate-900">Riwayat Kehadiran</h2><p class="mt-1 text-xs text-slate-500">Presensi bulan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}.</p></div>
                        <form method="GET" class="flex items-center gap-2"><select name="month" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500" aria-label="Bulan">@for($i = 1; $i <= 12; $i++)<option value="{{ $i }}" @selected($month === $i)>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>@endfor</select><select name="year" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500" aria-label="Tahun">@for($i = now()->year + 1; $i >= now()->year - 5; $i--)<option value="{{ $i }}" @selected($year === $i)>{{ $i }}</option>@endfor</select><button class="rounded-lg bg-slate-800 px-3 py-2 text-sm font-bold text-white transition hover:bg-slate-700" type="submit" title="Terapkan filter"><i class="fa-solid fa-filter"></i></button></form>
                    </div>
                    <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm"><thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Karyawan</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3 text-center">Masuk</th><th class="px-4 py-3 text-center">Pulang</th><th class="px-4 py-3 text-center">Status</th><th class="px-5 py-3 text-center">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $attendance)
                            @php($isLate = in_array(strtoupper($attendance->status), ['LATE', 'TERLAMBAT']))
                            <tr class="transition hover:bg-slate-50"><td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $attendance->member?->name ?? $attendance->employee_id }}</p><p class="mt-0.5 font-mono text-xs text-slate-400">{{ $attendance->employee_id }} · {{ $attendance->member?->role ?? 'Karyawan' }}</p></td><td class="px-4 py-4 whitespace-nowrap text-slate-600">{{ $attendance->date->translatedFormat('d M Y') }}</td><td class="px-4 py-4 text-center font-mono font-bold text-slate-700">{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td><td class="px-4 py-4 text-center font-mono text-slate-500">{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td><td class="px-4 py-4 text-center"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $isLate ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $isLate ? 'Terlambat' : 'Hadir' }}</span></td><td class="px-5 py-4 text-center">@if($attendance->date->isToday() && $attendance->clock_in && !$attendance->clock_out)<form action="{{ route('absensi.clock-out', $attendance) }}" method="POST">@csrf @method('PATCH')<button class="rounded-lg bg-slate-800 px-3 py-2 text-xs font-bold text-white transition hover:bg-slate-700" type="submit"><i class="fa-solid fa-right-from-bracket mr-1"></i>Pulang</button></form>@elseif($attendance->clock_out)<span class="text-xs font-semibold text-slate-400"><i class="fa-solid fa-check mr-1 text-emerald-500"></i>Selesai</span>@else<span class="text-xs text-slate-400">—</span>@endif</td></tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-14 text-center text-slate-400"><i class="fa-regular fa-calendar-xmark mb-3 block text-3xl"></i><p class="font-medium">Belum ada data kehadiran pada periode ini.</p></td></tr>
                        @endforelse
                    </tbody></table></div>
                    @if($attendances->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $attendances->links() }}</div>@endif
                </section>
            </div>
        </div>
    </main>
</div>
<script>
    const employees = @json($members->mapWithKeys(fn ($member) => [$member->employee_no => ['name' => $member->name, 'role' => $member->role]]));
    const employeeInput = document.getElementById('employee_no');
    employeeInput.addEventListener('change', () => {
        const employee = employees[employeeInput.value.trim().toUpperCase()];
        if (!employee) return;
        document.getElementById('name').value = employee.name;
        document.getElementById('jabatan').value = employee.role;
    });
    setInterval(() => document.getElementById('live-clock').textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }), 1000);
</script>
</body>
</html>
