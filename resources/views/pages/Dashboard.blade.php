<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800 font-sans">
    <div class="flex min-h-screen flex-col md:flex-row">
        @include('components.sidebar')

        <main class="flex-1 min-w-0">
            <header class="bg-white border-b border-slate-200 px-4 py-4 md:px-8 md:py-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-black">Dashboard</h1>
                    <p class="text-sm text-slate-500">Ringkasan operasional gudang</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ url('/input-material') }}" class="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700 flex items-center justify-center sm:w-auto">
                        <i class="fa-solid fa-plus mr-2"></i>Input Material
                    </a>

                    <!-- Dropdown operator selector (pilihan kebawah) -->
                    <div class="relative">
                        <button id="operatorDropdownToggle" type="button" class="inline-flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 bg-white px-3 py-1 text-sm shadow-sm sm:w-auto">
                            <span class="hidden sm:inline">Pilih Operator</span>
                            <i class="fa-solid fa-user text-sm"></i>
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>

                        <div id="operatorDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-50 overflow-hidden">
                            <div class="py-2">
                                @foreach(($members ?? collect()) as $m)
                                    <button type="button" class="operator-option w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-3" data-id="{{ $m->id }}" data-name="{{ $m->name }}">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-xs">{{ strtoupper(substr(explode(' ', $m->name)[0] ?? $m->name,0,2)) }}</div>
                                        <div>
                                            <div class="font-semibold text-sm">{{ $m->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $m->role }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-4 space-y-6 md:p-8">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Material</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($totalMaterial) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Nama material terdaftar</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Stok Kritis</p>
                        <p class="mt-2 text-3xl font-black text-rose-600">{{ $stokKritis }}</p>
                        <p class="mt-1 text-xs text-slate-500">Perlu diperiksa</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:col-span-1">
                        <p class="border-b border-slate-100 pb-3 text-xs font-bold uppercase tracking-wider text-slate-700">Product Details</p>
                        <div class="mt-3 grid grid-cols-[1fr_auto] gap-x-4 gap-y-2 text-xs">
                            <div class="text-rose-400">Low Stock Items</div>
                            <div class="font-black text-rose-500">{{ number_format($stokKritis) }}</div>

                            <div class="text-slate-600">All Item Group</div>
                            <div class="font-black text-slate-700">{{ number_format($totalMaterial) }}</div>

                            <div class="text-slate-600">All Items</div>
                            <div class="font-black text-slate-700">{{ number_format($totalStok, 0) }}</div>

                            <div class="flex items-center gap-1 text-rose-400">
                                Unconfirmed Items
                                <span title="Material tanpa status" class="text-[10px]">ⓘ</span>
                            </div>
                            <div class="font-black text-rose-500">{{ number_format($unconfirmedItems) }}</div>
                        </div>

                        <div class="mt-3 flex items-center justify-end">
                            <div class="relative h-16 w-16 rounded-full" style="background: conic-gradient(#10b981 {{ $activePercentage }}%, #e5e7eb 0);">
                                <div class="absolute inset-2 flex items-center justify-center rounded-full bg-white text-xs font-black text-slate-700">
                                    {{ $activePercentage }}%
                                </div>
                            </div>
                            <span class="ml-3 text-xs text-slate-600">Active Items</span>
                        </div>
                    </div>
                </div>

                <!-- TIM GUDANG -->
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold">Tim Gudang & Tata Usaha</h2>
                        <p class="text-xs text-slate-500">Klik anggota untuk melihat kontak dan profil digital</p>
                    </div>
                    <div class="text-xs font-bold text-slate-500">{{ isset($members) ? $members->count() : 0 }} Personel</div>
                    </div>

                    <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($members ?? collect() as $m)
                            <button class="team-card flex items-center gap-4 p-4 rounded-xl border border-slate-200 bg-slate-100 hover:shadow-md transition" data-name="{{ $m->name }}" data-role="{{ $m->role }}" data-phone="{{ $m->phone }}" data-email="{{ $m->email }}" data-website="{{ $m->website }}">
                                <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold shadow-sm">{{ strtoupper(collect(explode(' ', $m->name))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode('')) }}</div>
                                <div class="text-left">
                                    <div class="font-bold text-slate-800">{{ $m->name }}</div>
                                    <div class="text-xs text-slate-600">{{ $m->role }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    </div>

                    <!-- Modal -->
                    <div id="teamModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-md w-full shadow-lg overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div id="modalAvatar" class="w-12 h-12 rounded-full bg-slate-300 flex items-center justify-center font-bold text-white">--</div>
                                    <div>
                                        <h3 id="modalName" class="text-lg font-bold">Nama</h3>
                                        <p id="modalRole" class="text-xs text-slate-500">Jabatan</p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div class="text-xs text-slate-400">Kontak</div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div id="modalPhone" class="font-mono font-semibold">+62...</div>
                                            <div id="modalEmail" class="text-xs text-slate-500">email@example.com</div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a id="callBtn" href="#" class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm">Panggil</a>
                                            <a id="emailBtn" href="#" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">Email</a>
                                            <a id="websiteBtn" href="#" target="_blank" class="px-3 py-2 bg-slate-700 text-white rounded-lg text-sm hidden">Website</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t text-right">
                                <button id="closeModal" class="px-4 py-2 bg-slate-100 rounded-lg">Tutup</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.querySelectorAll('.team-card').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const name = btn.dataset.name;
                                const role = btn.dataset.role;
                                const phone = btn.dataset.phone;
                                const email = btn.dataset.email;

                                document.getElementById('modalName').textContent = name;
                                document.getElementById('modalRole').textContent = role;
                                document.getElementById('modalPhone').textContent = phone;
                                document.getElementById('modalEmail').textContent = email;
                                document.getElementById('modalAvatar').textContent = name.split(' ').map(n=>n.charAt(0)).slice(0,2).join('').toUpperCase();

                                document.getElementById('callBtn').href = `tel:${phone}`;
                                document.getElementById('emailBtn').href = `mailto:${email}`;
                                if (btn.dataset.website) {
                                    document.getElementById('websiteBtn').href = btn.dataset.website;
                                    document.getElementById('websiteBtn').classList.remove('hidden');
                                } else {
                                    document.getElementById('websiteBtn').classList.add('hidden');
                                }

                                        document.getElementById('teamModal').classList.remove('hidden');
                                document.getElementById('teamModal').classList.add('flex');
                            });
                        });

                                // Operator selection from header avatars
                                function selectOperator(id, name) {
                                    try {
                                        localStorage.setItem('selectedOperatorId', id);
                                        localStorage.setItem('selectedOperatorName', name);
                                    } catch (e) {
                                        console.warn('LocalStorage not available', e);
                                    }
                                    // Navigate to input-material with operator query param for immediate prefill
                                    window.location.href = '/input-material?operator=' + encodeURIComponent(id);
                                }

                                // also allow operator-select buttons to show tooltip on hover (title already set)
                                document.querySelectorAll('.operator-option').forEach((btn) => {
                                    btn.addEventListener('click', () => {
                                        const id = btn.dataset.id;
                                        const name = btn.dataset.name;
                                        selectOperator(id, name);
                                    });
                                });

                                document.querySelectorAll('.operator-select').forEach(b => b.addEventListener('click', e => {}));

                                // Dropdown toggle behavior for operator selector
                                const operatorToggle = document.getElementById('operatorDropdownToggle');
                                const operatorDropdown = document.getElementById('operatorDropdown');
                                if (operatorToggle && operatorDropdown) {
                                    operatorToggle.addEventListener('click', (ev) => {
                                        ev.stopPropagation();
                                        operatorDropdown.classList.toggle('hidden');
                                    });

                                    // close when clicking outside
                                    document.addEventListener('click', (ev) => {
                                        if (!operatorDropdown.classList.contains('hidden')) {
                                            operatorDropdown.classList.add('hidden');
                                        }
                                    });
                                }

                        document.getElementById('closeModal').addEventListener('click', () => {
                            document.getElementById('teamModal').classList.add('hidden');
                            document.getElementById('teamModal').classList.remove('flex');
                        });
                    </script>

                <!-- Peringatan Stok Kritis -->

                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                        <div>
                            <h2 class="text-lg font-extrabold">Peringatan Stok Kritis</h2>
                            <p class="text-xs text-slate-500">Material dengan jumlah di bawah batas minimum</p>
                        </div>
                        <a href="{{ url('/daftar-stok') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-6 py-3">Kode</th>
                                    <th class="px-6 py-3">Nama Material</th>
                                    <th class="px-6 py-3">Stok</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criticalMaterials as $material)
                                    <tr class="border-t border-slate-100">
                                        <td class="px-6 py-4 font-mono font-bold">{{ $material->kode_barang }}</td>
                                        <td class="px-6 py-4">{{ $material->nama_material }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ number_format($material->stok_akhir, 2) }} {{ $material->satuan }}</td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">{{ $material->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Tidak ada stok kritis.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
