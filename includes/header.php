<?php
// Determine base path for includes
$basePath = '';
if (defined('IS_API')) {
    $basePath = '../';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Task Manager</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="<?php echo $basePath; ?>index.php" class="nav-brand">
                Task Manager
            </a>
            <?php if (isLoggedIn()): ?>
            <div class="nav-right">
                <span class="nav-user">Hi, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                <a href="<?php echo $basePath; ?>logout.php" class="btn btn-sm btn-outline">Logout</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container">
