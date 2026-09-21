<x-layouts.shop title="Vietec Store">
    <section class="mb-12 max-w-3xl">
        <p class="mb-4 text-sm font-medium uppercase tracking-[.3em] text-cyan-300">Ecommerce learning lab</p>
        <h1 class="text-4xl font-semibold tracking-tight text-white md:text-6xl">Thiết bị tốt cho những dự án nghiêm túc.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-400">Một storefront Blade thật, chạy trên cùng domain model và query layer với REST API.</p>
    </section>
    <form method="GET" class="mb-8 flex max-w-xl gap-3">
        <input name="q" value="{{ request('q') }}" placeholder="Tìm sản phẩm..." class="min-w-0 flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none ring-cyan-400 focus:ring-2">
        <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950">Tìm</button>
    </form>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
        <x-product-card :product="$product" />
        @empty
        <p class="text-slate-400">Không tìm thấy sản phẩm phù hợp.</p>
        @endforelse
    </div>
    <div class="mt-10">{{ $products->links() }}</div>
</x-layouts.shop>