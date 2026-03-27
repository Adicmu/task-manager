# Task Management System — Internship Assignment

A robust, full-stack Task Management Web Application built from scratch using **Core PHP**, **MySQL**, and **jQuery**. This project demonstrates a solid understanding of professional backend development, database design, asynchronous frontend interactions, and secure coding practices.

---

## 🚀 Features

- **Secure Authentication**: Session-based user registration and login with `password_hash()` and `password_verify()`.
- **Complete Task CRUD**: Full Create, Read, Update, and Delete capabilities for tasks.
- **AJAX-Powered Interaction**: All task operations (Add, Edit, Delete, Toggle Status) are handled asynchronously for a seamless user experience.
- **Dynamic Search & Filtering**: Real-time task search by title and filtering by status (Pending, In Progress, Completed).
- **Advanced UI Features**: 
  - Column-based sorting (ID, Title, Status, Due Date).
  - Server-side Pagination for efficient data handling.
- **Mobile Responsive**: Clean and modern UI that adjusts to different screen sizes.
- **Security First**: 
  - Full protection against SQL Injection using PDO prepared statements.
  - Form validation on both client-side (jQuery) and server-side (PHP).

---

## 🛠 Project Setup Steps

### Prerequisites
- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- A local web server (e.g., Apache/NGINX) or PHP built-in server.

### 1. Clone/Extract the Project
Place the project folder in your web server's root directory (e.g., `www/`, `htdocs/`, or `/var/www/html/`).

### 2. Configure Database Connection
1. Open the file `config/database.php`.
2. Update the `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` constants to match your local environment.

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 3. Running the Application
If using the PHP built-in server, run the following from the project root:
```bash
php -S localhost:8000
```
Then visit `http://localhost:8000` in your browser.

---

## 🗄 Database Import Steps

The database schema and sample data are provided in `sql/database.sql`.

### Option A: Using phpMyAdmin (Recommended)
1. Open phpMyAdmin and create a new database named `task_manager`.
2. Select the `task_manager` database on the left sidebar.
3. Click the **Import** tab.
4. Choose the `sql/database.sql` file and click **Go** at the bottom.

### Option B: Using MySQL Command Line
Run the following command (replace `root` with your username):
```bash
mysql -u root -p -e "CREATE DATABASE task_manager"
mysql -u root -p task_manager < sql/database.sql
```

**Default Test Account:**
- **Username:** `admin`
- **Password:** `password123`

---

## 📝 Assumptions & Design Decisions

- **Multi-User Architecture**: Tasks are linked to specific User IDs. Each user can only see, edit, and delete their own tasks.
- **Session Security**: Authentication is managed via PHP sessions. An `auth_check.php` helper is used on protected pages to ensure only logged-in users can reach the dashboard.
- **Data Integrity**: Used `ENUM` types for task status and `DATE` for due dates to ensure consistent data storage.
- **Consistent UI**: Leveraged reusable PHP includes for the `header.php` and `footer.php` to maintain a DRY (Don't Repeat Yourself) architecture.
- **External Dependencies**: Minimized external libraries to focus on Core PHP skills, using only a standard jQuery CDN for the frontend logic.

---

## 📂 Folder Structure

- `api/`: AJAX endpoints for authentication and task operations.
- `assets/`: UI assets (CSS styles and JS logic).
- `config/`: Database connection and global settings.
- `includes/`: Reusable HTML components and auth helpers.
- `sql/`: Database schema and migration files.
- `*.php`: Main entry pages (index, login, register, logout).
