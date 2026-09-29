<?php

// Bring in the Database class so CustomerClass can extend it
require_once __DIR__ . "/../core/db_class.php";

// This is the "model" layer for the customer table. It only knows about
// the `customer` table and the SQL needed to read/write it - it has no
// idea about forms, HTML, sessions or redirects. That separation makes it
// reusable from anywhere (a controller, a script, a test, etc.).
//
// "extends Database" means CustomerClass automatically inherits the connection
// logic and the fetchAll()/fetchOne()/execute() helper methods from the
// Database class, without having to rewrite any of that here.
class CustomerClass extends Database
{
    // Task 3: is this email already registered? Returns true/false.
    public function emailExists($email)
    {
        // "?" is a placeholder - PDO fills it in safely with $email,
        // which prevents SQL injection.
        $row = $this->fetchOne(
            "SELECT customer_email FROM customer WHERE customer_email = ?",
            [$email]
        );

        // fetchOne() returns false when no row matched
        return $row !== false;
    }

    // Task 3: insert a new customer (this is what "registration" does).
    // $pass is the plain password - it is hashed here, so a plain-text
    // password can never reach the database.
    // Returns the new customer_id, or false if the insert failed.
    public function addCustomer($name, $email, $pass, $country, $city, $contact)
    {
        // password_hash() turns the password into a secure, one-way hash.
        // The same password gives a different hash every time (random salt),
        // and there is no way to turn the hash back into the password.
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        // customer_image is left out, so it becomes NULL.
        // user_role is left out, so it becomes the DB default: 2 (customer).
        $sql = "
            INSERT INTO customer (
                customer_name,
                customer_email,
                customer_pass,
                customer_country,
                customer_city,
                customer_contact
            ) VALUES (?, ?, ?, ?, ?, ?)
        ";

        // execute() comes from the Database class (see core/db_class.php)
        $ok = $this->execute($sql, [$name, $email, $hash, $country, $city, $contact]);

        // lastInsertId() is the AUTO_INCREMENT id MySQL just gave the new row
        return $ok ? (int) $this->getConnection()->lastInsertId() : false;
    }

    // Task 4: get one customer row by email, or false if there is none.
    public function getCustomerByEmail($email)
    {
        return $this->fetchOne(
            "SELECT * FROM customer WHERE customer_email = ?",
            [$email]
        );
    }

    // Task 4: check an email + password pair.
    // Returns the customer row on success, false on failure.
    public function login($email, $pass)
    {
        $row = $this->getCustomerByEmail($email);

        // password_verify() hashes the typed password the same way and
        // compares it with the stored hash. We never "decrypt" anything.
        if ($row && password_verify($pass, $row['customer_pass'])) {
            // The hash has done its job - don't pass it any further
            unset($row['customer_pass']);
            return $row;
        }

        return false;
    }

    // Get every customer in the table, newest first (used by views/admin/customers.php).
    // Note: customer_pass is deliberately left out of the SELECT so
    // password hashes are never sent to the views/pages that list customers.
    public function getAllCustomers()
    {
        $sql = "
            SELECT
                customer_id,
                customer_name,
                customer_email,
                customer_country,
                customer_city,
                customer_contact,
                customer_image,
                user_role
            FROM customer
            ORDER BY customer_id DESC
        ";

        return $this->fetchAll($sql);
    }
}
