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

// BASE_URL = the web address of the project folder, used to build links
// and redirects that work from any page, however deep in the folders it is.
// It is worked out automatically, so the same code works on your laptop
// (/ecomlab/) and on the server (/~faculty/e-commerce-labs/shoppn/).
//
// How: compare the running script's path on disk with its path in the URL.
//   on disk:  /home/faculty/public_html/e-commerce-labs/shoppn/views/login.php
//   project:  /home/faculty/public_html/e-commerce-labs/shoppn
//   so the part inside the project is:                        /views/login.php
//   URL path: /~faculty/e-commerce-labs/shoppn/views/login.php
//   cut that same part off the URL  -> /~faculty/e-commerce-labs/shoppn/
$project_dir = realpath(__DIR__ . '/..');                          // folder above core/
$script_file = realpath($_SERVER['SCRIPT_FILENAME']);              // e.g. .../views/login.php
$inside      = substr($script_file, strlen($project_dir));         // "/views/login.php"
$base        = substr($_SERVER['SCRIPT_NAME'], 0, -strlen($inside)); // "/~faculty/.../shoppn"
define('BASE_URL', $base . '/');

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

// Save an uploaded product image into images/products/.
// $file is one entry of $_FILES, e.g. $_FILES['product_image'].
// Returns [path, error]:
//   [null, null]                           no file was chosen (that's allowed)
//   ['images/products/product_ab12.jpg', null]  saved - store this path in the database
//   [null, 'message']                      something was wrong with the file
function upload_product_image($file)
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'The image could not be uploaded. Please try again.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'The image must be 2MB or smaller.'];
    }

    // Check what the file REALLY is by reading its contents. The file name
    // and the browser's "type" can be faked, e.g. evil.php renamed to cat.jpg.
    // We also pick the extension ourselves, so a .php file can never be saved.
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return [null, 'The image must be a JPG, PNG, GIF or WEBP file.'];
    }

    $folder = __DIR__ . '/../images/products';
    if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
        return [null, 'The images/products folder is missing and could not be created.'];
    }

    // A random name, so two uploads called "photo.jpg" don't overwrite each other
    $name = 'product_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];

    if (!move_uploaded_file($file['tmp_name'], $folder . '/' . $name)) {
        return [null, 'The image could not be saved on the server.'];
    }

    return ['images/products/' . $name, null];
}

// Delete a product image that is no longer used (e.g. after it was replaced).
// Only files inside images/products/ can be deleted.
function delete_product_image($path)
{
    if ($path && strpos($path, 'images/products/') === 0) {
        $full = __DIR__ . '/../' . $path;
        if (is_file($full)) {
            unlink($full);
        }
    }
}

// TODO: session timeout
// Track the time of the last request. If too much time has passed
// since then, log the user out automatically.

// TODO: detect session hijacking
// Store the user's IP address and browser (User-Agent) at login.
// On every page load, compare them to the current request - if
// they don't match, something is wrong, so log the user out.
