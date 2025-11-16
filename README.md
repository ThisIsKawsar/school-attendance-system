# School Attendance System

A comprehensive **School Attendance Management System** built with **Laravel 12** and **Vue.js 3**.

This application provides an efficient way to manage student attendance, generate reports, and monitor real-time statistics through a modern dashboard.

---

## Features

- **Student Management** (CRUD operations)
- **Bulk Attendance Recording** (Mark attendance for entire class in one go)
- **Monthly Attendance Reports** (PDF & Excel export)
- **Real-time Dashboard** with interactive charts
- **Redis Caching** for high performance
- **RESTful API** with **Laravel Sanctum** authentication
- Role-based access (Admin, Teacher)

---

## Requirements

| Dependency       | Version       |
|------------------|---------------|
| PHP              | `>= 8.2`      |
| Laravel          | `12.x`        |
| MySQL            | `>= 8.0`      |
| Redis            | Latest        |
| Node.js          | `>= 20.x`     |
| Composer         | Latest        |

---

## Installation

Clone the Repository

```bash
git clone <your-repository-url>
cd school-attendance-system
```

Install PHP dependencies:

```bash
composer install
```
Install JavaScript dependencies:

```bash
npm install
```
Copy environment file:

```bash
cp .env.example .env
```
Generate application key:

```bash
php artisan key:generate
```
Configure database in .env:

env
```bash
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
```
Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```
Build frontend assets:

```bash
npm run build
```
Start the development server:

```bash
php artisan serve
```
Testing
Run the test suite:

```bash
php artisan test
```
Artisan Commands
Generate monthly attendance report:

```bash
php artisan attendance:generate-report 2024-01
php artisan attendance:generate-report 2024-01 "10"
```
API Authentication
The API uses Laravel Sanctum. Include the token in requests:

http
Authorization: Bearer {api_token}
Default Credentials
After seeding:

Email: admin@school.com

Password: password
