<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>

<nav class="site-header">
    <div class="nav-container">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="index.php" class="nav-brand">CourseMap</a>
            <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme"></button>
        </div>

        <div class="nav-actions">
            <?php if (isset($_SESSION["user_id"])): ?>
                <?php if (
                    isset($_SESSION["username"]) &&
                    $_SESSION["username"] === "Admin"
                ): ?>
                    <a href="admin.php" class="nav-link" style="color: var(--accent-primary); border: 1px solid var(--accent-light); background: var(--accent-light); border-radius: 20px; padding: 8px 16px;">
                        Console Deck
                    </a>
                <?php endif; ?>
                <span style="font-size: 0.9rem; color: var(--text-secondary);">
                    <strong style="color: var(--text-primary);"><?php echo htmlspecialchars(
                        $_SESSION["username"],
                    ); ?></strong>
                </span>
                <a onclick="return confirm('Are you sure you want to logout?')" href="logout.php" class="btn btn-outline btn-sm">
                    Sign out
                </a>
            <?php else: ?>
                <a href="auth.php" class="nav-link">Login</a>
                <a href="auth.php" class="btn btn-sm">Signup</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    const themeToggle = document.getElementById('themeToggle');
    const bodyElement = document.body;

    // Check for saved theme preference or default to dark mode
    const currentTheme = localStorage.getItem('theme') || 'dark';

    // Apply the theme on page load
    if (currentTheme === 'light') {
        bodyElement.classList.add('light-mode');
        themeToggle.classList.add('light');
    }

    // Toggle theme on button click
    themeToggle.addEventListener('click', () => {
        const isLightMode = bodyElement.classList.toggle('light-mode');
        themeToggle.classList.toggle('light');

        // Save preference
        localStorage.setItem('theme', isLightMode ? 'light' : 'dark');
    });
</script>
