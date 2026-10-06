<?php

// Action for the "Add Category" form in views/admin/category.php.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Security first: only admins may add categories. This must be checked here
// too, not just on the page - anyone could send a POST straight to this file.
require_admin();

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/category.php');
}

// Sanitise
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

// Validate (cat_name is VARCHAR(100) in the database)
if ($name === '' || mb_strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required (max 100 characters).';
    redirect('views/admin/category.php');
}

$controller = new ProductController();

if ($controller->addCategory($name)) {
    $_SESSION['success'] = 'Category added.';
} else {
    $_SESSION['error'] = 'Could not add category. Please try again.';
}

redirect('views/admin/category.php');
