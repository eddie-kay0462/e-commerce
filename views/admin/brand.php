<?php
// Admin view: add a brand + list all brands (Task 5), edit a brand (Task 6).
// require_admin() runs before ANY HTML is output.
require_once __DIR__ . '/../../core/core.php';
require_admin();

// Views get data from the Controller, never from the Model directly.
require_once __DIR__ . '/../../controllers/ProductController.php';
$controller = new ProductController();
$brands = $controller->getAllBrands();

// Task 6: edit mode. If the URL has ?edit_id=N, load that brand so the
// form can be pre-filled. $edit_brand stays null in normal "add" mode.
$edit_brand = null;
if (isset($_GET['edit_id'])) {
	$edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
	$edit_brand = $edit_id ? $controller->getBrandById($edit_id) : false;

	if (!$edit_brand) {
		$_SESSION['error'] = 'Brand not found.';
		redirect('views/admin/brand.php');
	}
}

$page_title = 'Brands';
// header.php shows $_SESSION['success'] / $_SESSION['error']
require __DIR__ . '/../layout/header.php';
?>

<h1>Brands</h1>

<?php if ($edit_brand): ?>
	<!-- Edit mode: form posts to the UPDATE action, pre-filled with the current name -->
	<h2>Edit brand</h2>
	<form action="../../actions/update_brand_action.php" method="POST">
		<!-- Hidden field: tells the action WHICH brand to update -->
		<input type="hidden" name="brand_id" value="<?php echo (int) $edit_brand['brand_id']; ?>">
		<div class="field">
			<label for="brand_name">Brand name</label>
			<input type="text" id="brand_name" name="brand_name" maxlength="100" required
				value="<?php echo htmlspecialchars($edit_brand['brand_name']); ?>">
		</div>
		<button type="submit">Update Brand</button>
		<a href="brand.php">Cancel</a>
	</form>
<?php else: ?>
	<!-- Add mode (Task 5) -->
	<form action="../../actions/add_brand_action.php" method="POST">
		<div class="field">
			<label for="brand_name">Brand name</label>
			<input type="text" id="brand_name" name="brand_name" maxlength="100" required>
		</div>
		<button type="submit">Add Brand</button>
	</form>
<?php endif; ?>

<h2>All brands</h2>

<?php if (empty($brands)): ?>
	<p>No brands yet.</p>
<?php else: ?>
	<table class="data-table">
		<thead>
			<tr><th>ID</th><th>Name</th><th></th></tr>
		</thead>
		<tbody>
			<?php foreach ($brands as $brand): ?>
				<tr>
					<td><?php echo (int) $brand['brand_id']; ?></td>
					<td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
					<!-- Clicking Edit reloads this page in edit mode (Task 6) -->
					<td><a href="brand.php?edit_id=<?php echo (int) $brand['brand_id']; ?>">Edit</a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
