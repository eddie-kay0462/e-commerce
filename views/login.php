<?php
// View: login form (Task 4).
// Flow: form -> js/validate.js -> actions/login_action.php -> controller
// -> model (password_verify) -> session is filled -> redirect home.
require_once __DIR__ . '/../core/core.php';

if (is_logged_in()) {
	redirect('index.php');
}

$page_title = 'Login';
require __DIR__ . '/layout/header.php';
?>

<h1>Log in</h1>

<form id="login-form" action="../actions/login_action.php" method="POST" novalidate>
	<div class="field">
		<label for="customer_email">Email</label>
		<input type="email" id="customer_email" name="customer_email" maxlength="50">
		<span class="field-error" id="customer_email-error"></span>
	</div>

	<div class="field">
		<label for="customer_pass">Password</label>
		<input type="password" id="customer_pass" name="customer_pass">
		<span class="field-error" id="customer_pass-error"></span>
	</div>

	<button type="submit" id="login-btn">Log in</button>
</form>

<p>Don't have an account? <a href="register.php">Register</a></p>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
