<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin - Dashboard', description: 'Tổng quan vận hành cửa hàng')]
class AdminDashboardController extends Controller
{
    #[OA\Get(
        path: '/api/admin/dashboard',
        summary: 'Lấy thống kê tổng quan cho Admin',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Dashboard'],
        responses: [new OA\Response(response: 200, description: 'Thống kê dashboard')]
    )]
    public function index(): JsonResponse
    {
        $statusCounts = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return $this->successResponse([
            'orders' => [
                'total' => Order::count(),
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'processing' => (int) ($statusCounts['processing'] ?? 0),
                'completed' => (int) ($statusCounts['completed'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
            'revenue' => [
                'completed' => (float) Order::where('status', 'completed')->sum('total_amount'),
                'today' => (float) Order::where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->sum('total_amount'),
            ],
            'catalog' => [
                'products' => Product::count(),
                'published_products' => Product::where('status', 'published')->count(),
                'low_stock_products' => Product::where('stock', '<=', 5)->count(),
                'out_of_stock_products' => Product::where('stock', 0)->count(),
            ],
            'customers' => User::whereHas('roles', fn($query) => $query->where('name', 'customer'))->count(),
        ]);
    }
}
