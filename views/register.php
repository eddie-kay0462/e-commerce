<?php
// View: customer registration form (Task 3).
// Flow: user fills this form -> js/validate.js checks it in the browser
// -> the browser POSTs it to actions/register_action.php -> controller
// -> model -> database -> the action redirects to My Account (or back
// here with $_SESSION['error'], which header.php displays).
require_once __DIR__ . '/../core/core.php';

// Already logged in? No need to register again.
if (is_logged_in()) {
	redirect('views/account/my_account.php');
}

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Ivory Coast', 'Togo', 'United Kingdom', 'United States', 'Other'];

$page_title = 'Register';
require __DIR__ . '/layout/header.php';
?>

<h1>Create an account</h1>

<!--
	action = where the form data is sent, method = POST (data goes in the
	request body, not the URL - important for passwords).
	novalidate turns off the browser's built-in checks so our own
	js/validate.js messages are shown instead.
	Each input's "name" is the key PHP reads from $_POST in the action.
	Each "*-error" span is where validate.js writes that field's error.
-->
<form id="register-form" action="../actions/register_action.php" method="POST" novalidate>
	<div class="field">
		<label for="customer_name">Full Name</label>
		<input type="text" id="customer_name" name="customer_name" maxlength="100">
		<span class="field-error" id="customer_name-error"></span>
	</div>

	<div class="field">
		<label for="customer_email">Email</label>
		<input type="email" id="customer_email" name="customer_email" maxlength="50">
		<span class="field-error" id="customer_email-error"></span>
	</div>

	<div class="field">
		<label for="customer_pass">Password</label>
		<input type="password" id="customer_pass" name="customer_pass">
		<small class="hint">At least 8 characters, with an uppercase letter, a lowercase letter, a number and a special character (e.g. ! @ # $).</small>
		<span class="field-error" id="customer_pass-error"></span>
	</div>

	<div class="field">
		<label for="confirm_pass">Confirm Password</label>
		<input type="password" id="confirm_pass" name="confirm_pass">
		<span class="field-error" id="confirm_pass-error"></span>
	</div>

	<div class="field">
		<label for="customer_country">Country</label>
		<select id="customer_country" name="customer_country">
			<option value="">-- Select country --</option>
			<?php foreach ($countries as $country): ?>
				<option value="<?php echo htmlspecialchars($country); ?>"><?php echo htmlspecialchars($country); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="field-error" id="customer_country-error"></span>
	</div>

	<div class="field">
		<label for="customer_city">City</label>
		<input type="text" id="customer_city" name="customer_city" maxlength="30">
		<span class="field-error" id="customer_city-error"></span>
	</div>

	<div class="field">
		<label for="customer_contact">Contact Number</label>
		<input type="tel" id="customer_contact" name="customer_contact" maxlength="15">
		<span class="field-error" id="customer_contact-error"></span>
	</div>

	<button type="submit" id="register-btn">Register</button>
</form>

<p>Already have an account? <a href="login.php">Log in</a></p>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
