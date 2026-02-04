# PHP Login Application

A simple, secure PHP login and registration system using MySQL.

**Live Demo:** [https://ajlato-login.up.railway.app](https://ajlato-login.up.railway.app)

## Features
- User Registration
- User Login
- Secure Password Hashing
- Session Management
- Protected "Welcome" Page
- Logout Functionality
- Styled with Bootstrap

## Prerequisites
- **PHP** (Wait... checks indicate 8.0+ recommended due to syntax, let's say 8.0+)
- **MySQL** Database

## Setup Instructions

### 1. Database Setup
The application requires a MySQL database. You can set up the required structure using the provided `database.sql` file.

**Using command line:**
```bash
mysql -u root < database.sql
```
*Note: This creates a database named `demo` and a table `users`.*

### 2. Configuration
The application uses `config.php` for database connection. It connects automatically using **Environment Variables** (best for deployments like Railway) and falls back to **Local Defaults** for development.

**Default Local Credentials:**
- Host: `localhost`
- User: `root`
- Password: (empty)
- Database: `demo`
- Port: `3306`

**Deployment (Railway):**
The easiest way to connect is to use the single connection URL variable.
1. Add a new variable to your service: `MYSQL_URL`
2. Set the value to Reference the MySQL service URL (e.g. `${{ MySQL.MYSQL_URL }}`)

*Alternatively, you can still specify individual variables (MYSQLHOST, MYSQLUSER, etc) if preferred.*

### 3. Running Locally
You can easily start the application using the built-in PHP development server.

Run this command in the project root:
```bash
php -S localhost:8000
```
Then open [http://localhost:8000](http://localhost:8000) in your browser.
