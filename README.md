# LaunchPoint API Starter Kit

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-blue">
  <img src="https://img.shields.io/badge/License-MIT-green">
</p>

---

## Table of Contents

- [Introduction](#introduction)
- [Architecture Overview](#architecture-overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Interactive Wizard](#interactive-wizard)
- [Artisan Commands Reference](#artisan-commands-reference)
  - [Setup & System Commands](#setup--system-commands)
  - [Architectural Scaffolding Commands](#architectural-scaffolding-commands)
  - [Data & Validation Generators](#data--validation-generators)
  - [Domain & Logic Generators](#domain--logic-generators)
- [Core Utilities](#core-utilities)
  - [ApiResponseTrait](#apiresponsetrait)
  - [FileHelper](#filehelper)
- [Auth Scaffolding System](#auth-scaffolding-system)
- [Generated Folder Structure](#generated-folder-structure)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)

---

## Introduction

**LaunchPoint** is a complete API starter kit and code generator suite for Laravel applications. It accelerates backend development by adhering to a strict **Clean Architecture** design pattern (`Controller -> Service -> Repository -> Model`).

LaunchPoint provides 15 dedicated Artisan generators that auto-wire architectural layers, generate full CRUD boilerplate, and scaffold essential API components in seconds.

---

## Architecture Overview

LaunchPoint enforces a layered design pattern for clear separation of concerns:

```
[ HTTP Request ]
       │
       ▼
┌──────────────┐
│  Controller  │  Handles HTTP requests, validation, and formats JSON responses.
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Service    │  Contains core business rules and orchestrates data processing.
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Repository  │  Encapsulates Eloquent database queries and handles data persistence.
└──────┬───────┘
       │
       ▼
┌──────────────┐
│    Model     │  Eloquent ORM representation of database entities.
└──────────────┘
```

---

## Features

- **Full Architectural Generators**: Generate Controllers, Services, Repositories, Models, Requests, Resources, Enums, Actions, and more.
- **Magic Scaffolding (`--all`)**: Auto-generate Controller, Service, and Repository layers simultaneously with full CRUD operations wired up.
- **Working CRUD Boilerplate**: Scaffolds complete implementations (`index`, `show`, `store`, `update`, `destroy`) with exception handling.
- **System Health Diagnostics**: Built-in health check command (`launchpoint:health`) to verify environment, database, and folder setup.
- **Interactive Wizard**: Interactive installer command allowing selective installation of Auth scaffolding, File helpers, and Response traits.
- **Standardized API Responses**: Built-in `ApiResponseTrait` supporting data payload, pagination meta, custom status codes, and error formatting.
- **File Helper Utility**: Static `FileHelper` for file upload, replacement, and deletion.
- **Authentication Scaffolding**: Production-ready Auth suite with Controllers, Services, Repositories, Form Requests, and OTP integration.

---

## Requirements

- **PHP**: ^8.1 or ^8.2 or ^8.3
- **Laravel Framework**: ^11.0 or ^12.0

---

## Installation

### 1. Install via Composer

```bash
composer require khaledabdalbasit/launchpoint
```

### 2. Run the Interactive Installer

```bash
php artisan launchpoint:install
```

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=launchpoint-config
```

---

## Interactive Wizard

When executing `php artisan launchpoint:install`, the wizard performs the following:

1. **API Route Verification**: Checks if `routes/api.php` exists. If missing, automatically executes `php artisan install:api`.
2. **Authentication Scaffolding**: Prompts whether to install full Auth scaffolding (Controllers, Services, Repositories, Requests, and OTP validation).
3. **Selective Components**: If full Auth is skipped, allows individual installation of `FileHelper` or `ApiResponseTrait`.
4. **Configuration Publishing**: Automatically publishes `config/launchpoint.php`.

---

## Artisan Commands Reference

LaunchPoint includes 15 commands designed to cover every layer of backend application development.

```bash
# View all available LaunchPoint commands and options
php artisan launchpoint:list
```

---

### Setup & System Commands

#### 1. launchpoint:install

Launches the interactive installation wizard to setup package components.

```bash
php artisan launchpoint:install
```

#### 2. launchpoint:list

Displays a formatted table of all available LaunchPoint commands, signatures, and options.

```bash
php artisan launchpoint:list
```

#### 3. launchpoint:health

Performs project health diagnostics, testing database connectivity, `.env` key configurations, and required storage/app directory paths.

```bash
php artisan launchpoint:health
```

---

### Architectural Scaffolding Commands

#### 4. launchpoint:make-controller

Generates an API Controller class in `app/Http/Controllers/`.

```bash
php artisan launchpoint:make-controller {name} [options]
```

- `--service=ServiceName`: Injects a specific Service class. Auto-generates the Service if missing.
- `--model=ModelName`: Specifies the associated Eloquent Model.
- `--all` or `-a`: Derives Service and Model names from the Controller name, generating Controller, Service, and Repository layers with working CRUD methods.

```bash
# Generate complete architectural stack in one command
php artisan launchpoint:make-controller ProductController --all
```

#### 5. launchpoint:make-service

Generates a Service class in `app/Services/`.

```bash
php artisan launchpoint:make-service {name} [options]
```

- `--model=ModelName`: Auto-generates the matching Repository (e.g. `ProductRepository`), injects it into the Service constructor, and delegates CRUD methods (`all`, `findOrFail`, `create`, `update`, `delete`).

```bash
php artisan launchpoint:make-service ProductService --model=Product
```

#### 6. launchpoint:make-repository

Generates a Repository class in `app/Repositories/`.

```bash
php artisan launchpoint:make-repository {name} [options]
```

- `--model=ModelName`: Equips the Repository with complete CRUD operations and database exception handling (`QueryException`, `ModelNotFoundException`).

```bash
php artisan launchpoint:make-repository ProductRepository --model=Product
```

#### 7. launchpoint:make-model

Generates an Eloquent Model in `app/Models/` with a pre-configured `$fillable` array.

```bash
php artisan launchpoint:make-model {name} [options]
```

- `--fillable=col1,col2`: Specifies fillable attributes.
- `--migration` or `-m`: Creates matching database migration file.
- `--factory` or `-f`: Creates model factory.
- `--seeder` or `-s`: Creates database seeder.
- `--all` or `-a`: Creates model, migration, factory, and seeder together.

```bash
php artisan launchpoint:make-model Product --fillable=title,price,stock --all
```

---

### Data & Validation Generators

#### 8. launchpoint:make-request

Generates a FormRequest class in `app/Http/Requests/` with structured `authorize()`, `rules()`, and `messages()` methods.

```bash
php artisan launchpoint:make-request {name} [options]
```

- `--model=ModelName`: Automatically generates suggested validation rules based on model name context.

```bash
php artisan launchpoint:make-request StoreProductRequest --model=Product
```

#### 9. launchpoint:make-resource

Generates an API Resource in `app/Http/Resources/`.

```bash
php artisan launchpoint:make-resource {name} [options]
```

- `--collection` or `-c`: Creates a matching `ResourceCollection` class alongside the Resource.

```bash
php artisan launchpoint:make-resource ProductResource --collection
```

#### 10. launchpoint:make-enum

Generates a PHP 8.1+ Backed Enum in `app/Enums/` with `label()` and option array helpers.

```bash
php artisan launchpoint:make-enum {name} [options]
```

- `--cases=case1,case2`: Defines enum cases directly from the command line.

```bash
php artisan launchpoint:make-enum OrderStatus --cases=pending,processing,completed,cancelled
```

---

### Domain & Logic Generators

#### 11. launchpoint:make-action

Generates a Single Action class in `app/Actions/` containing a `handle()` method.

```bash
php artisan launchpoint:make-action CreateOrderAction
```

#### 12. launchpoint:make-trait

Generates a custom Trait in `app/Traits/`.

```bash
php artisan launchpoint:make-trait HasUuid
```

#### 13. launchpoint:make-exception

Generates a custom Exception class in `app/Exceptions/` containing a `render()` method that returns a formatted JSON API response.

```bash
php artisan launchpoint:make-exception PaymentFailedException
```

#### 14. launchpoint:make-filter

Generates an Eloquent Query Filter class in `app/Filters/` for clean request filtering.

```bash
php artisan launchpoint:make-filter ProductFilter
```

#### 15. launchpoint:make-scope

Generates an Eloquent Local Scope class in `app/Scopes/`.

```bash
php artisan launchpoint:make-scope ActiveScope
```

---

## Core Utilities

### ApiResponseTrait

Standardizes JSON response structures across all API endpoints.

#### Scaffolded Path:
`app/Traits/ApiResponseTrait.php`

#### Available Methods:

- `apiResponse($data, $message, $status, $errors)`
- `successResponse($data, $message, $status)`
- `errorResponse($message, $status, $errors)`

#### Example Response Output:

```json
{
    "status": 200,
    "message": "Data retrieved successfully.",
    "data": {
        "id": 1,
        "name": "Sample Product"
    }
}
```

---

### FileHelper

Provides static methods for handling file uploads, updates, and removals in public storage.

#### Scaffolded Path:
`app/Helpers/FileHelper.php`

#### Usage Examples:

```php
use App\Helpers\FileHelper;

// Upload file to storage
$path = FileHelper::upload($request->file('image'), 'products');

// Replace existing file
$newPath = FileHelper::update($request->file('image'), $oldPath, 'products');

// Delete file
FileHelper::delete($filePath);
```

---

## Auth Scaffolding System

When installing the Auth suite via `launchpoint:install`, LaunchPoint generates the following production-ready components:

- **Controller**: `app/Http/Controllers/Api/Auth/AuthController.php`
- **Service**: `app/Services/Api/Auth/AuthService.php`
- **Repository**: `app/Repositories/Api/Auth/AuthRepository.php`
- **Requests**: 
  - `app/Http/Requests/Auth/LoginRequest.php`
  - `app/Http/Requests/Auth/RegisterRequest.php`
- **Supported Operations**: User registration, login, Sanctum token generation, OTP generation & verification, OTP resend, and logout.

---

## Generated Folder Structure

```
app/
├── Actions/
│   └── CreateOrderAction.php
├── Enums/
│   └── OrderStatus.php
├── Exceptions/
│   └── PaymentFailedException.php
├── Filters/
│   └── ProductFilter.php
├── Helpers/
│   └── FileHelper.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/Auth/AuthController.php
│   │   └── ProductController.php
│   ├── Requests/
│   │   └── Auth/
│   │       ├── LoginRequest.php
│   │       └── RegisterRequest.php
│   └── Resources/
│       ├── ProductResource.php
│       └── ProductCollection.php
├── Models/
│   └── Product.php
├── Repositories/
│   ├── Api/Auth/AuthRepository.php
│   └── ProductRepository.php
├── Scopes/
│   └── ActiveScope.php
├── Services/
│   ├── Api/Auth/AuthService.php
│   └── ProductService.php
└── Traits/
    ├── ApiResponseTrait.php
    └── HasUuid.php
```

---

## Configuration

Published to `config/launchpoint.php`:

```php
return [
    'api_prefix' => 'api',
    'default_response_code' => 200,
];
```

---

## Contributing

Contributions and pull requests are welcome!

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## License

Open-sourced software licensed under the [MIT license](LICENSE).

---

## Author

**Khaled Abdelbasit**
Backend Engineer specializing in Laravel architecture and API systems.
