<?php

// This is an "action" file - the endpoint the browser's JavaScript sends
// the registration form to (see js/customer.js -> fetch("../actions/customer_register_action.php")).
// Its job is: read the incoming request, hand the data to the controller,
// and send a response back. It should not contain any SQL itself - that
// belongs in the model (classes/CustomerClass.php).
require_once "../controller/CustomerController.php";

// Tell the browser the response body will be JSON, not HTML
header("Content-Type: application/json");

// Read each form field from $_POST. The `?? ''` part means "if this key
// doesn't exist, use an empty string instead of causing a PHP warning".
$name    = $_POST['customer_name'] ?? '';
$email   = $_POST['customer_email'] ?? '';
$pass    = $_POST['customer_pass'] ?? '';
$country = $_POST['customer_country'] ?? '';
$city    = $_POST['customer_city'] ?? '';
$contact = $_POST['customer_contact'] ?? '';
$image   = $_POST['customer_image'] ?? null; // optional field, can be empty

// user_role isn't collected from the form - every new sign-up is a
// regular customer (role 2), so it's hardcoded here.
$role    = 2;

// Server-side validation. The JavaScript in js/customer.js already checks
// this before sending the request, but a server-side check is still
// required because JavaScript can be bypassed (disabled, or the request
// sent directly without using the form).
if ($name === '' || $email === '' || $pass === '' || $country === '' || $city === '' || $contact === '') {
    echo json_encode(["success" => false, "message" => "All required fields must be filled."]);
    exit;
}

// Never store plain-text passwords. password_hash() turns the password
// into a secure, one-way hash before it ever reaches the database.
$hashedPass = password_hash($pass, PASSWORD_DEFAULT);

// Create the controller (this also connects to the database, since
// CustomerController creates a Customer, which extends Database).
$controller = new CustomerController();

// Hand the data to the controller, which hands it to the model, which
// runs the INSERT query.
$result = $controller->insert($name, $email, $hashedPass, $country, $city, $contact, $image, $role);

// Send a JSON response back to the JavaScript that called this file,
// so it can show a success or failure message to the user.
if ($result) {
    echo json_encode(["success" => true, "message" => "Registration successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed."]);
}
