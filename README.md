# 🔵 Bubble Campus Marketplace
**Yanbu Industrial College (YIC) - CS381 Final Project** **Developed by:** Jana Darandari & Dana Majed Aljehani  

## Project Overview
Bubble Campus Marketplace is a secure, role-based web application designed to allow college students to easily buy, sell, and trade items such as textbooks, electronics, and furniture. 

## Features
* **Role-Based Access:** Dedicated interfaces for 'Students' and 'Admins'.
* **Secure Authentication:** User registration and login utilizing bcrypt password hashing.
* **Product Management:** Full CRUD operations allowing students to post, update status (available/sold), and delete their own listings.
* **Search & Filtering:** Dynamic homepage filtering by category, price, and status.
* **Internal Messaging:** Secure messaging system allowing buyers to contact sellers directly.
* **Security Integrations:** Strict PDO prepared statements (SQLi prevention), output sanitization (XSS prevention), and session-based CSRF tokens.

## Setup Instructions
This project runs purely on Vanilla PHP and MySQL. No external frameworks are required.

1. **Clone the repository:** `git clone https://github.com/Jana-Darandari/Cs381-final-project.git`
2. **Local Environment:** Move the project folder into your local server directory (e.g., `C:\laragon\www\` for Laragon).
3. **Database Configuration:**
   * Open your MySQL manager (HeidiSQL or phpMyAdmin).
   * Import the `schema.sql` file provided in the root directory. This will auto-generate the `bubble_market` database, tables, and 10+ sample records.
   * Verify your local database credentials inside `php/db_connect.php`.
4. **Launch:** Open your browser and navigate to `https://github.com/Jana-Darandari/Cs381-final-project.git`.

## Default Login Credentials
The `schema.sql` file provides pre-configured accounts for testing (All passwords are exactly: **password**).

**Admin Account:**
* Email: `admin@campus.edu`
* Password: `password`

**Student Account:**
* Email: `student@campus.edu`
* Password: `password`

## Technology Stack
* **Frontend:** HTML5, CSS3, ES6 JavaScript (UI/UX Custom Design).
* **Backend:** Plain PHP with PDO.
* **Database:** MySQL.