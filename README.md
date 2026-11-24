# PHP Login Application

A modular, testable user authentication system built from scratch using **procedural PHP**, **MySQLi prepared statements**, and **Bootstrap**.

**Live Demo:** [https://ajlato-login.up.railway.app](https://ajlato-login.up.railway.app)

## Architecture

```text
login/
├── includes/
│   ├── config.php        # Environment-driven MySQL configuration
│   ├── db.php            # Procedural MySQLi connection & prepared statement helpers
│   ├── session.php       # Session cookie hardening, auth state, and flash helpers
│   ├── csrf.php          # CSRF token generation, rotation, and hash_equals validation
│   ├── validation.php    # Pure input validation functions
│   ├── auth.php          # Procedural form submission handlers
│   └── view.php          # Output escaping (e) and template rendering
├── views/
│   ├── layout.php        # Shared Bootstrap layout shell
│   ├── login.php         # Login form
│   ├── register.php      # Registration form
│   ├── dashboard.php     # Authenticated welcome page & POST logout form
│   ├── change-password.php
│   └── error.php         # Error view
├── tests/                # PHPUnit Unit & Feature tests
├── index.php             # Dashboard entrypoint
├── login.php             # Login entrypoint
├── register.php          # Registration entrypoint
├── change-password.php   # Password change entrypoint
├── logout.php            # POST-only logout entrypoint
├── database.sql          # MySQL schema
└── router.php            # Built-in PHP server router (clean URLs)
```

## Features
- User registration, login, password change, and CSRF-protected POST logout
- Password hashing via `password_hash()` and `password_verify()` (`PASSWORD_DEFAULT`)
- SQL injection protection with procedural MySQLi prepared statements (`mysqli_prepare`, `mysqli_stmt_bind_param`)
- Session fixation protection (`session_regenerate_id(true)`), `HttpOnly` + `SameSite=Lax` cookies, and CSRF token rotation on login
- Strict output escaping (`htmlspecialchars` with `ENT_QUOTES | ENT_SUBSTITUTE`) across all Bootstrap views
- Clean extensionless URLs via `.htaccess` (Apache) and `router.php` (PHP built-in server)

## Prerequisites
- **PHP 8.0+** with `ext-mysqli`
- **Composer**
- **MySQL** Database

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Database Setup
Import `database.sql` into MySQL:
```bash
mysql -u root < database.sql
```
*Note: This creates a database named `demo` and a `users` table.*

### 3. Configuration
`db_config_from_env()` in `includes/config.php` reads `MYSQL_URL` or `DATABASE_URL` (for Railway/cloud deployments), or falls back to `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, and `MYSQLPASSWORD` (local defaults: `localhost:3306`, DB `demo`, user `root`, empty password).

### 4. Running Locally

**Option A: Docker (PHP + MySQL 8.4, recommended):**
```bash
docker compose up --build
```
Then open [http://localhost:8081](http://localhost:8081) in your browser (`database.sql` is imported automatically on first start).

**Option B: Standalone PHP + existing MySQL:**
```bash
php -S localhost:8000 router.php
```
Then open [http://localhost:8000](http://localhost:8000) in your browser.

### 5. Running Tests
```bash
composer test
```
