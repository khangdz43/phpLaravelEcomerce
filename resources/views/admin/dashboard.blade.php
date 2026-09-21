<x-layouts.shop title="Quản trị | Vietec Store">
    <section class="admin-page">
        <div class="admin-heading">
            <div>
                <p class="eyebrow">OPERATIONS / CONTROL ROOM</p>
                <h1>Quản trị<br><em>cửa hàng.</em></h1>
            </div><a href="{{ route('shop.index') }}" class="back-link">← Về storefront</a>
        </div>
        @if (session('success')) <div class="success-notice">{{ session('success') }}</div> @endif
        <div class="admin-stats">
            <div><span>Sản phẩm</span><strong>{{ $products->total() }}</strong></div>
            <div><span>Đơn hàng</span><strong>{{ $orders->total() }}</strong></div>
            <div><span>Đang xử lý</span><strong>{{ $orders->where('status', 'processing')->count() }}</strong></div>
        </div>
        <div class="admin-table-block">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">LATEST ORDERS</p>
                    <h2>Đơn hàng mới</h2>
                </div>
            </div>
            <div class="admin-table">@forelse ($orders as $order)<div class="admin-row">
                    <div><strong>{{ $order->order_code }}</strong><span>{{ $order->customer_name }} · {{ $order->customer_phone }}</span></div><strong>@money($order->total_amount)</strong>
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()">@foreach (\App\Enums\OrderStatus::cases() as $status)<option value="{{ $status->value }}" @selected($order->status === $status->value)>{{ $status->value }}</option>@endforeach</select></form>
                </div>@empty<p class="muted-copy">Chưa có đơn hàng.</p>@endforelse</div>
        </div>
        <div class="admin-table-block">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">CATALOG</p>
                    <h2>Sản phẩm</h2>
                </div>
            </div>
            <div class="admin-table">@foreach ($products as $product)<div class="admin-row">
                    <div><strong>{{ $product->name }}</strong><span>{{ $product->category?->name }} · SKU {{ $product->sku }}</span></div><strong>@money($product->price)</strong><span class="status-pill">{{ $product->status }} · {{ $product->stock }} kho</span>
                </div>@endforeach</div>
        </div>
    </section>
</x-layouts.shop>