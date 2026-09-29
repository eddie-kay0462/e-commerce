<?php
// Admin view: list every customer.
// require_admin() comes first, before any HTML - customer details are
// private, so only admins (user_role 1) may see this page.
require_once __DIR__ . '/../../core/core.php';
require_admin();

// Views get data from the Controller, never from the Model directly.
require_once __DIR__ . '/../../controllers/CustomerController.php';
$controller = new CustomerController();

// $customers is an array of rows, e.g. $customers[0]['customer_name']
$customers = $controller->selectAll();

$page_title = 'All Customers';
require __DIR__ . '/../layout/header.php';
?>

<h1>All Customers</h1>

<table class="data-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Email</th>
			<th>Country</th>
			<th>City</th>
			<th>Contact</th>
			<th>Role</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($customers as $customer): ?>
			<!--
				htmlspecialchars() converts special characters (like <, >, &)
				into safe HTML entities before printing them. This stops a
				malicious customer_name (or any field) from being run as
				HTML/JavaScript in the browser - this is called an XSS attack.
			-->
			<tr>
				<td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
				<td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
				<td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
				<td><?php echo htmlspecialchars($customer['customer_country']); ?></td>
				<td><?php echo htmlspecialchars($customer['customer_city']); ?></td>
				<td><?php echo htmlspecialchars($customer['customer_contact']); ?></td>
				<td><?php echo (int) $customer['user_role'] === 1 ? 'Admin' : 'Customer'; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php require __DIR__ . '/../layout/footer.php'; ?>
