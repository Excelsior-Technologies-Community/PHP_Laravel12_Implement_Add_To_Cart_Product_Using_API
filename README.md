# PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API


## Introduction

PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API is a Laravel 12 project that demonstrates a real-world Add To Cart system using APIs.

This project is designed with industry-standard database fields such as:

status

created_by

updated_by

deleted_at (Soft Delete)

These fields are commonly used in professional eCommerce and enterprise applications, making this project interview-ready and production-oriented.


---


## Project Features


* Product management

* Add product to cart

* Update cart quantity

* Remove cart items

* Clear entire cart

* Status-based records

* Soft delete support

* API-based architecture

* Clean Laravel 12 structure


---


## Technologies Used


* Laravel 12

* MySQL

* PHP 8+

* REST APIs

* JSON responses

---


##  Project Structure

```
PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API
│
├── app
│   ├── Http
│   │   └── Controllers
│   │       ├── ProductController.php
│   │       └── CartController.php
│   │
│   └── Models
│       ├── Product.php
│       └── Cart.php
│
├── bootstrap
│   └── app.php
│
├── database
│   └── migrations
│       ├── create_products_table.php
│       └── create_carts_table.php
│
├── routes
│   └── api.php
│
├── .env
└── README.md
```

---


## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API "12.*"

cd PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API
```

---


## Step 2: Database Configuration

.env

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=add_to_cart_api
DB_USERNAME=root
DB_PASSWORD=
```

Create database add_to_cart_api using this command:

```bash
php artisan migrate
```

---


## Step 3: Create Models & Migrations

```bash
php artisan make:model Product -m
php artisan make:model Cart -m
```

---


## Step 4: Database Tables (With Professional Fields)

### Products Table

File: database/migrations/2026_01_02_000001_create_products_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Carts Table

File: database/migrations/2026_01_02_000002_create_carts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
```


Run migrations:

```
php artisan migrate
```

---


## Step 5: Models 


### Product.php

File: app/Models/Product.php


```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'created_by',
        'updated_by'
    ];
}
```

### Cart.php

File: app/Models/Cart.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'status',
        'created_by',
        'updated_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

---


## Step 6: Controllers

```bash
php artisan make:controller ProductController
php artisan make:controller CartController
```


### ProductController.php

File: app/Http/Controllers/ProductController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * List all active products
     */
    public function listProducts()
    {
        $products = Product::where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($products);
    }

    /**
     * Add new product
     */
    public function addProduct(Request $request)
    {
        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'status'      => 1,
            'created_by'  => 1
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'data'    => $product
        ]);
    }

     /**
     * Update existing product
     */
    public function updateProduct(Request $request)
    {
        // Find the product by ID from request body
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Update product details
        $product->name        = $request->name ?? $product->name;
        $product->description = $request->description ?? $product->description;
        $product->price       = $request->price ?? $product->price;
        $product->updated_by  = 1;
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data'    => $product
        ]);
    }

    /**
     * Soft delete product & update status
     */
    public function deleteProduct(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->status = 0;
        $product->updated_by = 1;
        $product->save();
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
```


### CartController.php

File: app/Http/Controllers/CartController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1; // default = 1

        // Get product
        $product = Product::where('id', $request->product_id)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not available'], 404);
        }

        // Check existing cart
        $cart = Cart::where('product_id', $product->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if ($cart) {
            // Increase quantity dynamically
            $cart->quantity += $quantity;
            $cart->price = $product->price; // keep latest price
            $cart->updated_by = 1;
            $cart->save();
        } else {
            // Add new cart row
            Cart::create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $product->price,
                'status'     => 1,
                'created_by' => 1
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }


    /**
     * View cart
     */
    public function viewCart()
    {
        return response()->json(
            Cart::with('product')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get()
        );
    }

    /**
     * Update cart quantity (POST)
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_id'  => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::findOrFail($request->cart_id);

        $cart->quantity   = $request->quantity;
        $cart->updated_by = 1;
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully']);
    }

    /**
     * Remove cart item (Soft delete + status=0)
     */
    public function removeFromCart(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);

        $cart->status = 0;
        $cart->updated_by = 1;
        $cart->save();

        $cart->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        Cart::where('status', 1)->get()->each(function ($cart) {
            $cart->status = 0;
            $cart->updated_by = 1;
            $cart->save();
            $cart->delete();
        });

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
```

---


## Step 7: API Routes

File: routes/api.php

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
Route::get('/listProducts', [ProductController::class, 'listProducts']);
Route::post('/products/add', [ProductController::class, 'addProduct']);
Route::post('/products/update', [ProductController::class, 'updateProduct']);
Route::post('/products/delete', [ProductController::class, 'deleteProduct']);

/*
|--------------------------------------------------------------------------
| Cart Routes (GET & POST Only)
|--------------------------------------------------------------------------
*/
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart/view', [CartController::class, 'viewCart']);

Route::post('/cart/update', [CartController::class, 'updateCart']);
Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
Route::post('/cart/clear', [CartController::class, 'clearCart']);
```

---

## Step 8: Update app.php

File: bootstrap/app.php

```php
<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---


## Step 9: Run Project

```bash
php artisan serve
```

---

##  Step 10: PRODUCT & Cart APIs Testing Example (POSTMAN)

### BASE URL

```
http://127.0.0.1:8000/api
```

### Headers for ALL POST requests:

```
Accept: application/json
Content-Type: application/json
```

**ADD PRODUCT**

Endpoint

```
POST /api/product/add
```

Full URL

```
http://127.0.0.1:8000/api/products/add
```

Headers

```
Accept: application/json
Content-Type: application/json
```

Body (JSON)

```
{
  "name": "iPhone 15 Pro",
  "description": "Apple mobile phone",
  "price": 89999
}
```

Response

```
{
  "message": "Product added successfully",
  "data": {
    "id": 1,
    "name": "iPhone 15",
    "description": "Apple mobile phone",
    "price": "89999.00",
    "status": 1
  }
}
```

**LIST PRODUCTS**

Endpoint

```
GET /listProducts
```

Full URL

```
http://127.0.0.1:8000/api/listProducts
```

Response

```
[
  {
    "id": 1,
    "name": "iPhone 15",
    "description": "Apple mobile phone",
    "price": "89999.00",
    "status": 1
  }
]
```

* Only status = 1 products
* Soft-deleted products are hidden



**UPDATE PRODUCT**

Endpoint

```
POST /products/update
```

Full URL

```
http://127.0.0.1:8000/api/products/update
```

Body

```
{
  "product_id": 1,
  "name": "iPhone 15 Pro",
  "price": 79999
}
```

Response

```
{
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro",
    "price": "79999.00"
  }
}
```

* Product price updated


**DELETE PRODUCT (SOFT DELETE)**

Endpoint

```
POST /products/delete
```

Body

```
{
  "product_id": 1
}
```

Response

```
{
  "message": "Product deleted successfully"
}
```


### CART APIs (POSTMAN)


**ADD PRODUCT  WITH MULTIPLE QUANTITY (DYNAMIC WAY**)

Endpoint

```
POST /cart/add
```

Full URL

```
http://127.0.0.1:8000/api/cart/add
```

Body

```
{
  "product_id": 1,
  "quantity": 3
}
```

Response

```
{
  "message": "Product added to cart successfully"
}
```

VIEW CART

Endpoint

```
GET /cart/view
```

Full URL

```
http://127.0.0.1:8000/api/cart/view
```

Response

```
[
  {
    "id": 1,
    "product_id": 1,
    "quantity": 3,
    "price": "79999.00",
    "status": 1,
    "product": {
      "id": 1,
      "name": "iPhone 15 Pro",
      "price": "79999.00"
    }
  }
]
```

**UPDATE CART QUANTITY ( + / − BUTTON )**

Endpoint

```
POST /cart/update
```

Body

```
{
  "cart_id": 1,
  "quantity": 2
}
```

Response

```
{
  "message": "Cart updated successfully"
}
```

**REMOVE SINGLE CART ITEM**

Endpoint

```
POST /cart/remove
```

Body

```
{
  "cart_id": 1
}
```

Response

```
{
  "message": "Cart item removed"
}
```

* status = 0
* soft deleted


**CLEAR FULL CART**

Endpoint

```
POST /cart/clear
```

Response

```
{
  "message": "Cart cleared successfully"
}
```

* All active cart rows cleared
* Soft delete applied


---

##  Custom Authentication & Customer-Based Cart (Concept)

> This step explains how the project can be extended.  
> Actual authentication implementation is optional for learning purposes.

In real-world applications, a cart is **never global**.  
Each cart always belongs to a **specific customer or user**.

### Why Custom Authentication is Important

In professional systems:

- Each user has **their own cart**
- Cart records are linked using **customer_id**
- APIs are protected using authentication

This project is intentionally designed so authentication can be added later **without changing existing logic**.

---


## Output

**PRODUCT ADD TO CART**

<img width="1382" height="998" alt="Screenshot 2026-01-02 103036" src="https://github.com/user-attachments/assets/31f837e7-bcce-434f-aa21-2d6ea4f08019" />


**CART VIEW**

<img width="1365" height="1006" alt="Screenshot 2026-01-02 103119" src="https://github.com/user-attachments/assets/41b1f3a9-d850-46ce-92d1-aab25a10c105" />


---

Your PHP_Laravel12_Implement_Add_To_Cart_Product_Using_API Project is Now Ready!
 
