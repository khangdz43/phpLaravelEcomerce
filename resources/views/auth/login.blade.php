<x-layouts.shop title="Đăng nhập | Vietec Store">
    <section class="auth-page">
        <p class="eyebrow">WELCOME BACK</p>
        <h1>Đăng nhập<br><em>tài khoản.</em></h1>
        @if ($errors->any()) <div class="form-errors">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('login.store') }}" class="checkout-form auth-form">@csrf<label>Email<input type="email" name="email" value="{{ old('email') }}" required></label><label>Mật khẩu<input type="password" name="password" required></label><label class="remember"><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label><button class="detail-add checkout-submit" type="submit">Đăng nhập <span>→</span></button></form>
    </section>
</x-layouts.shop>