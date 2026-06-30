//Test

<?php
// --- PHP logic runs first, on the server ---
session_start();
require_once "includes/db-connect.php";

// Check if a user is logged in (for the nav menu later)
$isLoggedIn = isset($_SESSION["user_id"]);
$userRole = $_SESSION["role"] ?? "guest";

// Pull a few featured courses from the database
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CourseMap - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

    <?php include "includes/header.php"; ?>

    <main>
        <section class="hero">
            <h1>Learn New Skills with CourseMap</h1>
            <p>Browse courses, enroll, and track your progress.</p>
            <a href="courses/listing.php" class="btn">Browse Courses</a>
        </section>

        <section class="featured-courses">
            <h2>Featured Courses</h2>
            <div class="course-grid">

        </section>
    </main>


</body>
</html>
