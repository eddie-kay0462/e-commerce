<?php
// Public view: one product's full details, e.g. single_product.php?id=5
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
$controller = new ProductController();

// The id comes from the URL, so anyone can type anything - validate it
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$product = $id ? $controller->getProductById($id) : false;

if (!$product) {
	$_SESSION['error'] = 'Product not found.';
	redirect('views/all_products.php');
}

$page_title = $product['product_title'] . ' - Shoppn';
require __DIR__ . '/layout/header.php';
?>

<p><a href="all_products.php">&larr; Back to shop</a></p>

<div class="product-detail">
	<?php if ($product['product_image']): ?>
		<img src="<?php echo BASE_URL . htmlspecialchars($product['product_image']); ?>"
			alt="<?php echo htmlspecialchars($product['product_title']); ?>">
	<?php else: ?>
		<div class="no-image">No image</div>
	<?php endif; ?>

	<div>
		<h1><?php echo htmlspecialchars($product['product_title']); ?></h1>
		<p class="product-price">GH₵ <?php echo number_format($product['product_price'], 2); ?></p>

		<table class="details">
			<tr>
				<th>Category</th>
				<!-- Links back to the shop, filtered to this category / brand -->
				<td><a href="all_products.php?cat=<?php echo (int) $product['product_cat']; ?>"><?php echo htmlspecialchars($product['cat_name'] ?? '-'); ?></a></td>
			</tr>
			<tr>
				<th>Brand</th>
				<td><a href="all_products.php?brand=<?php echo (int) $product['product_brand']; ?>"><?php echo htmlspecialchars($product['brand_name'] ?? '-'); ?></a></td>
			</tr>
		</table>

		<?php if ($product['product_desc']): ?>
			<!-- nl2br keeps the line breaks the admin typed in the description -->
			<p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
