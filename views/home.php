<?php
// Home page view. Loaded by index.php, which has already included core.php.
$page_title = 'Shoppn - Home';
require __DIR__ . '/layout/header.php';
?>

<h1>Welcome to Shoppn</h1>
<p><a href="all_products.php">Browse all products &rarr;</a></p>

<?php require __DIR__ . '/layout/footer.php'; ?>
