<?php
	// This file stores the database connection settings in one place.
	// Keeping credentials here (instead of inside db_class.php) means
	// you only have to update them in one spot if the database changes.

	// define() creates a global constant - a named value that cannot
	// be changed anywhere else in the app once it is set here.

	define("DATABASE", "shoppn"); // name of the database to connect to
	define("SERVER", "localhost"); // where the database server is running
	define("USERNAME", "root");    // MySQL username
	define("PASSWD", "");          // MySQL password (empty for local XAMPP by default)
?>
