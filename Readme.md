# E-Commerce API - Laravel

API sistem e-commerce lengkap dengan autentikasi JWT dan integrasi payment gateway Xendit.

## 📋 Daftar Isi

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)

---

## 🚀 Features

- ✅ **Authentication System**
  - Register & Login dengan JWT
  - Token-based authentication
  - Logout dengan token invalidation

- ✅ **Product Management**
  - List semua produk (dengan pagination)
  - Detail produk

- ✅ **Shopping & Checkout**
  - Cart system via checkout API
  - Stock validation otomatis
  - Multiple items dalam satu order

- ✅ **Payment Integration**
  - Integrasi Xendit Payment Gateway
  - Generate invoice otomatis
  - Webhook untuk notifikasi pembayaran
  - Support multiple payment methods (Virtual Account, E-Wallet, dll)

- ✅ **Order Management**
  - Riwayat pemesanan
  - Detail order dengan payment info
  - Status tracking (pending, paid, processing, shipped, completed)

- ✅ **Security**
  - Custom API Key protection
  - JWT Authentication
  - Webhook verification
  - Input validation
  - Database transactions

---

## 🛠️ Tech Stack

- **Backend Framework**: Laravel 11
- **Authentication**: JWT (tymon/jwt-auth)
- **Payment Gateway**: Xendit PHP SDK
- **Database**: MySQL
- **API Architecture**: RESTful API

---

## 📦 Requirements

Pastikan sistem Anda sudah terinstall:

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 5.7 atau MariaDB >= 10.3
- Git
- Text Editor (VS Code, Sublime, dll)

### Optional (untuk development):
- Laragon (Windows) / Valet (Mac) / Homestead
- Postman (untuk testing API)
- Ngrok (untuk webhook testing di localhost)

---

## 🔧 Installation

### Step 1: Clone Repository

```bash
# Clone repository
git clone <repository-url> ecommerce-api
cd ecommerce-api
```

Atau buat project Laravel baru:

```bash
composer create-project laravel/laravel ecommerce-api
cd ecommerce-api
```

### Step 2: Install Dependencies

```bash
# Install composer dependencies
composer install

# Install JWT Auth
composer require tymon/jwt-auth

# Install Xendit PHP SDK
composer require xendit/xendit-php
```

### Step 3: Copy Environment File

```bash
# Copy .env.example ke .env
cp .env.example .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Generate JWT Secret

```bash
# Publish JWT config
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Generate JWT secret key
php artisan jwt:secret
```

---

## ⚙️ Configuration

### 1. Database Configuration

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_api
DB_USERNAME=root
DB_PASSWORD=
```

**Buat database di MySQL/phpMyAdmin**:

```sql
CREATE DATABASE ecommerce_api;
```

### 2. API Key Configuration

Generate API key untuk security layer:

```bash
# Generate random string (32 characters)
php -r "echo bin2hex(random_bytes(32));"
```

Copy hasilnya dan tambahkan ke `.env`:

```env
# Custom API Key (hasil generate di atas)
API_KEY=hasil_generate_random_string_disini
```

### 3. Xendit Configuration

#### A. Daftar Akun Xendit

1. Buka https://dashboard.xendit.co/register
2. Daftar akun baru (gratis)
3. Verifikasi email
4. Login ke dashboard

#### B. Aktifkan Test Mode

1. Di dashboard Xendit, **toggle ke Test Mode** (pojok kanan atas)
2. Pastikan ada tulisan "Test Mode" di dashboard

#### C. Dapatkan API Keys

1. Go to **Settings → Developers → API Keys**
2. Copy **Secret Key** (yang diawali `xnd_development_`)
3. Copy **Public Key** (optional)

#### D. Dapatkan Webhook Token

1. Go to **Settings → Webhooks**
2. Scroll ke bagian **Webhook Settings**
3. Copy **Verification Token**

#### E. Update .env

```env
# Xendit Configuration
XENDIT_SECRET_KEY=xnd_development_xxxxxxxxxxxxxxxxxxxxx
XENDIT_WEBHOOK_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 4. Update Config Files

#### A. `config/app.php`

Tambahkan di array return:

```php
return [
    // ... existing config
    
    'api_key' => env('API_KEY'),
];
```

#### B. `config/services.php`

Tambahkan di array return:

```php
return [
    // ... existing services
    
    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    ],
];
```

#### C. `config/auth.php`

Update guards:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
        'hash' => false,
    ],
],
```

### 5. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🗄️ Database Setup

### Step 1: Copy Model Files

Copy semua file model dari repository ke folder `app/Models/`:
- `User.php`
- `Product.php`
- `Order.php`
- `OrderItem.php`
- `Payment.php`

### Step 2: Copy Migration Files

Copy semua migration files ke `database/migrations/`:
- `create_users_table.php`
- `create_products_table.php`
- `create_orders_table.php`
- `create_order_items_table.php`
- `create_payments_table.php`

### Step 3: Copy Middleware

Copy `ApiKeyMiddleware.php` ke `app/Http/Middleware/`

### Step 4: Copy Controllers

Copy semua controllers ke `app/Http/Controllers/Api/`:
- `AuthController.php`
- `ProductController.php`
- `CheckoutController.php`
- `PaymentController.php`
- `OrderController.php`

### Step 5: Setup Routes

Replace isi `routes/api.php` dengan kode dari repository.

### Step 6: Register Middleware

#### Untuk Laravel 11:

Edit `bootstrap/app.php`:

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

#### Untuk Laravel 10:

Edit `app/Http/Kernel.php`, tambahkan di `$middlewareAliases`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
];
```

### Step 7: Update Exception Handler

Edit `app/Exceptions/Handler.php`:

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
```

### Step 8: Run Migrations

```bash
# Run migrations
php artisan migrate
```

### Step 9: Seed Database (Optional)

Copy `ProductSeeder.php` ke `database/seeders/`:

```bash
# Run seeder
php artisan db:seed --class=ProductSeeder
```

Atau seed manual via tinker:

```bash
php artisan tinker
```

```php
\App\Models\Product::create([
    'name' => 'Laptop Gaming',
    'description' => 'Laptop gaming dengan spesifikasi tinggi',
    'price' => 15000000,
    'stock' => 10,
]);

\App\Models\Product::create([
    'name' => 'Mouse Wireless',
    'description' => 'Mouse wireless ergonomis',
    'price' => 250000,
    'stock' => 50,
]);
```

---

## 🚀 Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve
```

API akan berjalan di: `http://localhost:8000`

### Verify Installation

Test endpoint:

```bash
curl http://localhost:8000/api/products
```

Jika API Key protection bekerja, akan dapat response:

```json
{
  "success": false,
  "message": "Invalid or missing API key"
}
```

✅ Ini berarti API sudah berjalan dengan baik!

---

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

Semua endpoint membutuhkan **API Key** di header:

```
X-API-KEY: your-api-key-from-env
```

Endpoint yang membutuhkan user authentication juga butuh **JWT Token**:

```
Authorization: Bearer your-jwt-token
```

### Endpoints

#### 1. Authentication

##### Register
- **POST** `/register`
- **Headers**: `X-API-KEY`
- **Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "08123456789",
  "address": "Jl. Contoh No. 123"
}
```
- **Response**:
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer"
  }
}
```

##### Login
- **POST** `/login`
- **Headers**: `X-API-KEY`
- **Body**:
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
- **Response**: Same as Register

##### Logout
- **POST** `/logout`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

##### Get Current User
- **GET** `/me`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### 2. Products

##### Get All Products
- **GET** `/products?per_page=10`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Laptop Gaming",
        "description": "Laptop gaming dengan spesifikasi tinggi",
        "price": "15000000.00",
        "stock": 10
      }
    ],
    "total": 10
  }
}
```

##### Get Product Detail
- **GET** `/products/{id}`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Gaming",
    "description": "Laptop gaming dengan spesifikasi tinggi",
    "price": "15000000.00",
    "stock": 10
  }
}
```

#### 3. Checkout

##### Create Order
- **POST** `/checkout`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Body**:
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ],
  "shipping_address": "Jl. Contoh No. 123, Jakarta"
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-ABC123",
    "total_amount": "30250000.00",
    "status": "pending",
    "items": [...]
  }
}
```

#### 4. Payment

##### Create Invoice
- **POST** `/payment/create-invoice`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Body**:
```json
{
  "order_id": 1
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Invoice created successfully",
  "data": {
    "payment": {
      "id": 1,
      "external_id": "INV-ORD-ABC123",
      "invoice_url": "https://checkout.xendit.co/web/xxx",
      "status": "pending"
    },
    "invoice_url": "https://checkout.xendit.co/web/xxx",
    "expired_at": "2024-01-02T10:00:00.000Z"
  }
}
```

##### Webhook (Xendit Callback)
- **POST** `/payment/webhook`
- **Headers**: `x-callback-token: {xendit-webhook-token}`
- **Body**: (Dikirim otomatis oleh Xendit)
```json
{
  "external_id": "INV-ORD-ABC123",
  "status": "PAID",
  "paid_at": "2024-01-01T10:00:00.000Z"
}
```

#### 5. Orders (Riwayat Pemesanan)

##### Get Order History
- **GET** `/orders?per_page=10`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Query Parameters**:
  - `per_page` (optional): Jumlah item per halaman (default: 10)
- **Response**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "order_number": "ORD-ABC123",
        "total_amount": "30250000.00",
        "status": "paid",
        "payment": {
          "status": "paid",
          "paid_at": "2024-01-01T10:00:00.000Z"
        }
      }
    ]
  }
}
```

##### Get Order Detail
- **GET** `/orders/{id}`
- **Headers**: `X-API-KEY`, `Authorization: Bearer {token}`
- **Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "order_number": "ORD-ABC123",
    "total_amount": "30250000.00",
    "status": "paid",
    "items": [...],
    "payment": {...}
  }
}
```

---

## 🧪 Testing

### Testing dengan cURL

#### 1. Register User

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-api-key" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "08123456789",
    "address": "Jakarta"
  }'
```

#### 2. Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-api-key" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Simpan token dari response!**

#### 3. Get Products

```bash
curl -X GET http://localhost:8000/api/products \
  -H "X-API-KEY: your-api-key" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 4. Checkout

```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-api-key" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 1}
    ],
    "shipping_address": "Jakarta"
  }'
```

#### 5. Create Payment

```bash
curl -X POST http://localhost:8000/api/payment/create-invoice \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-api-key" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "order_id": 1
  }'
```

### Testing dengan Postman

#### Setup Postman

1. **Import Collection** (jika ada file JSON collection)
2. **Create Environment**:
   - `base_url`: `http://localhost:8000/api`
   - `api_key`: `your-api-key-from-env`
   - `token`: (akan auto-fill setelah login)

#### Auto-Save Token Script

Tambahkan di **Tests tab** pada request Login:

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
    console.log("Token saved:", jsonData.data.token);
}
```

#### Test Sequence

1. Register → Save token (auto)
2. Login → Save token (auto)
3. Get Products
4. Checkout → Save order_id
5. Create Payment → Copy invoice_url
6. Open invoice_url → Simulate payment
7. Get Orders → Verify status = paid

---

## 🌐 Setup Webhook (Local Development)

### Menggunakan Ngrok

#### 1. Install Ngrok

Download dari: https://ngrok.com/download

#### 2. Setup Authtoken

```bash
ngrok config add-authtoken YOUR_AUTHTOKEN
```

#### 3. Start Ngrok

```bash
# Start Laravel server
php artisan serve

# Start ngrok (terminal baru)
ngrok http 8000
```

Copy URL yang diberikan ngrok (contoh: `https://abc123.ngrok.io`)

#### 4. Update .env

```env
APP_URL=https://abc123.ngrok.io
```

Clear cache:
```bash
php artisan config:clear
```

#### 5. Setup di Xendit Dashboard

1. Login ke https://dashboard.xendit.co/ (Test Mode)
2. Go to **Settings → Webhooks**
3. Add Webhook URL: `https://abc123.ngrok.io/api/payment/webhook`
4. Select events:
   - ☑️ invoice.paid
   - ☑️ invoice.expired
5. Save

#### 6. Test Webhook

1. Create order via API
2. Create payment invoice
3. Open invoice_url di browser
4. Simulate payment di Xendit dashboard
5. Monitor webhook di ngrok dashboard: `http://localhost:4040`
6. Check order status berubah menjadi "paid"

### Monitor Webhook

```bash
# Terminal 1: Laravel log
tail -f storage/logs/laravel.log

# Terminal 2: Ngrok dashboard
# Open browser: http://localhost:4040
```

---

## 🚀 Deployment (VPS/Cloud)

### Requirements

- VPS dengan Ubuntu 20.04/22.04
- Domain (optional, bisa pakai IP)
- SSL Certificate (gunakan Let's Encrypt)

### Quick Deployment Steps

#### 1. Setup Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
```

#### 2. Deploy Application

```bash
# Clone repository
cd /var/www
git clone <repository-url> ecommerce-api
cd ecommerce-api

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup permissions
sudo chown -R www-data:www-data /var/www/ecommerce-api
sudo chmod -R 755 /var/www/ecommerce-api
sudo chmod -R 775 /var/www/ecommerce-api/storage
sudo chmod -R 775 /var/www/ecommerce-api/bootstrap/cache
```

#### 3. Configure Environment

```bash
# Copy .env
cp .env.example .env
nano .env
```

Update `.env` untuk production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=ecommerce_api
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

API_KEY=production-api-key-here
XENDIT_SECRET_KEY=xnd_production_xxxxx
```

#### 4. Run Migrations

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

#### 5. Configure Nginx

Create `/etc/nginx/sites-available/ecommerce-api`:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/ecommerce-api/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/ecommerce-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 6. Setup SSL

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

#### 7. Setup Webhook di Xendit

Update webhook URL di Xendit Dashboard ke:
```
https://yourdomain.com/api/payment/webhook
```

---

## 🐛 Troubleshooting

### Error: "Route [login] not defined"

**Solusi**: Update `app/Exceptions/Handler.php` sesuai dokumentasi di atas.

### Error: "Invalid or missing API key"

**Solusi**: 
1. Pastikan header `X-API-KEY` ada
2. Pastikan value sama dengan `.env`
3. Clear cache: `php artisan config:clear`

### Error: Invoice URL null

**Solusi**:
1. Cek Xendit API key valid
2. Cek `config('services.xendit.secret_key')` tidak null
3. Clear cache: `php artisan config:clear`
4. Pastikan Test Mode aktif di Xendit

### Error: "Payment not found" di webhook

**Solusi**:
1. Gunakan `external_id` yang benar dari database
2. Jangan pakai test webhook dari Xendit (pakai data real)

### Config tidak ter-load

**Solusi**:
1. Jangan pakai `env()` di controller, pakai `config()`
2. Clear cache: `php artisan config:clear`
3. Restart server

### Webhook tidak terima

**Solusi**:
1. Pastikan ngrok masih running
2. Update webhook URL di Xendit setiap ngrok restart
3. Cek webhook token benar
4. Monitor log: `tail -f storage/logs/laravel.log`

---

## 📝 Additional Notes

### Security Best Practices

- ✅ Gunakan HTTPS di production
- ✅ Jangan commit `.env` ke git
- ✅ Generate API key yang kuat (min 32 karakter)
- ✅ Rotate API key secara berkala
- ✅ Monitor logs untuk suspicious activity
- ✅ Keep dependencies updated

### Performance Tips

- Cache config di production: `php artisan config:cache`
- Cache routes: `php artisan route:cache`
- Use queue untuk background jobs
- Enable OPcache di production

### Backup Strategy

```bash
# Backup database
mysqldump -u username -p ecommerce_api > backup.sql

# Backup files
tar -czf ecommerce-api-backup.tar.gz /var/www/ecommerce-api
```

---

## 📞 Support & Resources

- Laravel Documentation: https://laravel.com/docs
- JWT Auth: https://jwt-auth.readthedocs.io/
- Xendit API: https://developers.xendit.co/
- Ngrok: https://ngrok.com/docs

---

## 📄 License

This project is for technical test purposes.

---

## 👨‍💻 Author

Created for technical test submission.

---

**Happy Coding! 🚀**
