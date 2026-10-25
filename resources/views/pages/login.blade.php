<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
            <div class="bg-slate-900 px-6 py-5 text-white">
                <h1 class="text-2xl font-black">Stock Gudang</h1>
                <p class="mt-1 text-sm text-slate-300">Masuk untuk mengelola persediaan</p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="mb-1 block text-sm font-semibold text-slate-700">User</label>
                        <select id="username" name="username" required autofocus class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="Admin" @selected(old('username', 'Admin') === 'Admin')>Admin</option>
                            <option value="Staff" @selected(old('username') === 'Staff')>Staff</option>
                        </select>
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-semibold text-slate-700">Password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100" placeholder="Password">
                    </div>

                    <div class="flex items-center justify-between gap-3 text-sm">
                        <label class="inline-flex items-center gap-2 text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                        Masuk
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
