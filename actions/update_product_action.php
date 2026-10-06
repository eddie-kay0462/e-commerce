<?php

// Action for the "Edit Product" form in views/admin/product.php?edit_id=N.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/product.php');
}

// product_id comes from a hidden field, which a user can change - validate it
$id = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    $_SESSION['error'] = 'Invalid product.';
    redirect('views/admin/product.php');
}

$controller = new ProductController();

// Make sure the product exists, and get its current image
$product = $controller->getProductById($id);
if (!$product) {
    $_SESSION['error'] = 'Product not found.';
    redirect('views/admin/product.php');
}

// Sanitise
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));

// Validate (same rules as adding)
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
    // Back to the edit form, not the add form
    redirect('views/admin/product.php?edit_id=' . $id);
}

// A new image is optional when editing: no file chosen = keep the old one
[$new_image, $upload_error] = upload_product_image($_FILES['product_image'] ?? []);
if ($upload_error) {
    $_SESSION['error'] = $upload_error;
    redirect('views/admin/product.php?edit_id=' . $id);
}
$image = $new_image ?? $product['product_image'];

if ($controller->updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords)) {
    // The new image is saved, so the old file is no longer needed
    if ($new_image) {
        delete_product_image($product['product_image']);
    }
    $_SESSION['success'] = 'Product updated.';
} else {
    delete_product_image($new_image);
    $_SESSION['error'] = 'Could not update product. Please try again.';
}

redirect('views/admin/product.php');
