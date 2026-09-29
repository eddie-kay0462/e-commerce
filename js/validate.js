// Client-side form validation (Tasks 3 and 4).
//
// This runs in the browser BEFORE the form is sent. It gives the user
// instant feedback, but it is only for convenience: anyone can switch
// JavaScript off, so the action files in actions/ check everything again
// on the server.

// Regular expressions (patterns) from the lab's appendix
var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;   // something@something.something
var phoneRegex = /^[0-9+\-\s]{7,15}$/;           // 7-15 digits, +, - or spaces

// Strong password. Each (?=.*X) is a "lookahead": it checks that X appears
// somewhere, without using up any characters. All four must be true, and
// .{8,} then requires at least 8 characters in total.
//   (?=.*[a-z])        at least one lowercase letter
//   (?=.*[A-Z])        at least one uppercase letter
//   (?=.*\d)           at least one digit
//   (?=.*[^A-Za-z0-9]) at least one special character (anything not a letter/digit)
var passRegex  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

// Write a message into the <span id="FIELD-error"> next to a field
function showError(fieldId, message) {
	document.getElementById(fieldId + '-error').textContent = message;
}

// Clear every error message inside a form
function clearErrors(form) {
	var spans = form.querySelectorAll('.field-error');
	for (var i = 0; i < spans.length; i++) {
		spans[i].textContent = '';
	}
}

// Read a field's value with spaces trimmed off the ends
function val(fieldId) {
	return document.getElementById(fieldId).value.trim();
}

// Disable the button and change its text so the user can see something
// is happening and can't double-submit
function setLoading(button, text) {
	button.disabled = true;
	button.textContent = text;
}

// ---- Registration form (views/register.php) ----
var registerForm = document.getElementById('register-form');

if (registerForm) {
	// 'submit' fires when the user clicks Register or presses Enter
	registerForm.addEventListener('submit', function (e) {
		clearErrors(registerForm);
		var ok = true;

		if (val('customer_name').length < 2) {
			showError('customer_name', 'Please enter your full name.');
			ok = false;
		}
		if (!emailRegex.test(val('customer_email'))) {
			showError('customer_email', 'Please enter a valid email address.');
			ok = false;
		}
		// Password is not trimmed: spaces are allowed in passwords
		var pass = document.getElementById('customer_pass').value;
		if (!passRegex.test(pass)) {
			showError('customer_pass', 'Use 8+ characters with uppercase, lowercase, a number and a special character.');
			ok = false;
		}
		if (document.getElementById('confirm_pass').value !== pass) {
			showError('confirm_pass', 'Passwords do not match.');
			ok = false;
		}
		if (val('customer_country') === '') {
			showError('customer_country', 'Please choose your country.');
			ok = false;
		}
		if (val('customer_city') === '') {
			showError('customer_city', 'Please enter your city.');
			ok = false;
		}
		if (!phoneRegex.test(val('customer_contact'))) {
			showError('customer_contact', '7-15 digits (+, - and spaces allowed).');
			ok = false;
		}

		if (!ok) {
			// Stop the browser from sending the form
			e.preventDefault();
			return;
		}

		setLoading(document.getElementById('register-btn'), 'Registering...');
	});
}

// ---- Login form (views/login.php) ----
var loginForm = document.getElementById('login-form');

if (loginForm) {
	loginForm.addEventListener('submit', function (e) {
		clearErrors(loginForm);
		var ok = true;

		if (!emailRegex.test(val('customer_email'))) {
			showError('customer_email', 'Please enter a valid email address.');
			ok = false;
		}
		if (document.getElementById('customer_pass').value === '') {
			showError('customer_pass', 'Please enter your password.');
			ok = false;
		}

		if (!ok) {
			e.preventDefault();
			return;
		}

		setLoading(document.getElementById('login-btn'), 'Logging in...');
	});
}
