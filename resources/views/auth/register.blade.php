<x-layouts.shop title="Đăng ký | Vietec Store">
    <section class="auth-page">
        <p class="eyebrow">JOIN VIETEC</p>
        <h1>Tạo tài khoản<br><em>mới.</em></h1>
        @if ($errors->any()) <div class="form-errors">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('register.store') }}" class="checkout-form auth-form">
            @csrf
            <label>Họ và tên<input name="name" value="{{ old('name') }}" required autofocus></label>
            <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Mật khẩu<input type="password" name="password" required></label>
            <label>Xác nhận mật khẩu<input type="password" name="password_confirmation" required></label>
            <button class="detail-add checkout-submit" type="submit">Tạo tài khoản <span>→</span></button>
            <p class="auth-switch">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
        </form>
    </section>
</x-layouts.shop>