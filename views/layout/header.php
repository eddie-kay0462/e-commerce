<?php
// Shared page header - included at the top of every view.
// The page that includes this must already have included core/core.php.
// Optional: set $page_title before including this file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($page_title ?? 'Shoppn'); ?></title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
<header class="site-header">
	<a class="logo" href="<?php echo BASE_URL; ?>index.php">Shoppn</a>

	<!-- Site links (left). Admin links only show for admins. -->
	<nav class="nav-main">
		<a href="<?php echo BASE_URL; ?>index.php">Home</a>
		<a href="<?php echo BASE_URL; ?>views/all_products.php">Shop</a>
		<?php if (is_admin()): ?>
			<a href="<?php echo BASE_URL; ?>views/admin/brand.php">Brands</a>
			<a href="<?php echo BASE_URL; ?>views/admin/category.php">Categories</a>
			<a href="<?php echo BASE_URL; ?>views/admin/product.php">Products</a>
			<a href="<?php echo BASE_URL; ?>views/admin/customers.php">Customers</a>
		<?php endif; ?>
	</nav>

	<!-- Account links (right). Changes depending on whether someone is logged in (Task 4) -->
	<nav class="nav-user">
		<?php if (is_logged_in()): ?>
			<!-- First name only, so a long full name doesn't crowd the bar -->
			<span class="welcome">Welcome, <?php echo htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]); ?></span>
			<a href="<?php echo BASE_URL; ?>views/account/my_account.php">My Account</a>
			<a class="btn-outline" href="<?php echo BASE_URL; ?>logout.php">Logout</a>
		<?php else: ?>
			<a href="<?php echo BASE_URL; ?>views/login.php">Login</a>
			<a class="btn-outline" href="<?php echo BASE_URL; ?>views/register.php">Register</a>
		<?php endif; ?>
	</nav>
</header>

<main class="container">
<?php
// "Flash" messages: an action stores a message in the session, redirects,
// and the next page shows it here exactly once, then deletes it.
// Doing this in the header means every page shows them automatically.
if (isset($_SESSION['error'])): ?>
	<p class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
	<?php unset($_SESSION['error']);
endif;

if (isset($_SESSION['success'])): ?>
	<p class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
	<?php unset($_SESSION['success']);
endif; ?>
