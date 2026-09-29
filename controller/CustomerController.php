<?php

// Bring in the Customer model class
require_once "../classes/CustomerClass.php";

// The controller sits between the "outside world" (actions/functions/views)
// and the model (Customer). Its job is to receive plain data, pass it to
// the model, and hand back whatever the model returns. This keeps the
// model focused on the database, and keeps things like forms/JSON out of
// the model entirely.
class CustomerController
{
    // Holds the Customer instance this controller talks to.
    private $customer;

    // Runs automatically when `new CustomerController()` is called.
    // Creates one Customer instance (and therefore one database
    // connection, since Customer extends Database) for this controller
    // to reuse across its methods.
    public function __construct()
    {
        $this->customer = new Customer();
    }

    // Insert a new customer. This just forwards the data straight to the
    // model's insertCustomer() method - a controller method usually stays
    // this thin unless extra logic (validation, formatting, etc.) belongs
    // here instead of in the action file.
    public function insert($name, $email, $pass, $country, $city, $contact, $image, $role)
    {
        return $this->customer->insertCustomer($name, $email, $pass, $country, $city, $contact, $image, $role);
    }

    // Get the full list of customers from the model.
    public function selectAll()
    {
        return $this->customer->getAllCustomers();
    }

    // To keep building this app, add one method here for every new
    // Customer model method you create (e.g. update(), delete(), findByEmail()).
}
