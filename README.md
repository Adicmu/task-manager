# Task Management System

A professional Task Management Web Application built with Core PHP, MySQL, and jQuery.

## Features

- **User Authentication**: Secure login and registration system.
- **Task CRUD**: Create, Read, Update, and Delete tasks.
- **AJAX Driven**: Smooth user experience with no page reloads for task operations.
- **Search & Filter**: Easily find tasks by title or filter by status.
- **Sorting & Pagination**: Efficiently manage large lists of tasks.
- **Security**: Uses prepared statements to prevent SQL injection and secure password hashing.

## Setup Instructions

1. **Database Setup**:
   - Create a MySQL database named `task_manager`.
   - Import the schema from `sql/database.sql`.

2. **Configuration**:
   - Update `config/database.php` with your database credentials.

3. **Running the App**:
   - Place the project in your web server's root directory (e.g., `htdocs`).
   - Access the application via your browser.
   - Default login: `admin` / `password123`.

## Technical Stack
- **Backend**: PHP
- **Database**: MySQL (PDO)
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
