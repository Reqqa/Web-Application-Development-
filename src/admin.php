<?php
session_start();

// --- AUTH GUARD ---
if (empty($_SESSION["user_id"])) {
    header("Location: auth.php");
    exit();
}
if ($_SESSION["username"] != "Admin") {
    header("Location: javascript:history.back();");
    exit();
}
// Establish Connection to MySQL
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
        // --- ADD COURSE ---
        if ($action === "add_course") {
            $stmt = $pdo->prepare(
                "INSERT INTO courses (title, description, duration, sections, level, banner_class, icon) VALUES (?, ?, ?, ?, ?, ?, ?)",
            );
            $stmt->execute([
                $_POST["title"],
                $_POST["description"],
                $_POST["duration"],
                $_POST["sections"],
                $_POST["level"],
                $_POST["banner_class"],
                $_POST["icon"],
            ]);
            $message = "Course added successfully!";
            $status = "success";
        }

        // --- EDIT COURSE ---
        if ($action === "edit_course") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare(
                "UPDATE courses SET title = ?, description = ?, duration = ?, sections = ?, level = ?, banner_class = ?, icon = ? WHERE id = ?",
            );
            $stmt->execute([
                $_POST["title"],
                $_POST["description"],
                $_POST["duration"],
                $_POST["sections"],
                $_POST["level"],
                $_POST["banner_class"],
                $_POST["icon"],
                $id,
            ]);
            $message = "Course updated successfully!";
            $status = "success";
        }

        // --- ADD LESSON CONTENT ---
        if ($action === "add_content") {
            $stmt = $pdo->prepare(
                "INSERT INTO course_contents (course_id, sequence_order, section_title, body_text, code_snippet, callout_note) VALUES (?, ?, ?, ?, ?, ?)",
            );
            $stmt->execute([
                $_POST["course_id"],
                $_POST["sequence_order"],
                $_POST["section_title"],
                $_POST["body_text"],
                $_POST["code_snippet"] ?: null,
                $_POST["callout_note"] ?: null,
            ]);
            $message = "Lesson content added successfully!";
            $status = "success";
        }

        // --- EDIT LESSON CONTENT ---
        if ($action === "edit_content") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare(
                "UPDATE course_contents SET course_id = ?, sequence_order = ?, section_title = ?, body_text = ?, code_snippet = ?, callout_note = ? WHERE id = ?",
            );
            $stmt->execute([
                $_POST["course_id"],
                $_POST["sequence_order"],
                $_POST["section_title"],
                $_POST["body_text"],
                $_POST["code_snippet"] ?: null,
                $_POST["callout_note"] ?: null,
                $id,
            ]);
            $message = "Lesson content updated successfully!";
            $status = "success";
        }

        // --- DELETE COURSE ---
        if ($action === "delete_course") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);

            $stmt2 = $pdo->prepare(
                "DELETE FROM course_contents WHERE course_id = ?",
            );
            $stmt2->execute([$id]);
            $message =
                "Course and its associated lesson contents deleted successfully.";
            $status = "success";
        }

        // --- DELETE LESSON CONTENT ---
        if ($action === "delete_content") {
            $id = intval($_POST["id"]);
            $stmt = $pdo->prepare("DELETE FROM course_contents WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Lesson content deleted successfully.";
            $status = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
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

// Helper to find entry for pre-filling edit modes
function find_by_id($rows, $id)
{
    foreach ($rows as $row) {
        if ($row["id"] == $id) {
            return $row;
        }
    }
    return null;
}

$edit_course = isset($_GET["edit_course"])
    ? find_by_id($courses, intval($_GET["edit_course"]))
    : null;
$edit_content = isset($_GET["edit_content"])
    ? find_by_id($contents, intval($_GET["edit_content"]))
    : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Deck — CourseMap Admin</title>
    <!-- REFERENCE THE UNIFIED THEME CSS -->
    <link rel="stylesheet" href="../wrappers/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<script src="authguard.js"></script>
<body>
    <?php include "header.php"; ?>

    <main class="container">
        <header class="page-header">
            <h1>Platform Administration Control Deck</h1>
            <p>Enterprise-grade structural content deployment system.</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $status; ?>"><?php echo htmlspecialchars(
    $message,
); ?></div>
        <?php endif; ?>

        <section class="metrics-grid">
            <div class="metric-card">
                <span>Active Systems Engines</span>
                <h4><?php echo count($courses); ?> Courses</h4>
            </div>
            <div class="metric-card">
                <span>Compiled Lesson Blocks</span>
                <h4><?php echo count($contents); ?> Modules</h4>
            </div>
        </section>

        <section class="admin-grid">
            <div class="admin-box">
                <div class="form-box-header">
                    <h3><?php echo $edit_course
                        ? "Edit Active Course"
                        : "Create Core Course"; ?></h3>
                    <?php if ($edit_course): ?>
                        <a href="admin.php" class="btn-cancel">Dismiss</a>
                    <?php endif; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $edit_course
                        ? "edit_course"
                        : "add_course"; ?>">
                    <?php if ($edit_course): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_course[
                            "id"
                        ]; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="title">Course Title</label>
                        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars(
                            $edit_course["title"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="description">Syllabus Context Summary</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars(
                            $edit_course["description"] ?? "",
                        ); ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="duration">Instructional Duration (Hours)</label>
                        <input type="number" id="duration" name="duration" step="0.5" min="0" required value="<?php echo htmlspecialchars(
                            $edit_course["duration"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="sections">Target Section Allocation Count</label>
                        <input type="number" id="sections" name="sections" min="0" required value="<?php echo htmlspecialchars(
                            $edit_course["sections"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="level">Complexity Tier Ranking</label>
                        <select id="level" name="level" required>
                            <?php
                            $levels = ["Beginner", "Intermediate", "Advanced"];
                            $current_level = $edit_course["level"] ?? "";
                            foreach ($levels as $lvl) {
                                $selected =
                                    $current_level === $lvl ? "selected" : "";
                                echo "<option value=\"$lvl\" $selected>$lvl</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="banner_class">Frontend Frame CSS Configuration</label>
                        <input type="text" id="banner_class" name="banner_class" required value="<?php echo htmlspecialchars(
                            $edit_course["banner_class"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="icon">Design Vector Reference Element</label>
                        <input type="text" id="icon" name="icon" required value="<?php echo htmlspecialchars(
                            $edit_course["icon"] ?? "",
                        ); ?>">
                    </div>

                    <button type="submit" class="btn-primary-deck">
                        <?php echo $edit_course
                            ? "Apply Block Modification"
                            : "Deploy Course Architecture"; ?>
                    </button>
                </form>
            </div>

            <div class="admin-box">
                <div class="form-box-header">
                    <h3><?php echo $edit_content
                        ? "Modify Target Content Segment"
                        : "Inject Structural Course Content"; ?></h3>
                    <?php if ($edit_content): ?>
                        <a href="admin.php" class="btn-cancel">Dismiss</a>
                    <?php endif; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $edit_content
                        ? "edit_content"
                        : "add_content"; ?>">
                    <?php if ($edit_content): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_content[
                            "id"
                        ]; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="course_id">Host Target Matrix</label>
                        <select id="course_id" name="course_id" required>
                            <?php foreach ($courses as $c):
                                $sel =
                                    isset($edit_content["course_id"]) &&
                                    $edit_content["course_id"] == $c["id"]
                                        ? "selected"
                                        : ""; ?>
                                <option value="<?php echo $c[
                                    "id"
                                ]; ?>" <?php echo $sel; ?>>
                                    <?php echo htmlspecialchars($c["title"]); ?>
                                </option>
                            <?php
                            endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="sequence_order">Execution Order Rank</label>
                        <input type="number" id="sequence_order" name="sequence_order" min="0" required value="<?php echo htmlspecialchars(
                            $edit_content["sequence_order"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="section_title">Section Block Identifier Header</label>
                        <input type="text" id="section_title" name="section_title" required value="<?php echo htmlspecialchars(
                            $edit_content["section_title"] ?? "",
                        ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="body_text">Narrative Body Markdown/Text</label>
                        <textarea id="body_text" name="body_text" required><?php echo htmlspecialchars(
                            $edit_content["body_text"] ?? "",
                        ); ?></textarea>
                    </div>

                    <div class="form-row code">
                        <label for="code_snippet">Development Code Block Sandbox (Optional)</label>
                        <textarea id="code_snippet" name="code_snippet"><?php echo htmlspecialchars(
                            $edit_content["code_snippet"] ?? "",
                        ); ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="callout_note">Visual Highlight Highlight Note (Optional)</label>
                        <textarea id="callout_note" name="callout_note"><?php echo htmlspecialchars(
                            $edit_content["callout_note"] ?? "",
                        ); ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary-deck">
                        <?php echo $edit_content
                            ? "Commit Structural Updates"
                            : "Broadcast Node Stream"; ?>
                    </button>
                </form>
            </div>
        </section>

        <section class="admin-box" style="margin-bottom: 32px;">
            <h2>Master Course Architecture Records (<?php echo count(
                $courses,
            ); ?>)</h2>
            <div class="table-container">
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>System ID</th>
                            <th>Descriptive Title</th>
                            <th>Difficulty Target</th>
                            <th>Allocation Range</th>
                            <th>Data Mutations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><code style="color:var(--accent-primary);">[ID-<?php echo $c[
                                    "id"
                                ]; ?>]</code></td>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars(
                                    $c["title"],
                                ); ?></td>
                                <td>
                                    <span class="badge <?php echo strtolower(
                                        $c["level"],
                                    ); ?>">
                                        <?php echo htmlspecialchars(
                                            $c["level"],
                                        ); ?>
                                    </span>
                                </td>
                                <td style="color: var(--text-muted);"><?php echo $c[
                                    "duration"
                                ]; ?> Hours</td>
                                <td class="row-actions">
                                    <a href="admin.php?edit_course=<?php echo $c[
                                        "id"
                                    ]; ?>" class="btn-edit">Edit</a>
                                    <form method="POST" class="js-delete-form" data-item-type="course" data-item-name="<?php echo htmlspecialchars(
                                        $c["title"],
                                        ENT_QUOTES,
                                    ); ?>">
                                        <input type="hidden" name="action" value="delete_course">
                                        <input type="hidden" name="id" value="<?php echo $c[
                                            "id"
                                        ]; ?>">
                                        <button type="submit" class="btn-danger">Drop</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-box">
            <h2>Active Content Matrix Nodes Mapping (<?php echo count(
                $contents,
            ); ?>)</h2>
            <div class="table-container">
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Host Target Pipeline</th>
                            <th>Sequence Key</th>
                            <th>Section Identifier Block</th>
                            <th>Data Mutations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contents as $cnt): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.85rem; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars(
                                        $cnt["course_title"],
                                    ); ?>
                                </td>
                                <td><code>#<?php echo $cnt[
                                    "sequence_order"
                                ]; ?></code></td>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars(
                                    $cnt["section_title"],
                                ); ?></td>
                                <td class="row-actions">
                                    <a href="admin.php?edit_content=<?php echo $cnt[
                                        "id"
                                    ]; ?>" class="btn-edit">Edit</a>
                                    <form method="POST" class="js-delete-form" data-item-type="lesson" data-item-name="<?php echo htmlspecialchars(
                                        $cnt["section_title"],
                                        ENT_QUOTES,
                                    ); ?>">
                                        <input type="hidden" name="action" value="delete_content">
                                        <input type="hidden" name="id" value="<?php echo $cnt[
                                            "id"
                                        ]; ?>">
                                        <button type="submit" class="btn-danger">Drop</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include "footer.php"; ?>

    <script>
        function confirmDoubleDelete(itemType, itemName) {
            const firstCheck = confirm(`Are you sure you want to delete the ${itemType}: "${itemName}"?`);
            if (!firstCheck) return false;

            const warningMsg = itemType === 'course'
                ? `⚠️ WARNING: This will also permanently drop ALL lesson content attached to this course! Are you ABSOLUTELY sure?`
                : `Are you absolutely sure you want to permanently erase this lesson module? This action cannot be undone.`;
            return confirm(warningMsg);
        }

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
