<?php
require_once 'includes/auth_check.php';

// Destroy session and redirect
session_destroy();
header('Location: login.php');
exit;
