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
    <title>All Courses - CourseMap</title>
    <link rel="stylesheet" href="../wrappers/css/style.css">
    <style>
        /* Target card icons explicitly for larger display and precise opacity scaling */
        .course-card .card-banner .icon {
            font-size: 3.5rem; /* Enhanced visibility structural sizing */
            opacity: 0.67;     /* Clean corporate presentation tone */
            transition: opacity 0.2s ease;
        }

        .course-card:hover .card-banner .icon {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <header class="page-header">
            <h1>Explore All Courses</h1>
            <p>Advance your career with enterprise-grade development and systems engineering training.</p>
        </header>

        <div class="page-layout">
            <aside class="sidebar">
                <div class="filter-group">
                    <h3>Difficulty Level</h3>
                    <label class="filter-item">
                        <input type="checkbox" class="difficulty-checkbox" value="Beginner" checked> Beginner
                    </label>
                    <label class="filter-item">
                        <input type="checkbox" class="difficulty-checkbox" value="Intermediate" checked> Intermediate
                    </label>
                    <label class="filter-item">
                        <input type="checkbox" class="difficulty-checkbox" value="Advanced" checked> Advanced
                    </label>
                </div>
            </aside>

            <section class="main-content">
                <div class="results-bar">
                    <span id="counter-display">Showing <?php echo count(
                        $courses,
                    ); ?> courses</span>
                </div>
                <div class="course-grid">

                    <?php if (count($courses) > 0): ?>
                        <?php foreach ($courses as $course): ?>
                            <div class="course-card" data-level="<?php echo htmlspecialchars(
                                $course["level"],
                            ); ?>">
                                <div class="card-banner <?php echo htmlspecialchars(
                                    $course["banner_class"],
                                ); ?>">
                                    <span class="icon"><?php echo htmlspecialchars(
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
        </div>
    </main>

    <?php include "footer.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.difficulty-checkbox');
            const cards = document.querySelectorAll('.course-card');
            const counterDisplay = document.getElementById('counter-display');

            function applyFilters() {
                // Get list of all currently checked levels
                const activeLevels = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                let visibleCount = 0;

                cards.forEach(card => {
                    const cardLevel = card.getAttribute('data-level');

                    // If a card matches an active level checkbox, show it; otherwise, hide it
                    if (activeLevels.includes(cardLevel)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Dynamically update the results bar string layout context
                counterDisplay.textContent = `Showing ${visibleCount} course${visibleCount === 1 ? '' : 's'}`;
            }

            // Bind change listener hooks to all checkboxes
            checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));

            // Run on configuration setup initial start sequence
            applyFilters();
        });
    </script>
</body>
</html>
