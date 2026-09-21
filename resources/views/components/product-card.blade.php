<article class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.04] transition hover:-translate-y-1 hover:border-cyan-400/50">
    <div class="aspect-[4/3] bg-gradient-to-br from-cyan-500/20 via-slate-800 to-indigo-500/20"></div>
    <div class="space-y-3 p-5">
        <p class="text-xs uppercase tracking-[.2em] text-cyan-300">{{ $product->category?->name }}</p>
        <h2 class="text-lg font-medium text-white">{{ $product->name }}</h2>
        <div class="flex items-center justify-between gap-4">
            <span class="font-semibold text-amber-300">@money($product->sale_price ?? $product->price)</span>
            <a href="{{ route('shop.products.show', $product) }}" class="text-sm text-slate-300 transition group-hover:text-white">Xem chi tiết →</a>
        </div>
    </div>
</article>