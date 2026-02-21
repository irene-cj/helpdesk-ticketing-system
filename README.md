# IT Help Desk Ticketing System

A full-stack web application for managing IT support tickets, built with PHP, MySQL, HTML, CSS, and JavaScript.

## Overview

This system allows organizations to log, track, and resolve IT support requests through a clean web interface. Staff members can submit tickets and monitor their status, while administrators have full control over ticket assignment, prioritization, and resolution.

## Features

- **User Authentication** — Secure login and registration with hashed passwords
- **Role-Based Access Control** — Separate dashboards and permissions for staff and admins
- **Ticket Management** — Create, view, update, and close support tickets
- **Priority & Category System** — Tickets organized by urgency and type (Hardware, Software, Network, etc.)
- **Admin Dashboard** — Overview of all tickets, statuses, and activity
- **Email Notifications** — Automated alerts when tickets are created or updated
- **Comment System** — Staff and admins can communicate within each ticket

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8, PDO |
| Database | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| UI Framework | Bootstrap 5 |
| Local Dev | XAMPP |
| Version Control | Git & GitHub |

## Database Schema

- `users` — stores staff and admin accounts with roles
- `tickets` — core ticket data including status, priority, and assignment
- `comments` — threaded replies on each ticket
- `notifications` — log of all email alerts sent

## Getting Started

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 8+
- A GitHub account

### Installation

1. Clone the repository
```bash
   git clone https://github.com/irene-cj/helpdesk-ticketing-system.git
```

2. Move the folder to your XAMPP htdocs directory
```bash
   mv helpdesk-ticketing-system /Applications/XAMPP/htdocs/helpdesk
```

3. Import the database — open phpMyAdmin at `http://localhost/phpmyadmin`, create a database called `helpdesk_db`, and import `helpdesk_db.sql`

4. Configure your database connection in `config/db.php`
```php
   $host = 'localhost';
   $db   = 'helpdesk_db';
   $user = 'root';
   $pass = '';
```

5. Visit `http://localhost/helpdesk/auth/login.php`

### Default Admin Login
```
Email:    admin@helpdesk.com
Password: admin123
```
> ⚠️ Change this password immediately after first login in a real deployment.

## Project Structure
```
helpdesk/
├── config/          # Database connection
├── includes/        # Shared components (header, footer, auth guard)
├── assets/          # CSS and JavaScript
├── admin/           # Admin-only pages
├── staff/           # Staff-only pages
├── tickets/         # Ticket create, view, update
├── auth/            # Login, register, logout
└── email/           # Notification logic
```

## Author

**Irene Carrillo Jaramillo**  
[GitHub](https://github.com/irene-cj) · [LinkedIn](www.linkedin.com/in/irene-carrillo-2b5932250)
