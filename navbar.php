<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <h2>🌐 SocialApp</h2>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="profile.php">Profile</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>