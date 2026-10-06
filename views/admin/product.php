<?php
// Admin view: add a product + list all products, edit a product.
// require_admin() runs before ANY HTML is output.
require_once __DIR__ . '/../../core/core.php';
require_admin();

require_once __DIR__ . '/../../controllers/ProductController.php';
$controller = new ProductController();
$products   = $controller->getAllProducts();
// For the Category and Brand dropdowns
$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();

// Edit mode: ?edit_id=N loads that product so the form can be pre-filled
$edit_product = null;
if (isset($_GET['edit_id'])) {
	$edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
	$edit_product = $edit_id ? $controller->getProductById($edit_id) : false;

	if (!$edit_product) {
		$_SESSION['error'] = 'Product not found.';
		redirect('views/admin/product.php');
	}
}

// $form = the values to show in the form:
//   edit mode -> the product from the database
//   add mode  -> what was typed last time if adding failed, otherwise empty
$form = $edit_product ?: ($_SESSION['old_product'] ?? []);
unset($_SESSION['old_product']);

$page_title = 'Products';
require __DIR__ . '/../layout/header.php';
?>

<h1>Products</h1>

<?php if (empty($categories) || empty($brands)): ?>
	<!-- A product needs a category and a brand, so those must exist first -->
	<p class="alert alert-error">
		Add at least one <a href="category.php">category</a> and one
		<a href="brand.php">brand</a> before adding products.
	</p>
<?php else: ?>
	<h2><?php echo $edit_product ? 'Edit product' : 'Add product'; ?></h2>

	<!--
		enctype="multipart/form-data" is required for file uploads.
		Without it the browser sends only the file NAME, not the file.
	-->
	<form id="product-form" method="POST" enctype="multipart/form-data"
		action="../../actions/<?php echo $edit_product ? 'update_product_action.php' : 'add_product_action.php'; ?>">

		<?php if ($edit_product): ?>
			<input type="hidden" name="product_id" value="<?php echo (int) $edit_product['product_id']; ?>">
		<?php endif; ?>

		<div class="field">
			<label for="product_cat">Category</label>
			<select id="product_cat" name="product_cat" required>
				<option value="">-- Choose a category --</option>
				<?php foreach ($categories as $cat): ?>
					<option value="<?php echo (int) $cat['cat_id']; ?>"
						<?php if (($form['product_cat'] ?? '') == $cat['cat_id']) echo 'selected'; ?>>
						<?php echo htmlspecialchars($cat['cat_name']); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="field-error" id="product_cat-error"></span>
		</div>

		<div class="field">
			<label for="product_brand">Brand</label>
			<select id="product_brand" name="product_brand" required>
				<option value="">-- Choose a brand --</option>
				<?php foreach ($brands as $brand): ?>
					<option value="<?php echo (int) $brand['brand_id']; ?>"
						<?php if (($form['product_brand'] ?? '') == $brand['brand_id']) echo 'selected'; ?>>
						<?php echo htmlspecialchars($brand['brand_name']); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="field-error" id="product_brand-error"></span>
		</div>

		<div class="field">
			<label for="product_title">Title</label>
			<input type="text" id="product_title" name="product_title" maxlength="200" required
				value="<?php echo htmlspecialchars($form['product_title'] ?? ''); ?>">
			<span class="field-error" id="product_title-error"></span>
		</div>

		<div class="field">
			<label for="product_price">Price (GH₵)</label>
			<input type="number" id="product_price" name="product_price" min="0.01" step="0.01" required
				value="<?php echo htmlspecialchars($form['product_price'] ?? ''); ?>">
			<span class="field-error" id="product_price-error"></span>
		</div>

		<div class="field">
			<label for="product_desc">Description</label>
			<textarea id="product_desc" name="product_desc" maxlength="500" rows="4"><?php
				echo htmlspecialchars($form['product_desc'] ?? '');
			?></textarea>
			<span class="field-error" id="product_desc-error"></span>
		</div>

		<div class="field">
			<label for="product_image">Image</label>
			<?php if (!empty($edit_product['product_image'])): ?>
				<img class="thumb" src="<?php echo BASE_URL . htmlspecialchars($edit_product['product_image']); ?>" alt="Current image">
				<span class="hint">Choose a new file only if you want to replace this image.</span>
			<?php endif; ?>
			<input type="file" id="product_image" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp">
			<span class="hint">JPG, PNG, GIF or WEBP, max 2MB.</span>
			<span class="field-error" id="product_image-error"></span>
		</div>

		<div class="field">
			<label for="product_keywords">Keywords</label>
			<input type="text" id="product_keywords" name="product_keywords" maxlength="100"
				value="<?php echo htmlspecialchars($form['product_keywords'] ?? ''); ?>">
			<span class="hint">Words customers might search for, e.g. "phone, smartphone, android".</span>
			<span class="field-error" id="product_keywords-error"></span>
		</div>

		<button type="submit" id="product-btn"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
		<?php if ($edit_product): ?>
			<a href="product.php">Cancel</a>
		<?php endif; ?>
	</form>
<?php endif; ?>

<h2>All products</h2>

<?php if (empty($products)): ?>
	<p>No products yet.</p>
<?php else: ?>
	<table class="data-table">
		<thead>
			<tr><th>Image</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th></th></tr>
		</thead>
		<tbody>
			<?php foreach ($products as $product): ?>
				<tr>
					<td>
						<?php if ($product['product_image']): ?>
							<img class="thumb" src="<?php echo BASE_URL . htmlspecialchars($product['product_image']); ?>" alt="">
						<?php endif; ?>
					</td>
					<td><?php echo htmlspecialchars($product['product_title']); ?></td>
					<!-- ?? shows a dash if the category/brand was not found -->
					<td><?php echo htmlspecialchars($product['cat_name'] ?? '-'); ?></td>
					<td><?php echo htmlspecialchars($product['brand_name'] ?? '-'); ?></td>
					<td>GH₵ <?php echo number_format($product['product_price'], 2); ?></td>
					<td><a href="product.php?edit_id=<?php echo (int) $product['product_id']; ?>">Edit</a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<script src="../../js/validate.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
