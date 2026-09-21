<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vietec Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <header class="border-b border-white/10 bg-slate-950/95">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="{{ route('shop.index') }}" class="text-xl font-semibold tracking-tight">VIETEC<span class="text-cyan-400">/</span>STORE</a>
            <nav class="hidden gap-6 text-sm text-slate-300 md:flex">
                @foreach ($navigationCategories as $category)
                    <a href="{{ route('shop.index', ['category_id' => $category->id]) }}" class="transition hover:text-cyan-300">
                        {{ $category->name }} <span class="text-slate-500">({{ $category->products_count }})</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-6 py-10">
        {{ $slot }}
    </main>
</body>
</html>