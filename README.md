# Task Management System

A Task Management Web Application built with Core PHP, MySQL, and jQuery.

## Live Demo

This application is publicly hosted and can be accessed at:

**http://vidi-internship-assignment.42web.io/**

> Note: The site uses HTTP (not HTTPS), so your browser may show a security warning. This is expected for free hosting and the site is safe to use. You can proceed past the warning to use the full application.

Default login credentials:
- Username: `admin`
- Password: `password123`

If you prefer to run it locally instead, follow the setup steps below.

---

## Features

- User registration and login (session-based authentication)
- Full CRUD operations on tasks (Add, Edit, Delete, List)
- Search tasks by title
- Filter tasks by status (Pending, In Progress, Completed)
- Column sorting (ID, Title, Status, Due Date, Created At)
- Pagination (8 tasks per page)
- All task operations use jQuery AJAX (no page reloads)
- Form validation on both frontend (jQuery) and backend (PHP)
- Prepared statements to prevent SQL injection
- Responsive design for mobile and desktop
- Reusable PHP includes for header, footer, and database connection

---

## Local Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Clone the Repository

```bash
git clone https://github.com/Adicmu/task-manager.git
cd task-manager
```

### Step 2: Create and Import the Database

Open your MySQL client and run:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS task_manager;"
mysql -u root -p task_manager < sql/database.sql
```

Or if you prefer phpMyAdmin:
1. Create a new database called `task_manager`.
2. Select the database, click the **Import** tab.
3. Upload `sql/database.sql` and click **Go**.

This creates the `users` and `tasks` tables and inserts a default admin user.

### Step 3: Configure the Database Connection

Open `config/database.php` and update the credentials to match your local MySQL setup:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', '');  // Set your MySQL password here
```

### Step 4: Start the Server

From the project root directory, run:

```bash
php -S localhost:8000
```

Then open your browser and go to `http://localhost:8000`.

### Step 5: Login

Use the default account or register a new one:
- Username: `admin`
- Password: `password123`

---

## Folder Structure

```
task-manager/
├── api/
│   ├── auth.php           # Login/register AJAX endpoint
│   └── tasks.php          # Task CRUD AJAX endpoint
├── assets/
│   ├── css/
│   │   └── style.css      # Application styles
│   └── js/
│       └── app.js         # jQuery/AJAX application logic
├── config/
│   └── database.php       # PDO database connection
├── includes/
│   ├── auth_check.php     # Session authentication helpers
│   ├── header.php         # Reusable HTML header with navbar
│   └── footer.php         # Reusable HTML footer with scripts
├── sql/
│   └── database.sql       # Database schema and seed data
├── index.php              # Main dashboard page
├── login.php              # Login page
├── register.php           # Registration page
├── logout.php             # Logout handler
└── README.md              # This file
```

---

## Database Schema

### users
| Column     | Type         | Description              |
|------------|--------------|--------------------------|
| id         | INT (PK)     | Auto-increment user ID   |
| username   | VARCHAR(50)  | Unique username          |
| password   | VARCHAR(255) | Hashed password          |
| created_at | TIMESTAMP    | Account creation time    |

### tasks
| Column      | Type         | Description                          |
|-------------|--------------|--------------------------------------|
| id          | INT (PK)     | Auto-increment task ID               |
| user_id     | INT (FK)     | References users.id                  |
| title       | VARCHAR(255) | Task title (required)                |
| description | TEXT         | Task description                     |
| status      | ENUM         | Pending / In Progress / Completed    |
| due_date    | DATE         | Optional due date                    |
| created_at  | TIMESTAMP    | Task creation time                   |

---

## Assumptions

- MySQL is running on `localhost` with the default port (3306).
- PHP has the PDO MySQL extension enabled (standard in most setups).
- The application uses `password_hash()` with `PASSWORD_DEFAULT` for secure password storage.
- jQuery 3.7.1 is loaded from CDN.
- Tasks belong to individual users (multi-user support). Each user can only see their own tasks.
- Session-based authentication is used. Protected pages check for a valid session before loading.

---

## Tech Stack

| Layer    | Technology             |
|----------|------------------------|
| Backend  | Core PHP (no frameworks) |
| Database | MySQL with PDO         |
| Frontend | HTML, CSS, JavaScript  |
| Library  | jQuery 3.7.1           |
