<?php

// This is an "action" file - the endpoint views/register.php submits to.
// Its job is: read the form, clean and check it, hand it to the
// controller, store the outcome in the session, and redirect.
// No SQL and no HTML in here.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CustomerController.php";

// Only accept form submissions. Someone typing this URL into the
// browser (a GET request) just gets sent back to the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/register.php');
}

// Sanitise: trim() removes spaces at the ends, strip_tags() removes any
// HTML tags someone might try to sneak in.
// The `?? ''` part means "if this field is missing, use an empty string".
// The password is NOT stripped - it gets hashed, and every character counts.
$name    = trim(strip_tags($_POST['customer_name'] ?? ''));
$email   = trim($_POST['customer_email'] ?? '');
$pass    = $_POST['customer_pass'] ?? '';
$confirm = $_POST['confirm_pass'] ?? '';
$country = trim(strip_tags($_POST['customer_country'] ?? ''));
$city    = trim(strip_tags($_POST['customer_city'] ?? ''));
$contact = trim($_POST['customer_contact'] ?? '');

// Server-side validation. js/validate.js already checks all of this in
// the browser, but JavaScript can be switched off or skipped entirely
// (e.g. by sending the request with a tool like curl), so the server
// must never trust that the browser checked anything.
// The max lengths match the column sizes in the customer table.
$error = null;

if ($name === '' || $email === '' || $pass === '' || $country === '' || $city === '' || $contact === '') {
    $error = 'All fields are required.';
} elseif (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $error = 'Name must be between 2 and 100 characters.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    $error = 'Please enter a valid email address (max 50 characters).';
} elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $pass)) {
    // Same pattern as passRegex in js/validate.js
    $error = 'Password must be at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';
} elseif ($pass !== $confirm) {
    $error = 'Passwords do not match.';
} elseif (mb_strlen($country) > 30 || mb_strlen($city) > 30) {
    $error = 'Country and city must be 30 characters or fewer.';
} elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    $error = 'Contact number must be 7-15 digits (+, - and spaces allowed).';
}

if ($error !== null) {
    $_SESSION['error'] = $error;
    redirect('views/register.php');
}

// Hand the clean data to the controller, which asks the model to check
// the email and insert the row.
$controller = new CustomerController();
$result = $controller->register([
    'name'    => $name,
    'email'   => $email,
    'pass'    => $pass,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact,
]);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect('views/register.php');
}

// Registered - log them straight in.
// session_regenerate_id() gives the user a brand-new session id now that
// they are logged in, so an id someone else may have seen before login
// becomes useless (this blocks "session fixation" attacks).
session_regenerate_id(true);
$_SESSION['customer_id']    = $result['customer_id'];
$_SESSION['customer_name']  = $name;
$_SESSION['customer_email'] = $email;
$_SESSION['user_role']      = 2; // every new sign-up is a regular customer
$_SESSION['success']        = 'Welcome! Your account has been created.';

redirect('views/account/my_account.php');
