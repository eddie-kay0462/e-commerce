<?php
// Admin view: add a category + list all categories, edit a category.
// require_admin() runs before ANY HTML is output.
require_once __DIR__ . '/../../core/core.php';
require_admin();

// Views get data from the Controller, never from the Model directly.
require_once __DIR__ . '/../../controllers/ProductController.php';
$controller = new ProductController();
$categories = $controller->getAllCategories();

// Edit mode. If the URL has ?edit_id=N, load that category so the
// form can be pre-filled. $edit_category stays null in normal "add" mode.
$edit_category = null;
if (isset($_GET['edit_id'])) {
	$edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
	$edit_category = $edit_id ? $controller->getCategoryById($edit_id) : false;

	if (!$edit_category) {
		$_SESSION['error'] = 'Category not found.';
		redirect('views/admin/category.php');
	}
}

$page_title = 'Categories';
// header.php shows $_SESSION['success'] / $_SESSION['error']
require __DIR__ . '/../layout/header.php';
?>

<h1>Categories</h1>

<?php if ($edit_category): ?>
	<!-- Edit mode: form posts to the UPDATE action, pre-filled with the current name -->
	<h2>Edit category</h2>
	<form action="../../actions/update_category_action.php" method="POST">
		<!-- Hidden field: tells the action WHICH category to update -->
		<input type="hidden" name="cat_id" value="<?php echo (int) $edit_category['cat_id']; ?>">
		<div class="field">
			<label for="cat_name">Category name</label>
			<input type="text" id="cat_name" name="cat_name" maxlength="100" required
				value="<?php echo htmlspecialchars($edit_category['cat_name']); ?>">
		</div>
		<button type="submit">Update Category</button>
		<a href="category.php">Cancel</a>
	</form>
<?php else: ?>
	<!-- Add mode -->
	<form action="../../actions/add_category_action.php" method="POST">
		<div class="field">
			<label for="cat_name">Category name</label>
			<input type="text" id="cat_name" name="cat_name" maxlength="100" required>
		</div>
		<button type="submit">Add Category</button>
	</form>
<?php endif; ?>

<h2>All categories</h2>

<?php if (empty($categories)): ?>
	<p>No categories yet.</p>
<?php else: ?>
	<table class="data-table">
		<thead>
			<tr><th>ID</th><th>Name</th><th></th></tr>
		</thead>
		<tbody>
			<?php foreach ($categories as $category): ?>
				<tr>
					<td><?php echo (int) $category['cat_id']; ?></td>
					<td><?php echo htmlspecialchars($category['cat_name']); ?></td>
					<!-- Clicking Edit reloads this page in edit mode -->
					<td><a href="category.php?edit_id=<?php echo (int) $category['cat_id']; ?>">Edit</a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
