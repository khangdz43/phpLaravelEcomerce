<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

/**
 * @OA\Info(
 *     title="E-Commerce Core API Documentation",
 *     version="1.0.0",
 *     description="API documentation for Laravel E-Commerce Core project including Auth, Products, Categories, Cart, Orders, Coupons, Reviews/Comments, Addresses, and Admin Management.",
 *     @OA\Contact(
 *         email="admin@ecommerce-core.com",
 *         name="API Support Team"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter Sanctum Bearer Token"
 * )
 */
abstract class Controller
{
    use ApiResponse;
}

