<x-layouts.shop :title="$product->name">
    <a href="{{ route('shop.index') }}" class="text-sm text-cyan-300">← Quay lại cửa hàng</a>
    <div class="mt-8 grid gap-10 lg:grid-cols-[1.1fr_.9fr]">
        <div class="aspect-[4/3] rounded-3xl bg-gradient-to-br from-cyan-500/20 via-slate-800 to-indigo-500/20"></div>
        <div class="self-center">
            <p class="text-sm uppercase tracking-[.25em] text-cyan-300">{{ $product->category?->name }}</p>
            <h1 class="mt-4 text-4xl font-semibold text-white">{{ $product->name }}</h1>
            <p class="mt-5 text-3xl font-semibold text-amber-300">@money($product->sale_price ?? $product->price)</p>
            <p class="mt-6 leading-8 text-slate-400">{{ $product->description }}</p>
            <p class="mt-6 text-sm text-slate-500">SKU {{ $product->sku }} · Còn {{ $product->stock }} sản phẩm</p>
        </div>
    </div>
    <section class="mt-16 border-t border-white/10 pt-8">
        <h2 class="text-xl font-semibold text-white">Nhận xét</h2>
        <div class="mt-5 space-y-4">
            @forelse ($product->comments as $comment)
            <blockquote class="rounded-xl border border-white/10 bg-white/[.03] p-4 text-slate-300">
                <p>{{ $comment->body }}</p>
                <footer class="mt-2 text-sm text-slate-500">{{ $comment->user->name }}</footer>
            </blockquote>
            @empty
            <p class="text-slate-500">Chưa có nhận xét.</p>
            @endforelse
        </div>
    </section>
</x-layouts.shop>