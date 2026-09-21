<x-layouts.shop title="Đặt hàng thành công | Vietec Store">
    <section class="success-page">
        <div class="success-mark">✓</div>
        <p class="eyebrow">ORDER CONFIRMED</p>
        <h1>Cảm ơn bạn<br><em>đã đặt hàng.</em></h1>
        <p>Đơn hàng của bạn đã được tiếp nhận. Mã đơn hàng:</p><strong class="order-code">{{ $order->order_code }}</strong>
        <div class="success-card">
            <div><span>Tổng thanh toán</span><strong>@money($order->total_amount)</strong></div>
            <div><span>Trạng thái</span><strong>{{ $order->status }}</strong></div>
        </div><a href="{{ route('shop.index') }}" class="primary-button">Quay lại cửa hàng <span>→</span></a>
    </section>
    <script>
        localStorage.removeItem('vietec-cart');
    </script>
</x-layouts.shop>