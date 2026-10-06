<?php

// Action for the "Add Product" form in views/admin/product.php.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Only admins may add products - checked here too, not just on the page.
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/product.php');
}

// Sanitise
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));

// Keep what the admin typed, so the form can be filled in again if
// something is wrong (nobody wants to retype a whole product)
$_SESSION['old_product'] = [
    'product_cat'      => $cat,
    'product_brand'    => $brand,
    'product_title'    => $title,
    'product_price'    => $_POST['product_price'] ?? '',
    'product_desc'     => $desc,
    'product_keywords' => $keywords,
];

$controller = new ProductController();

// Validate. The lengths match the columns in the products table.
// There are no foreign keys on the server, so we check ourselves that the
// category and brand really exist.
$error = null;
if ($cat === false || !$controller->getCategoryById($cat)) {
    $error = 'Please choose a category.';
} elseif ($brand === false || !$controller->getBrandById($brand)) {
    $error = 'Please choose a brand.';
} elseif ($title === '' || mb_strlen($title) > 200) {
    $error = 'Product title is required (max 200 characters).';
} elseif ($price === false || $price <= 0) {
    $error = 'Price must be a number greater than 0.';
} elseif (mb_strlen($desc) > 500) {
    $error = 'Description can be at most 500 characters.';
} elseif (mb_strlen($keywords) > 100) {
    $error = 'Keywords can be at most 100 characters.';
}

if ($error) {
    $_SESSION['error'] = $error;
    redirect('views/admin/product.php');
}

// Upload the image last, so we don't save a file for a product that
// fails validation. The image is optional.
[$image, $upload_error] = upload_product_image($_FILES['product_image'] ?? []);
if ($upload_error) {
    $_SESSION['error'] = $upload_error;
    redirect('views/admin/product.php');
}

if ($controller->addProduct($cat, $brand, $title, $price, $desc, $image, $keywords)) {
    unset($_SESSION['old_product']);
    $_SESSION['success'] = 'Product added.';
} else {
    delete_product_image($image);
    $_SESSION['error'] = 'Could not add product. Please try again.';
}

redirect('views/admin/product.php');
