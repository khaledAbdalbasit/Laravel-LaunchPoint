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

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
    - [Install via Composer](#install-via-composer)
    - [Run LaunchPoint Installer](#run-launchpoint-installer)
    - [Installation Wizard](#installation-wizard)
        - [Step 1 — Ensure API Setup](#step-1-ensure-api-setup)
        - [Step 2 — Authentication System](#step-2-authentication-system)
        - [Step 3 — Optional Components](#step-3-optional-components)
        - [Step 4 — Publish Configuration](#step-4-publish-configuration)
- [Example Installation](#example-installation)
- [Generated Project Structure](#generated-project-structure)
- [Example API Response](#example-api-response)
- [Requirements](#requirements)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)

---

## Introduction

**LaunchPoint** is a powerful API starter kit for Laravel designed to accelerate backend development.

It provides an interactive scaffolding system that installs essential backend architecture components including:

- Authentication System
- Service Layer
- Repository Layer
- File Helpers
- API Response Traits

LaunchPoint helps developers **start building production-ready APIs within seconds instead of hours.**

---

## Features

- Interactive installation wizard
- Automatic Laravel API setup
- Clean architecture scaffolding
- Authentication system with OTP support
- File handling utilities
- Standardized API responses
- Modular installation
- Laravel 11+ ready

---

## Installation

### Install via Composer

```bash
composer require khaledabdalbasit/launchpoint
Run LaunchPoint Installer
php artisan launchpoint:install

Launches the interactive installation wizard.

Choose whether to install Authentication, FileHelper, ApiResponseTrait, etc.

Installation Wizard Steps
Step 1 — Ensure API Setup

Checks if routes/api.php exists.

If not, runs automatically:

php artisan install:api
Step 2 — Authentication System

Installs:

AuthController

LoginRequest

RegisterRequest

AuthService

UserResource

OTP integration

FileHelper

ApiResponseTrait

Step 3 — Optional Components

FileHelper:

php artisan launchpoint:install-filehelper

ApiResponseTrait:

php artisan launchpoint:install-apiresponse
Step 4 — Publish Configuration
php artisan vendor:publish --tag=launchpoint-config

Publishes config/launchpoint.php.

LaunchPoint Artisan Commands
1️⃣ Make Controller
php artisan launchpoint:make-controller {name} {--service=ServiceName}

Creates a controller, optionally connected to a Service.

Example:

php artisan launchpoint:make-controller UserController --service=UserService
2️⃣ Make Service
php artisan launchpoint:make-service {name} {--model=ModelName}

Creates a service, optionally tied to a Model.

Example:

php artisan launchpoint:make-service UserService --model=User
3️⃣ Make Repository
php artisan launchpoint:make-repository {name} {--model=ModelName}

Creates a repository, optionally tied to a Model.

Example:

php artisan launchpoint:make-repository UserRepository --model=User
Example Installation
php artisan launchpoint:install

LaunchPoint Installation Wizard

Do you want to install the Authentication Scaffolding? (yes/no) [yes]:
✔ Auth system installed

Installation completed successfully.
Generated Project Structure
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
Example API Response

Using ApiResponseTrait:

return $this->apiResponse($data, 'User logged in successfully');

Response:

{
    "status": true,
    "message": "User logged in successfully",
    "data": {
        "user": {}
    }
}
Requirements

PHP 8.1+

Laravel 11+

Roadmap

Repository scaffolding generator

Service generator

API resource generator

Role & Permission scaffolding

API versioning support

Swagger documentation generator

Contributing

Fork the repository

Create a feature branch

Commit your changes

Open a Pull Request

License

MIT License © Khaled Abdelbasit

Author

Khaled Abdelbasit
Backend Engineer specializing in Laravel architecture and API systems.
