<x-layouts.shop title="Đơn hàng của tôi | Vietec Store">
    <section class="account-page">
        <p class="eyebrow">MY ACCOUNT</p>
        <h1>Đơn hàng<br><em>của tôi.</em></h1>
        <div class="account-actions"><span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Đăng xuất</button></form>
        </div>
        <div class="order-list">@forelse ($orders as $order)<article class="order-row">
                <div><strong>{{ $order->order_code }}</strong><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                <div><span>{{ $order->items->count() }} sản phẩm</span><strong>@money($order->total_amount)</strong></div><b class="status-pill">{{ $order->status }}</b>
            </article>@empty<p class="muted-copy">Bạn chưa có đơn hàng nào.</p>@endforelse</div>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    </section>
</x-layouts.shop>