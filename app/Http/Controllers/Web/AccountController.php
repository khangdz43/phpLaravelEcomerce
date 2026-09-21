<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\AddressRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\AddressService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService,
        private readonly OrderService $orderService,
    ) {}

    public function orders(Request $request): View
    {
        return view('account.orders', [
            'orders' => $request->user()->orders()->with('items.product')->latest()->paginate(10),
        ]);
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('account.order-show', [
            'order' => $order->load(['items.product', 'payments', 'coupon']),
        ]);
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        $this->orderService->cancel($order, $request->user());

        return back()->with('success', 'Đơn hàng đã được hủy.');
    }

    public function addresses(Request $request): View
    {
        return view('account.addresses', [
            'addresses' => $request->user()->addresses()->latest()->get(),
        ]);
    }

    public function storeAddress(AddressRequest $request): RedirectResponse
    {
        $this->addressService->store($request->user(), $request->validated());

        return back()->with('success', 'Đã lưu địa chỉ giao hàng.');
    }

    public function destroyAddress(Request $request, Address $address): RedirectResponse
    {
        $this->addressService->destroy($request->user(), $address);

        return back()->with('success', 'Đã xóa địa chỉ.');
    }
}
