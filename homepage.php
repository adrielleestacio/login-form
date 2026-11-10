<?php
session_start();

// Only signed-in users may see this page.
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'connect.php';

$stmt = $conn->prepare('SELECT firstName, lastName FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($user === null) {
    // The account was removed while this session was still alive.
    header('Location: logout.php');
    exit();
}

// Escape before output so a name containing HTML can't inject markup (XSS).
$fullName = htmlspecialchars($user['firstName'] . ' ' . $user['lastName'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
    <div style="text-align:center; padding:15%;">
      <p style="font-size:50px; font-weight:bold;">
        Hello <?= $fullName ?> :)
      </p>
      <a href="logout.php">Logout</a>
    </div>
</body>
</html>
