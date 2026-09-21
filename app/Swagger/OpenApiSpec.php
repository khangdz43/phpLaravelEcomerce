<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API documentation for Laravel E-Commerce Core project including Auth, Products, Categories, Cart, Orders, Coupons, Reviews/Comments, Addresses, and Admin Management.",
    title: "E-Commerce Core API Documentation",
    contact: new OA\Contact(name: "API Support Team", email: "admin@gmail.com")
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class OpenApiSpec {}
