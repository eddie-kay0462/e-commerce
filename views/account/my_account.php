<?php
// View: the logged-in customer's account page.
// require_login() runs first - if nobody is logged in, the visitor is
// redirected to the login page before any HTML is sent.
require_once __DIR__ . '/../../core/core.php';
require_login();

$page_title = 'My Account';
require __DIR__ . '/../layout/header.php';
?>

<h1>My Account</h1>

<table class="details">
	<tr><th>Name</th><td><?php echo htmlspecialchars($_SESSION['customer_name']); ?></td></tr>
	<tr><th>Email</th><td><?php echo htmlspecialchars($_SESSION['customer_email']); ?></td></tr>
	<tr><th>Account type</th><td><?php echo is_admin() ? 'Admin' : 'Customer'; ?></td></tr>
</table>

<?php require __DIR__ . '/../layout/footer.php'; ?>
