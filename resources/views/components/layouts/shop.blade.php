<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vietec Store' }}</title>
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="announcement-bar">Miễn phí giao hàng cho đơn từ 500.000đ <span>•</span> Đổi trả trong 7 ngày</div>
    <header class="site-header">
        <div class="header-inner">
            <button class="mobile-menu" type="button" aria-label="Mở menu">☰</button>
            <a href="{{ route('shop.index') }}" class="brand"><span>VIETEC</span><b>/</b>STORE</a>
            <nav class="category-nav">
                <a href="{{ route('shop.index') }}">Tất cả</a>
                @foreach (($navigationCategories ?? collect()) as $category)
                <a href="{{ route('shop.index', ['category_id' => $category->id]) }}">{{ $category->name }} <small>{{ $category->products_count }}</small></a>
                @endforeach
            </nav>
            <a class="docs-link" href="{{ route('api.docs') }}">API</a>
            <button class="cart-trigger" type="button" data-cart-open aria-label="Mở giỏ hàng">
                <span class="cart-icon">+</span> Giỏ hàng <strong data-cart-count>0</strong>
            </button>
            @auth
            <a class="account-link" href="{{ route('account.orders') }}">Tài khoản</a>
            @else
            <a class="account-link" href="{{ route('login') }}">Đăng nhập</a>
            <a class="register-link" href="{{ route('register') }}">Đăng ký</a>
            @endauth
        </div>
    </header>
    <main class="page-shell">
        {{ $slot }}
    </main>
    <footer class="site-footer">
        <div><a href="{{ route('shop.index') }}" class="brand">VIETEC<span>/</span>STORE</a>
            <p>Thiết bị tốt cho những người xây dựng sản phẩm thật.</p>
        </div>
        <div><strong>Mua sắm</strong><a href="{{ route('shop.index') }}">Tất cả sản phẩm</a><a href="{{ route('shop.checkout') }}">Thanh toán</a></div>
        <div><strong>Hỗ trợ</strong><a href="{{ route('api.docs') }}">API Reference</a><a href="{{ route('login') }}">Tài khoản</a></div>
    </footer>
    <div class="cart-overlay" data-cart-overlay></div>
    <aside class="cart-drawer" data-cart-drawer aria-label="Giỏ hàng">
        <div class="drawer-head">
            <div><span class="eyebrow">YOUR BAG</span>
                <h2>Giỏ hàng</h2>
            </div><button type="button" data-cart-close aria-label="Đóng giỏ hàng">×</button>
        </div>
        <div class="cart-items" data-cart-items></div>
        <div class="drawer-foot">
            <div class="cart-total"><span>Tạm tính</span><strong data-cart-total>0 đ</strong></div><button class="checkout-button" type="button" data-checkout>Tiến hành đặt hàng <span>→</span></button>
        </div>
    </aside>
    <script src="{{ asset('js/shop.js') }}" defer></script>
</body>

</html>