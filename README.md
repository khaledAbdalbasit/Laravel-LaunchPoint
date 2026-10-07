# 🚀 LaunchPoint API Starter Kit

<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-11.x-red">
<img src="https://img.shields.io/badge/PHP-8.1%2B-blue">
<img src="https://img.shields.io/badge/License-MIT-green">
</p>

---

## 📌 Table of Contents

* [Introduction](#introduction)
* [Features](#features)
* [Installation](#installation)
    * [Install via Composer](#install-via-composer)
    * [Run LaunchPoint Installer](#run-launchpoint-installer)
    * [Installation Wizard](#installation-wizard)
* [LaunchPoint Artisan Commands](#launchpoint-artisan-commands)
    * [Make Controller](#1️⃣-make-controller)
    * [Make Service](#2️⃣-make-service)
    * [Make Repository](#3️⃣-make-repository)
    * [Make Request](#4️⃣-make-request)
    * [Make Resource](#5️⃣-make-resource)
* [Magic --all Flag](#-magic---all-flag)
* [Generated Project Structure](#generated-project-structure)
* [Example API Response](#example-api-response)
* [Requirements](#requirements)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Author](#author)

---

## Introduction

**LaunchPoint** is a powerful API starter kit for Laravel designed to accelerate backend development.

It provides an interactive scaffolding system that installs essential backend architecture components including:

* Authentication System (with OTP support)
* Service & Repository Layers
* FormRequest Validation
* API Resources (JSON transformation)
* File Helpers & API Response Traits

LaunchPoint helps developers **start building production-ready APIs within seconds instead of hours.**

---

## Features

* **🔥 Magic Scaffolding (`--all`)**: One command generates Controller, Service, Repository, FormRequest, and API Resource — all wired together with full CRUD.
* **⚡ Full CRUD Boilerplate**: Every generated class ships with working `index`, `show`, `store`, `update`, and `destroy` methods.
* **🏗️ Clean Architecture**: Enforces a strict `Request → Controller → Service → Repository → Model → Resource` chain.
* **✅ FormRequest Validation**: Auto-generates typed `FormRequest` classes with `$request->validated()` wiring.
* **📦 API Resources**: Auto-generates `JsonResource` classes for clean response transformation.
* Interactive installation wizard
* Automatic Laravel API setup
* Authentication system with OTP support (`fisal/laravel-otp`)
* File handling utilities & Standardized API responses
* Laravel 11+ ready

---

## Installation

### Install via Composer

```bash
composer require khaledabdalbasit/launchpoint
```

### Run LaunchPoint Installer

```bash
php artisan launchpoint:install
```

Launches the interactive installation wizard. Choose whether to install Authentication, FileHelper, ApiResponseTrait, etc.

### Installation Wizard

#### Step 1 — Ensure API Setup

Checks if `routes/api.php` exists. If not, runs automatically:

```bash
php artisan install:api
```

#### Step 2 — Authentication System

Installs:

* `AuthController`
* `LoginRequest` & `RegisterRequest`
* `AuthService` & `AuthRepository`
* OTP integration via `fisal/laravel-otp`
* `FileHelper`
* `ApiResponseTrait`

#### Step 3 — Optional Components

* **FileHelper only**
* **ApiResponseTrait only**

#### Step 4 — Publish Configuration

```bash
php artisan vendor:publish --tag=launchpoint-config
```

Publishes `config/launchpoint.php`.

---

## LaunchPoint Artisan Commands

LaunchPoint ships with powerful scaffolding generators that automatically write full CRUD boilerplate and link your architecture layers together.

---

### 1️⃣ Make Controller

Generates a new API Controller. Can be standalone, service-injected, or fully scaffolded.

```bash
php artisan launchpoint:make-controller {name} [options]
```

**Available Options:**

| Option | Description |
|---|---|
| `--service=ServiceName` | Inject a specific Service into the Controller constructor. Auto-generates the Service if it doesn't exist. |
| `--model=ModelName` | Associate a Model (used alongside `--service` or `--all`). |
| `--request=RequestName` | Inject a specific FormRequest for `store` & `update`. Auto-generates it if it doesn't exist. |
| `--resource=ResourceName` | Wrap responses in a specific API Resource. Auto-generates it if it doesn't exist. |
| `--all` / `--a` 🔥 | Derives all names from the base and generates the full stack automatically. |

**Examples:**

```bash
# Basic Controller
php artisan launchpoint:make-controller ProductController

# Controller with a specific service
php artisan launchpoint:make-controller ProductController --service=ProductService

# Full magic: generates Controller + Service + Repository + Request + Resource
php artisan launchpoint:make-controller Product --all
```

---

### 2️⃣ Make Service

Generates a Service class to house your business logic.

```bash
php artisan launchpoint:make-service {name} [options]
```

**Available Options:**

| Option | Description |
|---|---|
| `--model=ModelName` | Generates the Service pre-wired to an auto-created Repository with full CRUD methods. |

**Examples:**

```bash
# Basic empty Service
php artisan launchpoint:make-service PaymentService

# Generates ProductService AND ProductRepository, linked together
php artisan launchpoint:make-service ProductService --model=Product
```

---

### 3️⃣ Make Repository

Generates a Repository class to handle all database operations.

```bash
php artisan launchpoint:make-repository {name} [options]
```

**Available Options:**

| Option | Description |
|---|---|
| `--model=ModelName` | Generates the Repository with fully implemented CRUD methods (`all`, `findOrFail`, `create`, `update`, `delete`) for the given Model. |

**Examples:**

```bash
# Basic empty Repository
php artisan launchpoint:make-repository ReportRepository

# Full CRUD Repository for the Order model
php artisan launchpoint:make-repository OrderRepository --model=Order
```

---

### 4️⃣ Make Request

Generates a `FormRequest` class with `authorize()` and `rules()` methods ready to fill.

```bash
php artisan launchpoint:make-request {name}
```

> Automatically appends `Request` suffix if not provided.

**Examples:**

```bash
php artisan launchpoint:make-request StoreProductRequest
# → app/Http/Requests/StoreProductRequest.php

php artisan launchpoint:make-request Product
# → app/Http/Requests/ProductRequest.php
```

**Generated output:**

```php
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
```

---

### 5️⃣ Make Resource

Generates a `JsonResource` class for clean API response transformation.

```bash
php artisan launchpoint:make-resource {name}
```

> Automatically appends `Resource` suffix if not provided.

**Examples:**

```bash
php artisan launchpoint:make-resource ProductResource
# → app/Http/Resources/ProductResource.php

php artisan launchpoint:make-resource Product
# → app/Http/Resources/ProductResource.php
```

**Generated output:**

```php
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
```

---

## 🔥 Magic `--all` Flag

The most powerful feature of LaunchPoint. One single command scaffolds your **entire feature stack**:

```bash
php artisan launchpoint:make-controller Product --all
```

**What gets generated:**

```
app/
├── Http/
│   ├── Controllers/ProductController.php   ← Full CRUD, uses Request + Resource
│   ├── Requests/ProductRequest.php         ← $request->validated() in store/update
│   └── Resources/ProductResource.php       ← Wraps all responses
├── Services/
│   └── ProductService.php                  ← Delegates to Repository
└── Repositories/
    └── ProductRepository.php               ← Full CRUD for Product model
```

**The generated chain:**

```
ProductRequest → ProductController → ProductService → ProductRepository → Product model
                                                                        ↓
                                                              ProductResource (response)
```

**Controller response examples:**

```php
// index  → ResourceCollection
return $this->apiResponse(['data' => ProductResource::collection($data)]);

// show   → single Resource
return $this->apiResponse(['data' => new ProductResource($data)]);

// store  → validated() + Resource
public function store(ProductRequest $request) { ... }

// update → validated() + Resource
public function update(ProductRequest $request, $id) { ... }
```

---

## Generated Project Structure

After running `launchpoint:install` + `launchpoint:make-controller Product --all`:

```
app/
├── Helpers/
│   └── FileHelper.php
│
├── Traits/
│   └── ApiResponseTrait.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   └── ProductController.php
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── LoginRequest.php
│   │   │   └── RegisterRequest.php
│   │   └── ProductRequest.php
│   │
│   └── Resources/
│       └── ProductResource.php
│
├── Services/
│   ├── Auth/
│   │   └── AuthService.php
│   └── ProductService.php
│
└── Repositories/
    ├── Auth/
    │   └── AuthRepository.php
    └── ProductRepository.php
```

---

## Example API Response

Using `ApiResponseTrait`:

```php
return $this->apiResponse(['data' => new ProductResource($product), 'message' => 'Created!', 'code' => 201]);
```

Response:

```json
{
    "status": true,
    "message": "Created!",
    "data": {
        "id": 1,
        "name": "Example Product"
    }
}
```

---

## Requirements

* PHP 8.1+
* Laravel 11+

---

## Roadmap

* [x] Repository scaffolding generator
* [x] Service generator
* [x] API Resource generator
* [x] FormRequest generator
* [x] Magic `--all` full-stack scaffold
* [ ] Role & Permission scaffolding
* [ ] API versioning support
* [ ] Swagger / OpenAPI documentation generator
* [ ] PHPUnit test generation

---

## Contributing

* Fork the repository
* Create a feature branch
* Commit your changes
* Open a Pull Request

---

## License

MIT License © Khaled Abdelbasit

---

## Author

Khaled Abdelbasit
Backend Engineer specializing in Laravel architecture and API systems.
