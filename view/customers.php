<?php
// This is the "view" for listing every customer.
// Flow: this page includes the functions file -> calls
// getAllCustomersList() -> which calls the controller -> which calls
// the model -> which runs a SELECT and returns the rows as an array.
require_once "../functions/customer_functions.php";

// $customers is now an array of associative arrays, one per customer row,
// e.g. $customers[0]['customer_name']
$customers = getAllCustomersList();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>All Customers</title>
</head>
<body>
	<h1>All Customers</h1>

	<!-- Simple navigation so you can move between the pages of the app -->
	<nav>
		<a href="../index.php">Home</a> |
		<a href="register.php">Register Customer</a>
	</nav>

	<table border="1" cellpadding="5" cellspacing="0">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Email</th>
				<th>Country</th>
				<th>City</th>
				<th>Contact</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($customers as $customer) { ?>
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
				</tr>
			<?php } ?>
		</tbody>
	</table>
</body>
</html>
