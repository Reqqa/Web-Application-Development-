
<?php
// Establish Secure Connection to MySQL
$host = "localhost";
$db = "coursemap";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$message = "";
$status = "";

// Handle Form Processing
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    try {
        // --- MASS IMPORT COURSES ---
        if ($action === "mass_import_courses") {
            $json_data = json_decode($_POST["courses_json"], true);
            if (is_array($json_data)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO courses (title, description, duration, sections, level, banner_class, icon) VALUES (?, ?, ?, ?, ?, ?, ?)",
                );
                $count = 0;
                foreach ($json_data as $row) {
                    $stmt->execute([
                        $row["title"],
                        $row["description"],
                        $row["duration"],
                        $row["sections"],
                        $row["level"],
                        $row["banner_class"],
                        $row["icon"],
                    ]);
                    $count++;
                }
                $message = "Successfully mass-imported $count courses!";
                $status = "success";
            } else {
                throw new Exception(
                    "Invalid JSON formatting structure for Courses.",
                );
            }
        }

        // --- MASS IMPORT LESSON CONTENTS ---
        if ($action === "mass_import_contents") {
            $json_data = json_decode($_POST["contents_json"], true);
            if (is_array($json_data)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO course_contents (course_id, sequence_order, section_title, body_text, code_snippet, callout_note) VALUES (?, ?, ?, ?, ?, ?)",
                );
                $count = 0;
                foreach ($json_data as $row) {
                    $stmt->execute([
                        $row["course_id"],
                        $row["sequence_order"],
                        $row["section_title"],
                        $row["body_text"],
                        $row["code_snippet"] ?? null,
                        $row["callout_note"] ?? null,
                    ]);
                    $count++;
                }
                $message = "Successfully mass-imported $count lesson contents!";
                $status = "success";
            } else {
                throw new Exception(
                    "Invalid JSON formatting structure for Lesson Contents.",
                );
            }
        }

        // --- STANDARD INDIVIDUAL DELETE OPERATIONS ---
        if ($action === "delete_course") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);

            $stmt2 = $pdo->prepare(
                "DELETE FROM course_contents WHERE course_id = ?",
            );
            $stmt2->execute([$id]);
            $message =
                "Course and its associated lesson metrics dropped successfully.";
            $status = "success";
        }

        if ($action === "delete_content") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare("DELETE FROM course_contents WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Lesson node deleted successfully.";
            $status = "success";
        }
    } catch (Exception $e) {
        $message = "Error tracking operation: " . $e->getMessage();
        $status = "error";
    }
}

// Fetch active datasets
$courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
$contents = $pdo
    ->query(
        "SELECT cc.*, c.title as course_title FROM course_contents cc JOIN courses c ON cc.course_id = c.id ORDER BY cc.course_id ASC, cc.sequence_order ASC",
    )
    ->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Management Console</title>
    <link rel="stylesheet" href="../wrappers/css/style.css">
    <style>
        .admin-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 24px; border-radius: 6px; margin-bottom: 32px; }
        .admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        textarea.json-area { width: 100%; height: 160px; font-family: monospace; padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 8px; font-size: 0.85rem; background: #1e1e2e; color: #cdd6f4; resize: vertical; }
        .alert { padding: 16px; border-radius: 4px; margin-bottom: 24px; font-weight: 600; }
        .alert.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .btn-danger { background-color: #dc2626 !important; color: #fff !important; cursor: pointer; border: none; padding: 6px 12px; border-radius: 4px; }
        .btn-danger:hover { background-color: #b91c1c !important; }
    </style>
</head>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <header class="page-header">
            <h1>Platform Administration Control Deck</h1>
            <p>Deploy programmatic updates, execute data sweeps, or process continuous mass batch imports.</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $status; ?>"><?php echo htmlspecialchars(
    $message,
); ?></div>
        <?php endif; ?>

        <section class="admin-grid">
            <div class="admin-box">
                <h3>Mass Add Courses (JSON Entry)</h3>
                <p class="footnote">Inject an array containing title, description, duration, sections, level, banner_class, and icon.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="mass_import_courses">
                    <textarea class="json-area" name="courses_json" placeholder='[{"title": "Rust Mastery", ...}]' required></textarea>
                    <button type="submit" class="btn btn-sm" style="margin-top:12px; width:100%;">Process Mass Course Injection</button>
                </form>
            </div>

            <div class="admin-box">
                <h3>Mass Add Lessons (JSON Entry)</h3>
                <p class="footnote">Inject an array mapping to course_id, sequence_order, section_title, body_text, code_snippet, and callout_note.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="mass_import_contents">
                    <textarea class="json-area" name="contents_json" placeholder='[{"course_id": 2, ...}]' required></textarea>
                    <button type="submit" class="btn btn-sm" style="margin-top:12px; width:100%;">Process Mass Lesson Injection</button>
                </form>
            </div>
        </section>

        <section class="admin-box">
            <h2>Current Courses Registry (<?php echo count($courses); ?>)</h2>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Level</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><strong><?php echo $c["id"]; ?></strong></td>
                            <td><?php echo htmlspecialchars(
                                $c["title"],
                            ); ?></td>
                            <td><?php echo htmlspecialchars(
                                $c["level"],
                            ); ?></td>
                            <td><?php echo $c["duration"]; ?> hrs</td>
                            <td>
                                <form method="POST" class="js-delete-form"
                                      data-item-type="course"
                                      data-item-name="<?php echo htmlspecialchars(
                                          $c["title"],
                                          ENT_QUOTES,
                                      ); ?>">
                                    <input type="hidden" name="action" value="delete_course">
                                    <input type="hidden" name="id" value="<?php echo $c[
                                        "id"
                                    ]; ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-box">
            <h2>Active Lesson Contents Map (<?php echo count(
                $contents,
            ); ?>)</h2>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>Course Target</th>
                        <th>Seq</th>
                        <th>Section Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contents as $cnt): ?>
                        <tr>
                            <td><small><?php echo htmlspecialchars(
                                $cnt["course_title"],
                            ); ?></small></td>
                            <td><strong><?php echo $cnt[
                                "sequence_order"
                            ]; ?></strong></td>
                            <td><?php echo htmlspecialchars(
                                $cnt["section_title"],
                            ); ?></td>
                            <td>
                                <form method="POST" class="js-delete-form"
                                      data-item-type="lesson"
                                      data-item-name="<?php echo htmlspecialchars(
                                          $cnt["section_title"],
                                          ENT_QUOTES,
                                      ); ?>">
                                    <input type="hidden" name="action" value="delete_content">
                                    <input type="hidden" name="id" value="<?php echo $cnt[
                                        "id"
                                    ]; ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include "footer.php"; ?>

    <script>
        function confirmDoubleDelete(itemType, itemName) {
            // First validation checkpoint
            const firstCheck = confirm(`Are you sure you want to delete the ${itemType}: "${itemName}"?`);

            if (!firstCheck) {
                return false;
            }

            // Warning variations based on structural impacts
            const warningMsg = itemType === 'course'
                ? `⚠️ WARNING: This will also permanently drop ALL modular lesson content attached to this course! Are you ABSOLUTELY sure?`
                : `Are you absolutely sure you want to permanently erase this lesson module? This action cannot be undone.`;

            // Second validation checkpoint
            return confirm(warningMsg);
        }

        // Attach handlers via data-* attributes instead of inline PHP-in-JS
        document.querySelectorAll('.js-delete-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const itemType = form.dataset.itemType;
                const itemName = form.dataset.itemName;
                if (!confirmDoubleDelete(itemType, itemName)) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
