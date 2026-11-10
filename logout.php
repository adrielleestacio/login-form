<?php
// session_destroy() only works on a started session — the original file
// skipped session_start(), so "Logout" never actually logged anyone out.
session_start();

$_SESSION = [];

// Remove the session cookie from the browser as well.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: index.php');
exit();
