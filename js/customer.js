// This function runs when the "Register" button on view/register.php is
// clicked (see the button's onclick="registerCustomer()" attribute).
// It validates the form in the browser, then sends the data to the
// server-side action file, which does the actual database insert.
function registerCustomer() {
	// Read and trim() each field's value so accidental leading/trailing
	// spaces don't count as valid input.
	var name = document.getElementById("customer_name").value.trim();
	var email = document.getElementById("customer_email").value.trim();
	var pass = document.getElementById("customer_pass").value.trim();
	var country = document.getElementById("customer_country").value.trim();
	var city = document.getElementById("customer_city").value.trim();
	var contact = document.getElementById("customer_contact").value.trim();

	// The <p id="formMessage"> element where feedback is shown to the user
	var messageEl = document.getElementById("formMessage");

	// A simple regular expression to check for a basic "something@something.something" shape
	var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	// Client-side validation: stop here if any required field is empty.
	// This is just for a fast, friendly response in the browser - the
	// server (actions/customer_register_action.php) validates again too,
	// since client-side checks can always be bypassed.
	if (!name || !email || !pass || !country || !city || !contact) {
		messageEl.textContent = "Please fill in all required fields.";
		return;
	}

	// Stop here if the email doesn't look valid
	if (!emailPattern.test(email)) {
		messageEl.textContent = "Please enter a valid email address.";
		return;
	}

	// FormData automatically collects every input's name="" and value
	// from the form, so we don't have to build the request body by hand.
	var formData = new FormData(document.getElementById("registerForm"));

	// Send the form data to the action file using a POST request.
	// fetch() returns a Promise, so .then() runs once each step finishes.
	fetch("../actions/customer_register_action.php", {
		method: "POST",
		body: formData
	})
		.then(function (response) {
			// Parse the JSON response the action file sent back
			return response.json();
		})
		.then(function (data) {
			// Show whatever message the server sent (success or failure)
			messageEl.textContent = data.message;

			// If it worked, clear the form so it's ready for another entry
			if (data.success) {
				document.getElementById("registerForm").reset();
			}
		})
		.catch(function () {
			// Runs if the request itself failed (e.g. network/server error)
			messageEl.textContent = "Something went wrong. Please try again.";
		});
}

// To keep building this app, add one function here for each form/action
// pair you create (e.g. a future updateCustomer() or deleteCustomer()).
