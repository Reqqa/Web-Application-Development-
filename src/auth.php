<?php
session_start();

// Redirect if already authenticated
if (isset($_SESSION["user_id"])) {
    header("Location: courses.php");
    exit();
}

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

$error = "";
$success = "";

// Process Request Pipelines
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    // --- REGISTRATION PIPELINE ---
    if ($action === "register") {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (!empty($username) && !empty($email) && !empty($password)) {
            try {
                // Securely hash the password before entry
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
                );
                $stmt->execute([$username, $email, $hashedPassword]);

                $success = "Account created successfully! Please log in below.";
            } catch (\PDOException $e) {
                $error =
                    $e->getCode() == 23000
                        ? "Username or Email already registered."
                        : "Registration failed.";
            }
        } else {
            $error = "All fields are strictly required.";
        }
    }

    // --- LOGIN PIPELINE ---
    if ($action === "login") {
        $usernameOrEmail = trim($_POST["login_input"]);
        $password = $_POST["password"];

        if (!empty($usernameOrEmail) && !empty($password)) {
            $stmt = $pdo->prepare(
                "SELECT * FROM users WHERE username = ? OR email = ?",
            );
            $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
            $userRecord = $stmt->fetch();

            if (
                $userRecord &&
                password_verify($password, $userRecord["password_hash"])
            ) {
                // Regenerate session ID to prevent fixation attacks
                session_regenerate_id(true);
                $_SESSION["user_id"] = $userRecord["id"];
                $_SESSION["username"] = $userRecord["username"];

                header("Location: courses.php");
                exit();
            } else {
                $error = "Invalid username/email or password matching state.";
            }
        } else {
            $error = "Please fill in all authorization keys.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Center - CourseMap</title>
    <link rel="stylesheet" href="../wrappers/css/style.css">
    <style>
        .auth-container { max-width: 450px; margin: 60px auto; padding: 32px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .auth-toggle { display: flex; gap: 16px; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        .toggle-btn { background: none; border: none; font-size: 1.1rem; font-weight: 600; color: #94a3b8; cursor: pointer; padding: 4px 12px; }
        .toggle-btn.active { color: #1e1e2e; border-bottom: 2px solid #1e1e2e; margin-bottom: -10px; }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
        .input-group { margin-bottom: 16px; }
        .input-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; color: #334155; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; }
        .auth-alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; font-weight: 500; }
        .auth-alert.err { background: #fee2e2; color: #991b1b; }
        .auth-alert.succ { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body style="background: #f8fafc; display: flex; flex-direction: column; min-height: 100vh;">

    <main style="flex-grow: 1; padding: 20px;">
        <div class="auth-container">
            <h2 style="margin-bottom: 4px; text-align: center;">CourseMap Gate</h2>
            <p style="text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 24px;">Access your enterprise engineering decks</p>

            <?php if (
                !empty($error)
            ): ?> <div class="auth-alert err"><?php echo htmlspecialchars(
     $error,
 ); ?></div> <?php endif; ?>
            <?php if (
                !empty($success)
            ): ?> <div class="auth-alert succ"><?php echo htmlspecialchars(
     $success,
 ); ?></div> <?php endif; ?>

            <div class="auth-toggle">
                <button class="toggle-btn active" onclick="switchTab('login-form', this)">Sign In</button>
                <button class="toggle-btn" onclick="switchTab('register-form', this)">Register</button>
            </div>

            <form id="login-form" class="form-panel active" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <label>Username or Email Address</label>
                    <input type="text" name="login_input" required>
                </div>
                <div class="input-group">
                    <label>Password Mapping Token</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 8px;">Authenticate Access</button>
            </form>

            <form id="register-form" class="form-panel" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="input-group">
                    <label>Desired Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-group">
                    <label>Secure Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 8px;">Create Platform Account</button>
            </form>
        </div>
    </main>

    <script>
        function switchTab(formId, btnEl) {
            document.querySelectorAll('.form-panel').forEach(form => form.classList.remove('active'));
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));

            document.getElementById(formId).classList.add('active');
            btnEl.classList.add('active');
        }
    </script>
</body>
</html>
