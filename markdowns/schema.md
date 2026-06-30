-- =========================================================
-- CourseMap Database Schema
-- UECS2094/UECS2194/EECS2194 Web Application Development
-- =========================================================

CREATE DATABASE IF NOT EXISTS coursemap_db;
USE coursemap_db;

-- ---------------------------------------------------------
-- 1. USERS (Admins + Students, role-based)
-- ---------------------------------------------------------
CREATE TABLE users (
    user_id         INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    profile_image   VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- 2. CATEGORIES (so listing page can filter courses)
-- ---------------------------------------------------------
CREATE TABLE categories (
    category_id     INT AUTO_INCREMENT PRIMARY KEY,
    category_name   VARCHAR(80) NOT NULL UNIQUE
);

-- ---------------------------------------------------------
-- 3. COURSES (Admin: full CRUD; Student: Read only)
-- ---------------------------------------------------------
CREATE TABLE courses (
    course_id       INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(150) NOT NULL,
    short_desc      VARCHAR(255) NOT NULL,
    full_desc       TEXT NOT NULL,
    thumbnail       VARCHAR(255) DEFAULT NULL,
    category_id     INT,
    instructor_name VARCHAR(100) DEFAULT NULL,
    duration_hours  INT DEFAULT 0,
    level           ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    created_by      INT NOT NULL,                 -- admin user_id who created it
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- 4. LESSONS (sub-units inside a course, for granular completion)
-- ---------------------------------------------------------
CREATE TABLE lessons (
    lesson_id       INT AUTO_INCREMENT PRIMARY KEY,
    course_id       INT NOT NULL,
    lesson_title    VARCHAR(150) NOT NULL,
    lesson_order    INT NOT NULL DEFAULT 1,
    content_type    ENUM('video', 'text', 'quiz') DEFAULT 'text',
    content_body    TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- 5. ENROLLMENTS (this IS the "cart/wishlist" — student-saved courses)
-- ---------------------------------------------------------
CREATE TABLE enrollments (
    enrollment_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    course_id       INT NOT NULL,
    status          ENUM('saved', 'enrolled', 'completed') DEFAULT 'saved',
    enrolled_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- 6. PROGRESS (mark-as-complete tracking, per lesson)
-- ---------------------------------------------------------
CREATE TABLE progress (
    progress_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    lesson_id       INT NOT NULL,
    is_completed    BOOLEAN DEFAULT FALSE,
    completed_at    TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_progress (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- 7. REVIEWS (optional — supports "user interactions" on details page)
-- ---------------------------------------------------------
CREATE TABLE reviews (
    review_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    course_id       INT NOT NULL,
    rating          TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment         TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- 8. CONTACT MESSAGES (from Contact Page form)
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
    message_id      INT AUTO_INCREMENT PRIMARY KEY,
    sender_name     VARCHAR(100) NOT NULL,
    sender_email    VARCHAR(150) NOT NULL,
    subject         VARCHAR(150),
    message         TEXT NOT NULL,
    submitted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- SEED DATA
-- =========================================================

INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Admin', 'admin@coursemap.com', '$2y$10$examplehashedpasswordadminxxxxxxxxxxxxxxxxxxxxxxx', 'admin'),
('Alice Tan', 'alice@student.com', '$2y$10$examplehashedpasswordstudent1xxxxxxxxxxxxxxxxxxx', 'student'),
('Brian Lee', 'brian@student.com', '$2y$10$examplehashedpasswordstudent2xxxxxxxxxxxxxxxxxxx', 'student');

INSERT INTO categories (category_name) VALUES
('Web Development'), ('Data Science'), ('Mobile Development'), ('Design');

INSERT INTO courses (title, short_desc, full_desc, category_id, instructor_name, duration_hours, level, created_by) VALUES
('Introduction to PHP', 'Learn server-side scripting with PHP.', 'A beginner-friendly course covering PHP syntax, forms, sessions, and MySQL integration.', 1, 'Dr. Wong', 12, 'Beginner', 1),
('Responsive CSS Layouts', 'Master CSS media queries and flexible design.', 'Covers flexbox, grid, and media queries to build responsive websites without frameworks.', 1, 'Ms. Kumar', 8, 'Beginner', 1),
('SQL for Beginners', 'Understand relational databases and queries.', 'Introduces tables, joins, normalization, and CRUD operations in MySQL.', 2, 'Mr. Lim', 10, 'Beginner', 1);

INSERT INTO lessons (course_id, lesson_title, lesson_order, content_type, content_body) VALUES
(1, 'PHP Syntax Basics', 1, 'text', 'Introduction to variables, loops, and functions in PHP.'),
(1, 'Connecting to MySQL', 2, 'text', 'Using mysqli to connect PHP to a MySQL database.'),
(2, 'Flexbox Fundamentals', 1, 'text', 'Building layouts using the CSS flexbox model.'),
(2, 'Media Queries', 2, 'text', 'Making layouts responsive across devices.');

INSERT INTO enrollments (user_id, course_id, status) VALUES
(2, 1, 'enrolled'),
(2, 2, 'saved'),
(3, 1, 'enrolled');

INSERT INTO progress (user_id, lesson_id, is_completed, completed_at) VALUES
(2, 1, TRUE, NOW()),
(2, 2, FALSE, NULL);

INSERT INTO reviews (user_id, course_id, rating, comment) VALUES
(2, 1, 5, 'Very clear explanations, helped me understand PHP fast.');
