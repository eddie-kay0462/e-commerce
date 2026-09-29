<?php

// Action for the login form (views/login.php).
// Read the form -> ask the controller -> store the result in the session -> redirect.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CustomerController.php";

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/login.php');
}

$email = trim($_POST['customer_email'] ?? '');
$pass  = $_POST['customer_pass'] ?? '';

if ($email === '' || $pass === '') {
    $_SESSION['error'] = 'Please enter your email and password.';
    redirect('views/login.php');
}

$controller = new CustomerController();
$result = $controller->login($email, $pass);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect('views/login.php');
}

$customer = $result['customer'];

// New session id at the moment of login (blocks session fixation)
session_regenerate_id(true);

// Remember who this is. Every later page reads these to know
// who is logged in and whether they are an admin.
$_SESSION['customer_id']    = (int) $customer['customer_id'];
$_SESSION['customer_name']  = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role']      = (int) $customer['user_role'];

redirect('index.php');
