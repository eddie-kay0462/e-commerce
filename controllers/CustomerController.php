<?php

// Bring in the Customer model class
require_once __DIR__ . "/../classes/CustomerClass.php";

// The controller sits between the "outside world" (actions and views)
// and the model (CustomerClass). Its job is to receive plain data, ask the
// model to do the database work, and hand back a clear result.
// No SQL, no HTML, no $_POST, no header() redirects in here.
class CustomerController
{
    // Holds the CustomerClass instance this controller talks to.
    private $customer;

    // Runs automatically when `new CustomerController()` is called.
    // Creates one CustomerClass instance (and therefore one database
    // connection, since CustomerClass extends Database).
    public function __construct()
    {
        $this->customer = new CustomerClass();
    }

    // Task 3: register a new customer.
    // $data is an array of already-cleaned form values.
    // Returns ['success' => true, 'customer_id' => N]
    //      or ['success' => false, 'error' => '...'].
    public function register($data)
    {
        // Business rule: one account per email address
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $id = $this->customer->addCustomer(
            $data['name'],
            $data['email'],
            $data['pass'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if ($id === false) {
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        return ['success' => true, 'customer_id' => $id];
    }

    // Task 4: log a customer in.
    // Returns ['success' => true, 'customer' => row]
    //      or ['success' => false, 'error' => '...'].
    public function login($email, $pass)
    {
        $customer = $this->customer->login($email, $pass);

        if ($customer === false) {
            // Same message whether the email or the password was wrong,
            // so attackers can't use the login form to discover which
            // emails have accounts.
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        return ['success' => true, 'customer' => $customer];
    }

    // Get the full list of customers from the model.
    public function selectAll()
    {
        return $this->customer->getAllCustomers();
    }
}
