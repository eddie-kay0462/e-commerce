<?php

// Action for the "Edit Category" form in views/admin/category.php?edit_id=N.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/category.php');
}

// cat_id comes from a hidden field - but hidden doesn't mean safe, a user
// can change it. FILTER_VALIDATE_INT with min_range 1 accepts only whole
// numbers >= 1, and returns false for anything else ("abc", "-3", "1.5").
$id   = filter_var($_POST['cat_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid category.';
    redirect('views/admin/category.php');
}

if ($name === '' || mb_strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required (max 100 characters).';
    // Send them back to the edit form, not the add form
    redirect('views/admin/category.php?edit_id=' . $id);
}

$controller = new ProductController();

// Make sure the category actually exists before updating it
if (!$controller->getCategoryById($id)) {
    $_SESSION['error'] = 'Category not found.';
    redirect('views/admin/category.php');
}

if ($controller->updateCategory($id, $name)) {
    $_SESSION['success'] = 'Category updated.';
} else {
    $_SESSION['error'] = 'Could not update category. Please try again.';
}

redirect('views/admin/category.php');
