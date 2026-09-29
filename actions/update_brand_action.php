<?php

// Action for the "Edit Brand" form in views/admin/brand.php?edit_id=N.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/brand.php');
}

// brand_id comes from a hidden field - but hidden doesn't mean safe, a user
// can change it. FILTER_VALIDATE_INT with min_range 1 accepts only whole
// numbers >= 1, and returns false for anything else ("abc", "-3", "1.5").
$id   = filter_var($_POST['brand_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect('views/admin/brand.php');
}

if ($name === '' || mb_strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required (max 100 characters).';
    // Send them back to the edit form, not the add form
    redirect('views/admin/brand.php?edit_id=' . $id);
}

$controller = new ProductController();

// Make sure the brand actually exists before updating it
if (!$controller->getBrandById($id)) {
    $_SESSION['error'] = 'Brand not found.';
    redirect('views/admin/brand.php');
}

if ($controller->updateBrand($id, $name)) {
    $_SESSION['success'] = 'Brand updated.';
} else {
    $_SESSION['error'] = 'Could not update brand. Please try again.';
}

redirect('views/admin/brand.php');
