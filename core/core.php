<?php

// ============================================================
// core.php
// ------------------------------------------------------------
// This file gets included at the top of every protected page in
// an app (require_once "../core/core.php";). It's the place for
// anything that needs to happen on EVERY page load - not just
// database access (that's what core/db_class.php is for).
//
// Below is a checklist of what this file is typically responsible
// for. Build these out one at a time as the app needs them.
// ============================================================

// TODO: start output buffering (ob_start())
// header('Location: ...') redirects fail if any output was already
// sent to the browser. Buffering output here means pages further
// down the line can still redirect safely even after printing
// something.

// TODO: start and secure the session
// - session_start() must run before $_SESSION can be read/written
//   anywhere else in the app
// - on a real (HTTPS) server, harden the session cookie:
//   session.cookie_secure, session.cookie_httponly, session.cookie_samesite

// TODO: check for login
// A function that checks if a "logged in" session value is set.
// If not, remember the page the user was trying to reach, then
// redirect to the login page and stop the rest of the script from running.

// TODO: get the logged-in user's id
// A small getter so pages don't touch $_SESSION directly - they
// just call something like core_get_user_id().

// TODO: get the logged-in user's role
// Same idea as above, for role (e.g. admin, customer, staff) so
// pages can decide what to show based on who's looking.

// TODO: session timeout
// Track the time of the last request. If too much time has passed
// since then, log the user out automatically.

// TODO: detect session hijacking
// Store the user's IP address and browser (User-Agent) at login.
// On every page load, compare them to the current request - if
// they don't match, something is wrong, so log the user out.

// TODO: secure logout
// A function that clears all session data, deletes the session
// cookie, destroys the session, and starts a fresh one - used by
// both a manual "log out" click and the automatic checks above.

// TODO: actually run the session check(s) above
// Whatever function ties this all together (e.g. sessionSecurity())
// should be called here, so simply including this file is enough
// to protect a page - no extra function calls needed on every page.

?>
