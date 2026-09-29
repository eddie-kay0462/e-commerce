<?php

// Log the user out: wipe the session completely, then go home.
require_once __DIR__ . "/core/core.php";

// 1. Empty the session data
$_SESSION = [];

// 2. Tell the browser to delete the session cookie
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

// 3. Destroy the session file on the server
session_destroy();

header('Location: ' . BASE_URL . 'index.php');
exit;
