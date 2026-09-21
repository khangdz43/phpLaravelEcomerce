<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\LoginRequest;
use App\Http\Requests\Web\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function storeRegistration(RegisterRequest $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();
        $this->authService->registerWeb($request->validated());
        app(\App\Services\CartService::class)->mergeGuestCart($request->user(), $sessionId);
        $request->session()->regenerate();

        return redirect()->route('shop.index')->with('success', 'Tài khoản đã được tạo thành công.');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();

        if (! Auth::attempt($request->validated(), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.'])->withInput();
        }

        $request->session()->regenerate();
        app(\App\Services\CartService::class)->mergeGuestCart($request->user(), $sessionId);

        return redirect()->intended(route('shop.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.index');
    }
}
