Phase 1: Core Prerequisites (Bắt buộc phải cứng)
Trước khi gõ laravel new, ông phải giải quyết triệt để phần base này.

OOP Deep Dive: Abstract Class vs Interface, Trait, Static vs Instance, Method Chaining.

Design Patterns căn bản: Singleton, Factory, Repository, Dependency Injection (DI), Strategy Pattern.

Modern PHP Standards: PSR-4 (Autoloading), PSR-12 (Coding Style), Namespaces, Type Hinting & Return Types, Anonymous Functions & Closure.

Composer Master: composer.json vs composer.lock, PSR-4 Autoloading configuration, custom package loading.

Phase 2: Fundamental Laravel (Làm chủ Tooling)
Giai đoạn này giúp ông xây dựng Web App chuẩn chỉnh.

Routing & Controllers: Restful APIs, Middleware Pipeline, Route Model Binding (Explicit & Implicit).

Blade Engine: Directives, Components, Layouts, View Composers.

Database & Eloquent ORM:

Migrations, Seeders, Factories.

Relationships: One to One, One to Many, Many to Many, Polymorphic Relations (Rất quan trọng).

Query Builder vs Eloquent: Tránh N+1 Problem bằng with() (Eager Loading), Lazy Loading, Dynamic Scopes.

Validation & Security: Form Requests, Custom Validation Rules, CSRF, XSS Prevention, SQL Injection Guards.

Authentication & Authorization: Laravel Breeze/Fortify, Gates & Policies, Sanctum vs Passport (API Auth).




Phase 3: Advanced Architecture & Under The Hood (Bước ngoặt lên Senior)
Đây là lúc ông đào sâu vào bản chất (Internals) của Laravel.

Request Lifecycle: Bắt đầu từ public/index.php -> HTTP Kernel -> Service Providers -> Router -> Middleware -> Controller -> Response.

IoC Container & Dependency Injection:

Hiểu App::bind(), App::singleton(), App::scoped().

Automatic Dependency Resolution (Reflection API).

Service Providers & Service Containers: Đăng ký (Register) và Thực thi (Boot) services.

Events & Listeners: Decoupling Code với Event Driven Architecture.

Queues, Jobs & Redis: Asynchronous Processing, Job Chaining, Batching, Failed Jobs, Rate Limiting.

Task Scheduling & Console Commands: Custom Artisan Commands (php artisan make:command).





Phase 4: Production, Performance & Scaling (Master Level)
Chạy code trên local tốt là chưa đủ, hệ thống chịu nải trên Production mới là thước đo.

Database Optimization: Indexing strategies, Query Caching, Database Partitioning, Read/Write Splitting.

Application Caching: Redis/Memcached integration, Cache Tags, Cache stampede protection.

Testing (Mandatory): Unit Testing, Integration Testing, Feature Testing với Pest PHP hoặc PHPUnit. Mocking Services, Database Transactions in Tests.

Ecosystem Integration:

Laravel Horizon: Monitoring Queues.

Laravel Telescope: Local Debugging & Profiling.

Laravel Scout: Full-text Search (Meilisearch/Algolia).

Laravel Octane: Tăng tốc độ bằng Swoole / FrankPHP (Hiểu về Memory Leaks khi dùng Octane).

DevOps & Deployment: CI/CD Pipelines (GitHub Actions), Dockerizing Laravel Apps, Zero-downtime Deployment (Laravel Forge/Envoyer hoặc Deployer).

Checklist Thực Hành (Projects cần làm)
Level 1 (Nền tảng): Bắt đầu bằng một Blog System hoặc Task Management đơn giản có Phân quyền (RBAC - Role Based Access Control) bằng Policy.

Level 2 (Trung cấp): Dựng RESTful API E-commerce System hoàn chỉnh. Có Payment Gateway (Stripe/Paypal), gửi Email xác nhận qua Queue, Caching Product catalog bằng Redis.

Level 3 (Senior/Master): Build một Multi-tenant SaaS Application (Hệ thống phần mềm dịch vụ đa người dùng), tối ưu Query cho DB hàng triệu record, setup Octane và viết Full Suite Test (Coverage > 80%).

## Learning Reference Map

This repository is intentionally organized as a working reference project. Follow one request from the edge to the database:

1. **Routes and middleware**: `routes/api.php`, `routes/web.php`, `bootstrap/app.php`.
2. **Validation and authorization**: `app/Http/Requests`, `app/Rules`, `app/Policies`, `app/Http/Middleware`.
3. **Application layer**: `app/Http/Controllers`, `app/Services`, `app/Repositories`, `app/DTOs`.
4. **Persistence and relationships**: `app/Models`, `database/migrations`, `database/factories`, `database/seeders`.
5. **Responses**: `app/Http/Resources`.
6. **Executable documentation**: `tests/Feature/EcommerceApiTest.php`.

### Included Learning Examples

- Implicit slug binding: `Product::getRouteKeyName()` and `/api/products/{product}`.
- Middleware plus Policy authorization for product mutations.
- Eloquent scopes: `published()` and `priceBetween()`.
- Polymorphic comments attached to products and orders.
- Sanctum token authentication and database-backed RBAC.
- Form Requests, a custom `SalePriceBelowPrice` validation rule, transactions, row locking, and eager loading.
- SQLite in-memory feature tests for repeatable local verification.

### Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
php artisan view:cache
```

The next production-oriented extensions are payment provider adapters, queued order emails, Redis catalog caching, order ownership/history, and CI coverage thresholds. They should be added as separate application capabilities after the core flows and tests are understood.
