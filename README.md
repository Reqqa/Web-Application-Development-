# CourseMap
Website LocalHost Link ( Req's Env Only : http://localhost/Web-Application-Development-/)

CourseMap is an e-learning web application that allows students to browse, enroll in, and track progress through online courses, while administrators manage the full course catalog.

Built entirely with **HTML, CSS, JavaScript, PHP, and MySQL** — no external frameworks, libraries, or templates were used, in compliance with assignment requirements.

## Features

- **Home Page** — overview of CourseMap and featured courses
- **Course Listing Page** — browse all available courses with category filtering
- **Course Details Page** — full syllabus, lessons, instructor info, and student reviews
- **My Courses (Enrollment/Wishlist)** — students save, enroll in, and manage their courses
- **Mark as Complete** — students track lesson and course progress
- **Admin Dashboard** — full Create, Read, Update, Delete (CRUD) management of courses
- **User Authentication** — secure registration and login with session-based access control, role-based (admin/student) views
- **Contact Page** — contact form with business details and embedded map
- **Responsive Design** — fully adapts to mobile, tablet, and desktop using CSS media queries only

## Tech Stack

| Layer        | Technology               |
|--------------|---------------------------|
| Front-end    | HTML5, CSS3, vanilla JavaScript |
| Back-end     | PHP (procedural/mysqli)  |
| Database     | MySQL                    |
| Server       | Apache (via XAMPP/WAMP/MAMP) |

## Installation Instructions

### 1. Requirements
- XAMPP / WAMP / MAMP (or any Apache + PHP 7.4+ + MySQL stack)
- A web browser

### 2. Setup Steps

1. Clone or extract this project into your server's web root:
   - XAMPP: `htdocs/CourseMap`
   - WAMP: `www/CourseMap`

2. Start Apache and MySQL from your control panel.

3. Create the database:
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Click **Import**, select `database/coursemap.sql`, and run it.
   - This creates the `coursemap_db` database with all tables and sample seed data.

4. Configure the database connection:
   - Open `includes/db-connect.php`
   - Update the credentials if needed:
     ```php
     $host = "localhost";
     $db_user = "root";
     $db_pass = "";
     $db_name = "coursemap_db";
     ```

5. Open the project in your browser:
   ```
   http://localhost/CourseMap/index.php
   ```

### 3. Default Accounts (from seed data)

| Role    | Email               | Password   |
|---------|---------------------|------------|
| Admin   | admin@coursemap.com | (set via registration/reset — seed hash is a placeholder) |
| Student | alice@student.com   | (same as above) |

> Note: Seed password hashes in `coursemap.sql` are placeholders. Run the registration page once to create real accounts, or update the hashes using PHP's `password_hash()`.

## Database Configuration

- Database name: `coursemap_db`
- Default connection: `mysqli` via `includes/db-connect.php`
- Tables: `users`, `categories`, `courses`, `lessons`, `enrollments`, `progress`, `reviews`, `contact_messages`

## Project Structure

See `report/CourseMap_Report.pdf` for full website structure documentation and code explanations.

## Group Members

| Name | Student ID | Contribution |
|------|------------|---------------|
| TBD  | TBD        | TBD           |

## License

This project is submitted for academic purposes under UECS2094/UECS2194/EECS2194 Web Application Development.
