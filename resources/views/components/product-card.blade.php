<article class="product-card" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->sale_price ?? $product->price }}">
    <a href="{{ route('shop.products.show', $product) }}" class="product-visual visual-{{ $product->id % 4 }}"><span class="product-code">NO.{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span><span class="visual-word">{{ strtoupper(Illuminate\Support\Str::before($product->name, ' ')) }}</span></a>
    <div class="product-info">
        <div>
            <p class="product-category">{{ $product->category?->name ?? 'ESSENTIAL' }}</p>
            <h2>{{ $product->name }}</h2>
        </div>
        <button class="add-button" type="button" data-add-cart><span>+</span></button>
        <div class="product-meta"><span class="price">@money($product->sale_price ?? $product->price)</span><a href="{{ route('shop.products.show', $product) }}">Chi tiết <span>↗</span></a></div>
    </div>
</article>