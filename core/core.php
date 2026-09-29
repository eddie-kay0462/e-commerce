<?php

// ============================================================
// core.php
// ------------------------------------------------------------
// This file gets included at the top of every page and action
// (require_once __DIR__ . "/../core/core.php";). It's the place for
// anything that needs to happen on EVERY page load - not just
// database access (that's what core/db_class.php is for).
//
// Rule from the lab: core.php holds shared helpers only.
// No SQL, no HTML output, no business logic.
// ============================================================

// Start output buffering.
// header('Location: ...') redirects fail if any output was already
// sent to the browser. Buffering output here means pages further
// down the line can still redirect safely even after printing
// something.
ob_start();

// Start the session.
// session_start() must run before $_SESSION can be read/written
// anywhere else in the app. The if-check stops a "session already
// started" notice if this file is somehow included twice.
if (session_status() === PHP_SESSION_NONE) {
    // httponly: JavaScript cannot read the session cookie (helps against XSS)
    // samesite: the browser won't send the cookie on cross-site form posts
    // (on a real HTTPS server also add 'secure' => true)
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

date_default_timezone_set('Africa/Accra');

// The URL of the project folder, used to build links and redirects
// that work from any page, however deep in the folders it is.
// Change this if your project folder has a different name.
define('BASE_URL', '/ecomlab/');

// The database base class, so any file that includes core.php can
// also create Model classes.
require_once __DIR__ . '/db_class.php';

// Send the browser to another page and stop this script.
// $path is relative to the project folder, e.g. redirect('views/login.php').
// exit is important: without it PHP keeps running the rest of the page.
function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

// The visitor's IP address (used later for guest carts in Task 11).
function get_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// True if someone is logged in. Login stores customer_id in the
// session, so if it is there, the user is logged in.
function is_logged_in()
{
    return isset($_SESSION['customer_id']);
}

// True if the logged-in user is an admin (user_role 1 = admin, 2 = customer).
function is_admin()
{
    return is_logged_in() && (int) $_SESSION['user_role'] === 1;
}

// Put this at the very top of any page only logged-in users may see.
function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect('views/login.php');
    }
}

// Put this at the very top of every admin page, before any HTML.
function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to view that page.';
        redirect('index.php');
    }
}

// TODO: session timeout
// Track the time of the last request. If too much time has passed
// since then, log the user out automatically.

// TODO: detect session hijacking
// Store the user's IP address and browser (User-Agent) at login.
// On every page load, compare them to the current request - if
// they don't match, something is wrong, so log the user out.
