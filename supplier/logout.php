<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// Properly destroy session and redirect to login
logout(); // assume this clears session and cookies in includes/auth.php

header('Location: login.php'); // ✅ relative path
exit;
