<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<nav class="custom-navbar" style="background: #0a0a0a; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 16px 40px; display: flex; align-items: center; justify-content: space-between; font-family: sans-serif;">
    <div class="nav-branding" style="display: flex; align-items: center; gap: 12px;">
        <a href="courses.php" style="color: #ffffff; text-decoration: none; font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px;">Course<span style="color: #ffffff; opacity: 0.7;">Map</span></a>
    </div>
    <div class="nav-links" style="display: flex; gap: 32px;">
        <a href="index.php" style="color: #ffffff; text-decoration: none; font-size: 0.95rem; font-weight: 500; opacity: 0.85;">Home</a>
        <a href="#" style="color: #ffffff; text-decoration: none; font-size: 0.95rem; font-weight: 500; opacity: 0.85;">About</a>
        <a href="#" style="color: #ffffff; text-decoration: none; font-size: 0.95rem; font-weight: 500; opacity: 0.85;">Contact</a>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="admin.php" style="color: #ffffff; text-decoration: none; font-size: 0.95rem; font-weight: 600;">Console Deck</a>
        <?php endif; ?>
    </div>
    <div class="nav-auth-actions" style="display: flex; align-items: center; gap: 16px;">
        <?php if (isset($_SESSION["user_id"])): ?>
            <span style="color: #b8b8b8; font-size: 0.9rem;"><strong style="color: #ffffff;"><?php echo htmlspecialchars(
                $_SESSION["username"],
            ); ?></strong></span>
            <a href="logout.php" style="color: #ffffff; border: 1px solid rgba(255,255,255,0.25); text-decoration: none; padding: 8px 18px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: transparent; transition: background 0.2s;">Sign out</a>
        <?php else: ?>
            <a href="auth.php" style="color: #ffffff; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Login</a>
            <a href="auth.php" style="color: #0a0a0a; background: #ffffff; text-decoration: none; padding: 10px 22px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; transition: transform 0.1s;">Signup</a>
        <?php endif; ?>
    </div>
</nav>
