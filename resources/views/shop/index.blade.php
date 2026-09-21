<x-layouts.shop title="Vietec Store">
    <section class="hero-block">
        <div class="hero-copy">
            <span class="hero-kicker">BỘ SƯU TẬP MỚI · 2026</span>
            <h1>Thiết bị cho<br><em>nhịp làm việc mới.</em></h1>
            <p class="hero-description">Laptop, phụ kiện và những món đồ công nghệ được tuyển chọn để bạn tập trung vào điều quan trọng nhất: tạo ra sản phẩm tốt hơn.</p>
            <a href="#catalog" class="primary-button">Khám phá sản phẩm <span>↓</span></a>
        </div>
        <div class="hero-art"><span class="orbit orbit-one"></span><span class="orbit orbit-two"></span>
            <div class="hero-device"><span>VIETEC</span><b>WORK<br>WITHOUT<br>NOISE.</b></div>
            <div class="hero-sticker">EST.<br>2026</div>
        </div>
    </section>
    <section class="trust-strip">
        <div><b>✓</b><span>Thanh toán an toàn<strong>COD & chuyển khoản</strong></span></div>
        <div><b>↗</b><span>Giao hàng nhanh<strong>Toàn quốc 2-4 ngày</strong></span></div>
        <div><b>↺</b><span>Đổi trả dễ dàng<strong>Trong vòng 7 ngày</strong></span></div>
    </section>
    <section id="catalog" class="catalog-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">THE COLLECTION</p>
                <h2>Tools for momentum.</h2>
            </div><span class="result-count"><strong>{{ $products->total() }}</strong> đang bán <small>/ {{ $catalogTotal }} tổng catalog</small></span>
        </div>
        <div class="catalog-body">
            <aside class="catalog-sidebar"><strong>Danh mục</strong><a href="{{ route('shop.index') }}" class="{{ !request('category_id') ? 'is-active' : '' }}">Tất cả sản phẩm <span>{{ $products->total() }}</span></a>@foreach (($navigationCategories ?? collect()) as $category)<a href="{{ route('shop.index', ['category_id' => $category->id]) }}" class="{{ request('category_id') == $category->id ? 'is-active' : '' }}">{{ $category->name }} <span>{{ $category->products_count }}</span></a>@endforeach<div class="sidebar-note"><b>Vietec Promise</b>
                    <p>Sản phẩm chính hãng, kiểm tra trước khi gửi.</p>
                </div>
            </aside>
            <div class="catalog-results">
                <form method="GET" action="{{ route('shop.index') }}" class="filter-panel">
                    <div class="filter-main-row">
                        <div class="search-field">
                            <span>⌕</span>
                            <input name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, SKU, mô tả..." autocomplete="off">
                            @if (request('q'))
                            <a href="{{ route('shop.index', request()->except('q', 'page')) }}" class="clear-search-btn" title="Xóa từ khóa">×</a>
                            @endif
                        </div>
                        <div class="filter-select-group">
                            <select name="category_id" class="filter-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach (($navigationCategories ?? collect()) as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <select name="sort" class="filter-select">
                                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                                <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option>
                                <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option>
                                <option value="name_asc" @selected(request('sort') === 'name_asc')>Tên A-Z</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-price-row">
                        <div class="price-inputs">
                            <span class="price-label">Khoảng giá (đ):</span>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ (min)" min="0" step="10000" class="price-input">
                            <span class="price-divider">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến (max)" min="0" step="10000" class="price-input">
                        </div>
                        <div class="filter-actions">
                            <button class="filter-button" type="submit">Lọc sản phẩm <span>↗</span></button>
                            @if (request()->hasAny(['q', 'category_id', 'min_price', 'max_price']) || (request('sort') && request('sort') !== 'newest'))
                            <a href="{{ route('shop.index') }}" class="reset-filter-btn" title="Xóa toàn bộ bộ lọc">Xóa lọc ↺</a>
                            @endif
                        </div>
                    </div>

                    <div class="quick-price-tags">
                        <span class="quick-title">Chọn nhanh:</span>
                        <a href="{{ route('shop.index', array_merge(request()->except('page', 'min_price', 'max_price'), ['max_price' => 1000000])) }}" class="quick-price-btn {{ request('max_price') == 1000000 && !request('min_price') ? 'is-active' : '' }}">&lt; 1 triệu</a>
                        <a href="{{ route('shop.index', array_merge(request()->except('page'), ['min_price' => 1000000, 'max_price' => 5000000])) }}" class="quick-price-btn {{ request('min_price') == 1000000 && request('max_price') == 5000000 ? 'is-active' : '' }}">1tr - 5 triệu</a>
                        <a href="{{ route('shop.index', array_merge(request()->except('page'), ['min_price' => 5000000, 'max_price' => 15000000])) }}" class="quick-price-btn {{ request('min_price') == 5000000 && request('max_price') == 15000000 ? 'is-active' : '' }}">5tr - 15 triệu</a>
                        <a href="{{ route('shop.index', array_merge(request()->except('page', 'max_price'), ['min_price' => 15000000])) }}" class="quick-price-btn {{ request('min_price') == 15000000 && !request('max_price') ? 'is-active' : '' }}">&gt; 15 triệu</a>
                    </div>
                </form>

                @if (request()->hasAny(['q', 'category_id', 'min_price', 'max_price', 'sort']))
                <div class="active-filters-bar">
                    <span class="active-filters-title">Đang lọc:</span>
                    @if (request('q'))
                    <span class="filter-chip">Từ khóa: "{{ request('q') }}" <a href="{{ route('shop.index', request()->except('q', 'page')) }}">×</a></span>
                    @endif
                    @if (request('category_id'))
                    @php $currentCat = ($navigationCategories ?? collect())->firstWhere('id', request('category_id')); @endphp
                    <span class="filter-chip">Danh mục: {{ $currentCat?->name ?? request('category_id') }} <a href="{{ route('shop.index', request()->except('category_id', 'page')) }}">×</a></span>
                    @endif
                    @if (request('min_price') || request('max_price'))
                    <span class="filter-chip">Giá: {{ request('min_price') ? number_format((float) request('min_price'), 0, ',', '.') . 'đ' : '0' }} - {{ request('max_price') ? number_format((float) request('max_price'), 0, ',', '.') . 'đ' : '∞' }} <a href="{{ route('shop.index', request()->except('min_price', 'max_price', 'page')) }}">×</a></span>
                    @endif
                    @if (request('sort') && request('sort') !== 'newest')
                    <span class="filter-chip">Sắp xếp: {{ request('sort') === 'price_asc' ? 'Giá tăng dần' : (request('sort') === 'price_desc' ? 'Giá giảm dần' : 'Tên A-Z') }} <a href="{{ route('shop.index', request()->except('sort', 'page')) }}">×</a></span>
                    @endif
                </div>
                @endif

                <div class="product-grid">
                    @forelse ($products as $product)
                    <x-product-card :product="$product" />
                    @empty
                    <div class="empty-state"><span>◌</span>
                        <h3>Chưa tìm thấy sản phẩm phù hợp</h3>
                        <p>Hãy thử thay đổi từ khóa tìm kiếm hoặc điều chỉnh khoảng giá lọc nhé.</p>
                        <a href="{{ route('shop.index') }}" class="primary-button" style="margin-top: 18px; display: inline-block;">Xem tất cả sản phẩm</a>
                    </div>
                    @endforelse
                </div>
                <div class="pagination-wrap">{{ $products->links() }}</div>
            </div>
        </div>
    </section>
</x-layouts.shop>