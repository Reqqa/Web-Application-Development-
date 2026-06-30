<?php
// 1. Initialize the session context so PHP knows which session to target
session_start();

// 2. Clear all session variables in memory array tracking
$_SESSION = [];

// 3. Destroy the session cookie footprint inside the user's browser completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000, // Explicitly backdate expiration to force browser trash collection
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"],
    );
}

// 4. Destroy the actual session file storage on the server
session_destroy();

// 5. Route the clean, anonymous user back to your newly overhauled login portal
header("Location: auth.php");
exit();
?>
