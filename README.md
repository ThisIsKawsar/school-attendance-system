# School Attendance System

A comprehensive school attendance management system built with Laravel and Vue.js.

## Features

- Student Management (CRUD operations)
- Bulk Attendance Recording
- Monthly Reports Generation
- Real-time Dashboard with Charts
- Redis Caching for Performance
- RESTful API with Sanctum Authentication

## Requirements

- PHP 8.2+
- Laravel 12
- MySQL 8.0+
- Redis
- Node.js 20+
- Composer

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd school-attendance-system

Install PHP dependencies:

```bash
composer install
Install JavaScript dependencies:

```bash
npm install
Copy environment file:

```bash
cp .env.example .env
Generate application key:

```bash
php artisan key:generate
Configure database in .env:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_attendance
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
Build frontend assets:

```bash
npm run build
Start the development server:

```bash
php artisan serve
Testing
Run the test suite:

```bash
php artisan test
Artisan Commands
Generate monthly attendance report:

```bash
php artisan attendance:generate-report 2024-01
php artisan attendance:generate-report 2024-01 "10"
API Authentication
The API uses Laravel Sanctum. Include the token in requests:

http
Authorization: Bearer {api_token}
Default Credentials
After seeding:

Email: admin@school.com

Password: password