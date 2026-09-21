<x-layouts.shop title="API Reference | Vietec Store">
    <section class="api-docs-page">
        <p class="eyebrow">DEVELOPER REFERENCE</p>
        <h1>API<br><em>contract.</em></h1>
        <p class="docs-lead">API không tự render thành trang web. Đây là contract JSON để Postman, mobile app hoặc frontend độc lập gọi vào backend Laravel.</p>
        <div class="docs-note"><strong>Base URL</strong><code>{{ url('/api') }}</code><span>API public dùng được ngay. Các endpoint bảo mật cần Bearer token lấy từ login.</span></div>
        <div class="endpoint-list">
            <article><span class="method get">GET</span><code>/products</code>
                <p>Danh sách sản phẩm published, search bằng `?q=`, sort bằng `?sort=price_asc`.</p>
                <pre>curl "{{ url('/api/products') }}"</pre>
            </article>
            <article><span class="method get">GET</span><code>/products/{slug}</code>
                <p>Chi tiết một sản phẩm theo slug.</p>
                <pre>curl "{{ url('/api/products/aut-alias-hic') }}"</pre>
            </article>
            <article><span class="method post">POST</span><code>/auth/login</code>
                <p>Đăng nhập và nhận Sanctum Bearer token.</p>
                <pre>curl -X POST "{{ url('/api/auth/login') }}" -H "Content-Type: application/json" -d '{"email":"staff@gmail.com","password":"12345678"}'</pre>
            </article>
            <article><span class="method get">GET</span><code>/orders</code>
                <p>Lịch sử đơn của user hiện tại. Gửi header `Authorization: Bearer TOKEN`.</p>
                <pre>curl "{{ url('/api/orders') }}" -H "Authorization: Bearer TOKEN" -H "Accept: application/json"</pre>
            </article>
            <article><span class="method post">POST</span><code>/orders</code>
                <p>Tạo đơn; server tự tính giá và kiểm tra stock.</p>
                <pre>curl -X POST "{{ url('/api/orders') }}" -H "Content-Type: application/json" -d '{"customer_name":"Test","customer_email":"test@example.com","customer_phone":"0900000000","shipping_address":"Hanoi","items":[{"product_id":1,"quantity":1}]}'</pre>
            </article>
        </div>
    </section>
</x-layouts.shop>