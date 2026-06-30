<?php
// 1. Establish secure database connection
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

// 2. Intercept the dynamic course ID from the URL (e.g. content.php?course_id=2)
// Fallback to course 1 (Python) if none is specified
$course_id = isset($_GET["course_id"]) ? intval($_GET["course_id"]) : 1;

// 3. Query the main course info for the header banner
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$courseStmt->execute([$course_id]);
$currentCourse = $courseStmt->fetch();

if (!$currentCourse) {
    die("Course not found.");
}

// 4. Query all 4 content sections matching this specific course_id
$contentStmt = $pdo->prepare(
    "SELECT * FROM course_contents WHERE course_id = ? ORDER BY sequence_order ASC",
);
$contentStmt->execute([$course_id]);
$modules = $contentStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(
        $currentCourse["title"],
    ); ?> - Dashboard</title>
    <!-- Relative path to your stylesheet -->
    <link rel="stylesheet" href="../wrappers/css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <!-- Course Meta Header Area -->
        <header class="page-header">
            <span class="course-meta"><?php echo htmlspecialchars(
                $currentCourse["icon"],
            ); ?> Dynamic Learning Deck</span>
            <h1><?php echo htmlspecialchars($currentCourse["title"]); ?></h1>
            <p><?php echo htmlspecialchars(
                $currentCourse["description"],
            ); ?></p>
        </header>

        <div class="page-layout">

            <!-- Left Navigation Sticky Sidebar Index -->
            <aside class="sidebar sticky-sidebar">
                <div class="filter-group">
                    <h3>Course Roadmap</h3>
                    <?php if (count($modules) > 0): ?>
                        <?php foreach ($modules as $index => $module): ?>
                            <a href="#section-<?php echo $module[
                                "id"
                            ]; ?>" class="syllabus-link <?php echo $index === 0
    ? "active"
    : ""; ?>">
                                <?php echo $module[
                                    "sequence_order"
                                ]; ?>. <?php echo htmlspecialchars(
    $module["section_title"],
); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: #6b7280; padding: 0 10px;">No modules loaded yet.</p>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Right Main Documentation Deck Panel -->
            <section class="main-content documentation-deck">

                <?php if (count($modules) > 0): ?>
                    <?php foreach ($modules as $module): ?>

                        <!-- Modular Content Area Block -->
                        <article id="section-<?php echo $module[
                            "id"
                        ]; ?>" class="doc-section">
                            <h2><?php echo $module[
                                "sequence_order"
                            ]; ?>. <?php echo htmlspecialchars(
    $module["section_title"],
); ?></h2>

                            <!-- Main Explanatory Paragraph -->
                            <p><?php echo nl2br(
                                htmlspecialchars($module["body_text"]),
                            ); ?></p>

                            <!-- Monospaced Code Snippet Highlight Box -->
                            <?php if (!empty($module["code_snippet"])): ?>
                                <pre><code><?php echo htmlspecialchars(
                                    $module["code_snippet"],
                                ); ?></code></pre>
                            <?php endif; ?>

                            <!-- Blockquote Sidebar Warning / Tip Box -->
                            <?php if (!empty($module["callout_note"])): ?>
                                <blockquote>
                                    <strong>Developer Note:</strong><br>
                                    <?php echo htmlspecialchars(
                                        $module["callout_note"],
                                    ); ?>
                                </blockquote>
                            <?php endif; ?>
                        </article>

                        <hr>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 40px; text-align: center; border-radius: 6px;">
                        <h3>Syllabus is Empty</h3>
                        <p>We couldn't find any data in the <code>course_contents</code> table for course ID: <strong><?php echo $course_id; ?></strong>.</p>
                    </div>
                <?php endif; ?>

            </section>
        </div>
    </main>

    <?php include "footer.php"; ?>

    <!-- Optional small JavaScript snippet to switch active highlights when scrolling -->
    <script>
        document.querySelectorAll('.syllabus-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.syllabus-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
