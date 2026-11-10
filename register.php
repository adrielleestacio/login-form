<?php
// Handles both forms on index.php: sign up and sign in.
session_start();
require 'connect.php';

const PASSWORD_MIN_LENGTH = 8;

/** Save a message for index.php, then send the visitor back to the right form. */
function redirectWithMessage(string $message, string $form, string $type = 'error'): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message, 'form' => $form];
    header('Location: index.php');
    exit();
}

/** True for hashes written by the first version of this app, which used md5(). */
function isLegacyMd5Hash(string $hash): bool
{
    return (bool) preg_match('/^[a-f0-9]{32}$/i', $hash);
}

// ---------------------------------------------------------------- Sign up
if (isset($_POST['signUp'])) {
    $firstName = trim($_POST['fName'] ?? '');
    $lastName  = trim($_POST['lName'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
        redirectWithMessage('Please fill in all fields.', 'signup');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithMessage('Please enter a valid email address.', 'signup');
    }
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        redirectWithMessage('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.', 'signup');
    }

    // Prepared statements send the SQL and the values separately, so user
    // input can never change the query itself (no SQL injection).
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $alreadyRegistered = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($alreadyRegistered) {
        redirectWithMessage('That email address is already registered.', 'signup');
    }

    // password_hash() uses bcrypt with a random per-user salt. The raw
    // password is never stored, and the hash can't be reversed.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        'INSERT INTO users (firstName, lastName, email, password) VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('ssss', $firstName, $lastName, $email, $passwordHash);
    $stmt->execute();
    $stmt->close();

    redirectWithMessage('Account created — you can sign in now.', 'signin', 'success');
}

// ---------------------------------------------------------------- Sign in
if (isset($_POST['signIn'])) {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $isValid      = false;
    $needsRehash  = false;

    if ($user !== null) {
        if (password_verify($password, $user['password'])) {
            $isValid     = true;
            $needsRehash = password_needs_rehash($user['password'], PASSWORD_DEFAULT);
        } elseif (isLegacyMd5Hash($user['password']) && hash_equals($user['password'], md5($password))) {
            // Account created before hashing was fixed: accept this once,
            // then replace the md5 digest with a proper bcrypt hash below.
            $isValid     = true;
            $needsRehash = true;
        }
    }

    if (!$isValid) {
        // One message for both "unknown email" and "wrong password", so the
        // form doesn't reveal which emails have accounts.
        redirectWithMessage('Incorrect email or password.', 'signin');
    }

    if ($needsRehash) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $newHash, $user['id']);
        $stmt->execute();
        $stmt->close();
    }

    // A fresh session ID on login prevents session-fixation attacks.
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];

    header('Location: homepage.php');
    exit();
}

// Visiting this file directly has nothing to show.
header('Location: index.php');
exit();
