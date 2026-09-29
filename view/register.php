<!--
	This is the "view" for registering a new customer.
	Flow: user fills this form -> clicks Register -> js/customer.js
	validates it and sends it to actions/customer_register_action.php
	-> which calls the controller -> which calls the model -> which
	inserts the row into the database.
-->
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register Customer</title>
</head>
<body>
	<h1>Customer Registration</h1>

	<!-- Simple navigation so you can move between the pages of the app -->
	<nav>
		<a href="../index.php">Home</a> |
		<a href="customers.php">View All Customers</a>
	</nav>

	<!--
		Each input's "name" attribute matches a column in the `customer`
		table (see classes/CustomerClass.php) and is what PHP reads via
		$_POST in actions/customer_register_action.php. The "id" attribute
		is what js/customer.js uses to read the value in the browser.
	-->
	<form id="registerForm">
		<div>
			<label>Name</label>
			<input type="text" name="customer_name" id="customer_name">
		</div>
		<div>
			<label>Email</label>
			<input type="text" name="customer_email" id="customer_email">
		</div>
		<div>
			<label>Password</label>
			<input type="password" name="customer_pass" id="customer_pass">
		</div>
		<div>
			<label>Country</label>
			<input type="text" name="customer_country" id="customer_country">
		</div>
		<div>
			<label>City</label>
			<input type="text" name="customer_city" id="customer_city">
		</div>
		<div>
			<label>Contact</label>
			<input type="text" name="customer_contact" id="customer_contact">
		</div>
		<div>
			<label>Image (optional)</label>
			<input type="text" name="customer_image" id="customer_image">
		</div>
		<div>
			<!--
				type="button" (not "submit") stops the browser from
				trying to reload the page. Instead, clicking it calls
				the registerCustomer() function defined in js/customer.js.
			-->
			<button type="button" onclick="registerCustomer()">Register</button>
		</div>
	</form>

	<!-- Validation/success/error messages get written into here by customer.js -->
	<p id="formMessage"></p>

	<!-- Loads the shared JavaScript file that contains registerCustomer() -->
	<script src="../js/customer.js"></script>
</body>
</html>
