<?php
require_once 'includes/auth_check.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Register';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Sign up to start managing your tasks.</p>

        <div id="registerAlert" class="alert alert-error" style="display:none;"></div>

        <form id="registerForm" novalidate>
            <div class="form-group">
                <label for="regUsername">Username</label>
                <input type="text" id="regUsername" class="form-control" maxlength="50" required autofocus>
                <span class="error-msg" id="regUsernameError"></span>
            </div>
            <div class="form-group">
                <label for="regPassword">Password</label>
                <input type="password" id="regPassword" class="form-control" required>
                <span class="error-msg" id="regPasswordError"></span>
            </div>
            <div class="form-group">
                <label for="regConfirmPassword">Confirm Password</label>
                <input type="password" id="regConfirmPassword" class="form-control" required>
                <span class="error-msg" id="regConfirmError"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block" id="btnRegister">Create Account</button>
        </form>

        <p class="auth-link">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
