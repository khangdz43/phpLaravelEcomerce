<x-layouts.shop title="Checkout | Vietec Store">
    <div class="checkout-layout">
        <section>
            <a href="{{ route('shop.index') }}" class="back-link">← Tiếp tục mua sắm</a>
            <div class="checkout-heading">
                <p class="eyebrow">SECURE CHECKOUT</p>
                <h1>Hoàn tất<br><em>đơn hàng.</em></h1>
            </div>
            @if ($errors->any())
            <div class="form-errors" style="margin: 16px 0; padding: 14px 18px; background: rgba(185, 77, 62, 0.15); border: 1px solid var(--danger); color: var(--danger); border-radius: 10px; font-size: 13px;">
                {{ $errors->first() }}
            </div>
            @endif

            @if (($addresses ?? collect())->isNotEmpty())
            <div class="saved-addresses" style="margin-bottom: 20px; padding: 16px; background: var(--panel); border: 1px solid var(--line); border-radius: 12px;">
                <label style="display:block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Địa chỉ đã lưu của bạn:</label>
                <select id="saved-address-select" style="width: 100%; padding: 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff;">
                    <option value="">-- Chọn địa chỉ đã lưu --</option>
                    @foreach ($addresses as $addr)
                    <option value="{{ $addr->address_line }}" data-phone="{{ $addr->phone }}" data-name="{{ $addr->recipient_name }}">
                        {{ $addr->recipient_name }} | {{ $addr->phone }} | {{ $addr->address_line }}, {{ $addr->city }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <form method="POST" action="{{ route('shop.checkout.store') }}" data-checkout-form class="checkout-form">
                @csrf
                <input type="hidden" name="items" data-checkout-items>
                <label>Họ và tên
                    <input id="input-customer-name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required>
                </label>
                <div class="form-row">
                    <label>Email
                        <input id="input-customer-email" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" required>
                    </label>
                    <label>Số điện thoại
                        <input id="input-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                    </label>
                </div>
                <label>Địa chỉ giao hàng
                    <textarea id="input-shipping-address" name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                </label>

                <div class="form-row">
                    <label>Phương thức thanh toán
                        <select name="payment_method" style="width: 100%; padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: var(--panel);">
                            <option value="cod" @selected(old('payment_method') === 'cod')>Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Chuyển khoản ngân hàng</option>
                        </select>
                    </label>
                    <label>Ghi chú đơn hàng
                        <input name="notes" value="{{ old('notes') }}" placeholder="Ví dụ: Giao giờ hành chính...">
                    </label>
                </div>

                <div class="coupon-box" style="margin: 20px 0; padding: 16px; background: var(--panel); border: 1px solid var(--line); border-radius: 12px;">
                    <label style="display:block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Mã giảm giá (Coupon)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="coupon_code" id="coupon-code-input" placeholder="Nhập mã ưu đãi (ví dụ: SALE10)..." style="flex: 1; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; text-transform: uppercase;" value="{{ old('coupon_code') }}">
                        <button type="button" id="apply-coupon-btn" class="coupon-apply-btn" style="padding: 10px 16px; background: var(--ink); color: #fff; border: 0; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer;">Áp dụng</button>
                    </div>
                    <div id="coupon-feedback" style="margin-top: 8px; font-size: 12px;"></div>
                </div>

                <button class="detail-add checkout-submit" type="submit" style="width: 100%; padding: 16px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; cursor: pointer;">Xác nhận đặt hàng <span>→</span></button>
            </form>
        </section>

        <aside class="checkout-summary">
            <p class="eyebrow">ORDER SUMMARY</p>
            <h2>Tóm tắt giỏ hàng</h2>
            <div data-checkout-summary></div>
            <div class="summary-breakdown" style="border-top: 1px solid var(--line); margin-top: 18px; padding-top: 14px; display: grid; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--muted);">
                    <span>Tạm tính</span>
                    <strong data-checkout-subtotal style="color: var(--ink);">0 đ</strong>
                </div>
                <div id="coupon-discount-row" style="display: none; justify-content: space-between; font-size: 13px; color: var(--success);">
                    <span>Giảm giá mã coupon</span>
                    <strong data-checkout-discount>-0 đ</strong>
                </div>
                <div class="summary-total" style="margin-top: 8px; padding-top: 12px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">Tổng thanh toán</span>
                    <strong data-checkout-total style="font-size: 20px; color: var(--accent);">0 đ</strong>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.shop>