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
$activeView = "login";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "register") {
        $activeView = "register";
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (!empty($username) && !empty($email) && !empty($password)) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
                );
                $stmt->execute([$username, $email, $hashedPassword]);
                $success = "Account created successfully! Please log in below.";
                $activeView = "login";
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

    if ($action === "login") {
        $activeView = "login";
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
</head>
<body class="auth-page-body">

    <div class="auth-card">
        <a href="javascript:history.back()" class="auth-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Back
        </a>

        <div class="auth-title">
            <h2>Coursemap</h2>
        </div>

        <h2 id="auth-heading" class="auth-title">
            <?= $activeView === "register"
                ? "Sign Up Account"
                : "Yooo, welcome back!" ?>
        </h2>
        <p id="auth-subheading" class="auth-subtitle">
            <?php if ($activeView === "register"): ?>
                Already registered? <a href="#" onclick="toggleAuthView('login', event)">Log in</a>
            <?php else: ?>
                First time here? <a href="#" onclick="toggleAuthView('register', event)">Sign up for free</a>
            <?php endif; ?>
        </p>

        <?php if (!empty($error)): ?>
            <div class="auth-alert err"><?php echo htmlspecialchars(
                $error,
            ); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="auth-alert succ"><?php echo htmlspecialchars(
                $success,
            ); ?></div>
        <?php endif; ?>

        <form id="login-form" class="form-panel <?= $activeView === "login"
            ? "active"
            : "" ?>" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="input-group">
                <label>Username or Email</label>
                <input type="text" name="login_input" placeholder="Your email or username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth-submit">Sign in</button>
        </form>

        <form id="register-form" class="form-panel <?= $activeView ===
        "register"
            ? "active"
            : "" ?>" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="eg. John" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="eg. john@example.com" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 8 characters" required>
            </div>
            <button type="submit" class="btn-auth-submit">Create account</button>
        </form>
    </div>

    <script>
        function toggleAuthView(view, event) {
            if (event) event.preventDefault();

            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const heading = document.getElementById('auth-heading');
            const subheading = document.getElementById('auth-subheading');

            if (view === 'register') {
                loginForm.classList.remove('active');
                registerForm.classList.add('active');
                heading.textContent = "Sign Up Account";
                subheading.innerHTML = 'Already registered? <a href="#" onclick="toggleAuthView(\'login\', event)">Log in</a>';
            } else {
                registerForm.classList.remove('active');
                loginForm.classList.add('active');
                heading.textContent = "Yooo, welcome back!";
                subheading.innerHTML = 'First time here? <a href="#" onclick="toggleAuthView(\'register\', event)">Sign up for free</a>';
            }
        }
    </script>
</body>
</html>
