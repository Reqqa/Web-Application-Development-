<?php
// 1. Establish Secure Connection to MySQL
$host = "localhost";
$db = "coursemap";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Query all courses from the table
$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseMap - Invest in Yourself</title>
    <link rel="stylesheet" href="../wrappers/css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <section class="hero">
            <h1 class="hero-title">Invest in a better future, <br>invest in <span class="highlight">yourself.</span></h1>
            <p class="hero-subtitle">Offering comprehensive online courses signed to empower students with the knowledge and skills needed to be equipped for a better future.</p>
            <a href="../src/courses.php" class="btn btn-primary" style="white-space: nowrap;">
                View courses
            </a>
        </section>

        <section class="courses-section">
            <div class="section-header">
                <h2>Browse our most popular courses.</h2>
                <a href="../src/courses.php" class="view-all">View all ➔</a>
            </div>

            <div class="page-layout">
                <section class="main-content">
                    <div class="course-grid">

                        <?php if (count($courses) > 0): ?>
                                <?php foreach (
                                    array_slice($courses, 0, 3)
                                    as $course
                                ): ?>

                                <div class="course-card" data-level="<?php echo htmlspecialchars(
                                    $course["level"],
                                ); ?>">
                                    <div class="card-banner <?php echo htmlspecialchars(
                                        $course["banner_class"],
                                    ); ?>">
                                        <span class="icon" style="font-size: 80px;"><?php echo htmlspecialchars(
                                            $course["icon"],
                                        ); ?></span>
                                    </div>
                                    <div class="card-content">
                                        <span class="course-meta">
                                            ⏱️ <?php echo htmlspecialchars(
                                                $course["duration"],
                                            ); ?> hours
                                            &nbsp;•&nbsp;
                                            📚 <?php echo htmlspecialchars(
                                                $course["sections"],
                                            ); ?> sections
                                            &nbsp;•&nbsp;
                                            <span class="level-badge"><?php echo htmlspecialchars(
                                                $course["level"],
                                            ); ?></span>
                                        </span>

                                        <h3><?php echo htmlspecialchars(
                                            $course["title"],
                                        ); ?></h3>
                                        <p class="course-desc"><?php echo htmlspecialchars(
                                            $course["description"],
                                        ); ?></p>

                                        <div class="card-footer">
                                            <a href="content.php?course_id=<?php echo $course[
                                                "id"
                                            ]; ?>" class="btn btn-sm" style="width: 100%; text-align: center; text-decoration: none; display: inline-block;">
                                                View Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No courses found in database.</p>
                        <?php endif; ?>

                    </div>
        </section>
    </main>

    <?php include "footer.php"; ?>
</body>
</html>
