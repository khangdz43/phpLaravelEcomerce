<x-layouts.shop :title="$product->name">
    <a href="{{ route('shop.index') }}" class="back-link">← Quay lại cửa hàng</a>
    <div class="detail-layout">
        <div class="detail-visual visual-{{ $product->id % 4 }}"><span class="product-code">NO.{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span><span class="visual-word">{{ strtoupper(Illuminate\Support\Str::before($product->name, ' ')) }}</span></div>
        <div class="detail-copy">
            <p class="product-category">{{ $product->category?->name ?? 'ESSENTIAL' }}</p>
            <h1>{{ $product->name }}</h1>
            <p class="detail-price">@money($product->sale_price ?? $product->price)</p>
            <p class="detail-description">{{ $product->description }}</p>
            <p class="detail-stock">SKU {{ $product->sku }} <span>•</span> Còn {{ $product->stock }} sản phẩm</p>
            <div class="detail-actions" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->sale_price ?? $product->price }}">
                @if ($product->stock > 0)
                <div class="quantity-stepper">
                    <button type="button" class="qty-btn" data-qty-dec aria-label="Giảm số lượng">−</button>
                    <input type="number" id="detail-quantity" class="qty-input" value="1" min="1" max="{{ min(99, $product->stock) }}" data-qty-input>
                    <button type="button" class="qty-btn" data-qty-inc aria-label="Tăng số lượng">+</button>
                </div>
                <button class="detail-add" type="button" data-add-cart>Thêm vào giỏ <span>+</span></button>
                @else
                <button class="detail-add is-disabled" type="button" disabled>Hết hàng</button>
                @endif
                <a href="#reviews" class="review-link">Xem nhận xét ↓</a>
            </div>
        </div>
    </div>
    <section id="reviews" class="reviews-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">FROM THE COMMUNITY</p>
                <h2>Nhận xét</h2>
            </div><span class="result-count">{{ $product->comments->count() }} nhận xét</span>
        </div>

        @if (session('success'))
        <div class="alert alert-success" style="margin: 16px 0; padding: 12px 18px; background: rgba(45, 157, 120, 0.15); border: 1px solid var(--success); color: var(--success); border-radius: 10px; font-size: 13px;">
            {{ session('success') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger" style="margin: 16px 0; padding: 12px 18px; background: rgba(185, 77, 62, 0.15); border: 1px solid var(--danger); color: var(--danger); border-radius: 10px; font-size: 13px;">
            {{ $errors->first() }}
        </div>
        @endif

        @auth
        <form method="POST" action="{{ route('shop.comments.store', $product) }}" class="comment-form">
            @csrf
            <label for="comment-body" style="display:block; margin-bottom: 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">Gửi nhận xét của bạn</label>
            <textarea id="comment-body" name="body" rows="3" class="comment-input" placeholder="Viết đánh giá, trải nghiệm sử dụng sản phẩm..." required style="width: 100%; border: 1px solid var(--line); border-radius: 12px; padding: 14px; background: var(--panel); color: var(--ink); margin-bottom: 12px;"></textarea>
            <button type="submit" class="comment-submit-btn" style="background: var(--ink); color: #fff; border: 0; padding: 10px 18px; border-radius: 10px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; cursor: pointer;">Gửi nhận xét <span>↗</span></button>
        </form>
        @else
        <div class="comment-login-prompt" style="padding: 16px 20px; background: var(--panel); border: 1px solid var(--line); border-radius: 12px; font-size: 13px; margin-bottom: 24px;">
            Vui lòng <a href="{{ route('login') }}" style="color: var(--accent); font-weight: 600; text-decoration: underline;">Đăng nhập</a> để chia sẻ nhận xét của bạn.
        </div>
        @endauth

        <div class="review-list">
            @forelse ($product->comments as $comment)
            <blockquote class="review-card">
                <p>{{ $comment->body }}</p>
                <footer>{{ $comment->user->name }} • <small>{{ $comment->created_at->diffForHumans() }}</small></footer>
            </blockquote>
            @empty
            <p class="muted-copy" style="grid-column: 1/-1; padding: 24px; text-align: center; color: var(--muted); background: var(--panel); border: 1px solid var(--line); border-radius: 12px;">Chưa có nhận xét nào cho sản phẩm này.</p>
            @endforelse
        </div>
    </section>
</x-layouts.shop>