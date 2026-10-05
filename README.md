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

        * [Step 1 — Ensure API Setup](#step-1-ensure-api-setup)
        * [Step 2 — Authentication System](#step-2-authentication-system)
        * [Step 3 — Optional Components](#step-3-optional-components)
        * [Step 4 — Publish Configuration](#step-4-publish-configuration)
* [LaunchPoint Artisan Commands](#launchpoint-artisan-commands)
* [Example Installation](#example-installation)
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

* Authentication System
* Service Layer
* Repository Layer
* File Helpers
* API Response Traits

LaunchPoint helps developers **start building production-ready APIs within seconds instead of hours.**

---

## Features

* **🔥 Magic Scaffolding (`--all`)**: Generate Controller, Service, and Repository layers in one command.
* **⚡ Full CRUD Boilerplate**: Generated classes come with fully working CRUD code (index, show, store, update, destroy).
* **🏗️ Clean Architecture**: Enforces a strict `Controller -> Service -> Repository -> Model` chain.
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

Launches the interactive installation wizard.  Choose whether to install Authentication, FileHelper, ApiResponseTrait, etc.

### Installation Wizard

#### Step 1 — Ensure API Setup

Checks if `routes/api.php` exists. If not, runs automatically:

```bash
php artisan install:api
```

#### Step 2 — Authentication System

Installs:

* `AuthController`
* `LoginRequest`
* `RegisterRequest`
* `AuthService`
* `UserResource`
* OTP integration
* `FileHelper`
* `ApiResponseTrait`

#### Step 3 — Optional Components

* **FileHelper**

```bash
php artisan launchpoint:install-filehelper
```

* **ApiResponseTrait**

```bash
php artisan launchpoint:install-apiresponse
```

#### Step 4 — Publish Configuration

```bash
php artisan vendor:publish --tag=launchpoint-config
```

Publishes `config/launchpoint.php`.

---

## LaunchPoint Artisan Commands

LaunchPoint comes with powerful scaffolding generators that automatically write full CRUD boilerplate and link your architecture layers together.

1️⃣ **Make Controller**

```bash
php artisan launchpoint:make-controller {name} {--service=ServiceName} {--model=ModelName} {--all|-a}
```

Creates a controller. 
- **`--service`**: Automatically injects the specified service into the controller.
- **`--all` or `-a` (🔥 Magic Flag)**: This is the most powerful command. It automatically derives and generates the Controller, Service, and Repository, linking them all together with full CRUD operations ready to go!

Example:
```bash
php artisan launchpoint:make-controller UserController --all
```
*(This single command generates `UserController`, `UserService`, and `UserRepository` with full CRUD boilerplate!)*

2️⃣ **Make Service**

```bash
php artisan launchpoint:make-service {name} {--model=ModelName}
```

Creates a service class. 
- **`--model`**: When provided, LaunchPoint will **automatically generate the matching Repository** for this model, and set up the Service to delegate all CRUD operations to that Repository.

Example:
```bash
php artisan launchpoint:make-service UserService --model=User
```

3️⃣ **Make Repository**

```bash
php artisan launchpoint:make-repository {name} {--model=ModelName}
```

Creates a repository pattern class.
- **`--model`**: Generates full CRUD methods (all, findOrFail, create, update, delete) tailored to the specified model.

Example:
```bash
php artisan launchpoint:make-repository UserRepository --model=User
```

---

## Example Installation

```bash
php artisan launchpoint:install
```

LaunchPoint Installation Wizard:

```
Do you want to install the Authentication Scaffolding? (yes/no) [yes]:
✔ Auth system installed
Installation completed successfully.
```

---

## Generated Project Structure

```
app
 ├── Helpers
 │   └── FileHelper.php
 │
 ├── Traits
 │   └── ApiResponseTrait.php
 │
 ├── Services
 │   └── Auth
 │        └── AuthService.php
 │
 └── Http
     ├── Controllers
     │    └── Auth
     │         └── AuthController.php
     │
     └── Requests
          └── Auth
               ├── LoginRequest.php
               └── RegisterRequest.php
Repositories/
 └── ExampleRepository.php
```

---

## Example API Response

Using `ApiResponseTrait`:

```php
return $this->apiResponse($data, 'User logged in successfully');
```

Response:

```json
{
    "status": true,
    "message": "User logged in successfully",
    "data": {
        "user": {}
    }
}
```

---

## Requirements

* PHP 8.1+
* Laravel 11+

---

## Roadmap

* Repository scaffolding generator
* Service generator
* API resource generator
* Role & Permission scaffolding
* API versioning support
* Swagger documentation generator

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
