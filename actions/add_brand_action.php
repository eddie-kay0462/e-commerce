<?php

// Action for the "Add Brand" form in views/admin/brand.php.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Security first: only admins may add brands. This must be checked here
// too, not just on the page - anyone could send a POST straight to this file.
require_admin();

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/brand.php');
}

// Sanitise
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

// Validate (brand_name is VARCHAR(100) in the database)
if ($name === '' || mb_strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required (max 100 characters).';
    redirect('views/admin/brand.php');
}

$controller = new ProductController();

if ($controller->addBrand($name)) {
    $_SESSION['success'] = 'Brand added.';
} else {
    $_SESSION['error'] = 'Could not add brand. Please try again.';
}

redirect('views/admin/brand.php');
