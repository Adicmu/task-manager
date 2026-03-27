<?php
require_once 'includes/auth_check.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Login';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-title">Sign In</h1>
        <p class="auth-subtitle">Welcome back! Log in to manage your tasks.</p>

        <div id="loginAlert" class="alert alert-error" style="display:none;"></div>

        <form id="loginForm" novalidate>
            <div class="form-group">
                <label for="loginUsername">Username</label>
                <input type="text" id="loginUsername" class="form-control" required autofocus>
                <span class="error-msg" id="loginUsernameError"></span>
            </div>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" class="form-control" required>
                <span class="error-msg" id="loginPasswordError"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block" id="btnLogin">Sign In</button>
        </form>

        <p class="auth-link">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
