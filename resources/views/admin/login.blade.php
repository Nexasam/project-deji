<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login – Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-950 antialiased">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            <a href="{{ route('home') }}" class="text-sm font-extrabold text-orange-600">Verified Shortlet</a>
            <p class="mt-8 text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Platform administration</p>
            <h1 class="mt-2 text-2xl font-extrabold">Sign in to review properties</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">This area is restricted to authorised platform administrators.</p>

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-7 space-y-4">
                @csrf
                <label class="block text-sm font-bold text-slate-700">Email
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                </label>
                @error('email')<p class="text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                <label class="block text-sm font-bold text-slate-700">Password
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-600"><input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">Remember me</label>
                <button class="w-full rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-extrabold text-white hover:bg-orange-700">Sign in as administrator</button>
            </form>
            <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-bold text-slate-500 hover:text-slate-900">Owner or guest login</a>
        </section>
    </main>
</body>
</html>
