<?php
// Public view: every product as a grid of cards, with search and
// category/brand filters. Anyone can see this page - no login needed.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
$controller = new ProductController();

// The search form uses GET, so the filters show in the URL
// (e.g. all_products.php?q=phone&cat=2) and the page can be bookmarked.
// Anything that isn't a valid id is treated as "no filter".
$keyword  = trim($_GET['q'] ?? '');
$cat_id   = filter_var($_GET['cat'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
$brand_id = filter_var($_GET['brand'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;

$products   = $controller->searchProducts($keyword, $cat_id, $brand_id);
$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();

$filtering = $keyword !== '' || $cat_id || $brand_id;

$page_title = 'Shop - Shoppn';
require __DIR__ . '/layout/header.php';
?>

<h1>Shop</h1>

<form class="filters" action="all_products.php" method="GET">
	<input type="search" name="q" placeholder="Search products..." aria-label="Search products"
		value="<?php echo htmlspecialchars($keyword); ?>">

	<select name="cat" aria-label="Category">
		<option value="">All categories</option>
		<?php foreach ($categories as $cat): ?>
			<option value="<?php echo (int) $cat['cat_id']; ?>" <?php if ($cat_id == $cat['cat_id']) echo 'selected'; ?>>
				<?php echo htmlspecialchars($cat['cat_name']); ?>
			</option>
		<?php endforeach; ?>
	</select>

	<select name="brand" aria-label="Brand">
		<option value="">All brands</option>
		<?php foreach ($brands as $brand): ?>
			<option value="<?php echo (int) $brand['brand_id']; ?>" <?php if ($brand_id == $brand['brand_id']) echo 'selected'; ?>>
				<?php echo htmlspecialchars($brand['brand_name']); ?>
			</option>
		<?php endforeach; ?>
	</select>

	<button type="submit">Search</button>
	<?php if ($filtering): ?>
		<a href="all_products.php">Clear</a>
	<?php endif; ?>
</form>

<?php if (empty($products)): ?>
	<p><?php echo $filtering ? 'No products match your search.' : 'No products yet.'; ?></p>
<?php else: ?>
	<p class="hint"><?php echo count($products); ?> product<?php echo count($products) === 1 ? '' : 's'; ?></p>

	<div class="product-grid">
		<?php foreach ($products as $product): ?>
			<!-- The whole card is a link to the single product page -->
			<a class="product-card" href="single_product.php?id=<?php echo (int) $product['product_id']; ?>">
				<?php if ($product['product_image']): ?>
					<img src="<?php echo BASE_URL . htmlspecialchars($product['product_image']); ?>"
						alt="<?php echo htmlspecialchars($product['product_title']); ?>">
				<?php else: ?>
					<div class="no-image">No image</div>
				<?php endif; ?>
				<div class="product-info">
					<span class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></span>
					<span class="hint"><?php echo htmlspecialchars($product['brand_name'] ?? ''); ?></span>
					<span class="product-price">GH₵ <?php echo number_format($product['product_price'], 2); ?></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>
